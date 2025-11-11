/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
({
    plugins: ['Stage2CssLoader', 'Dashlet'],

    /**
     * Classes that are responsible of COG icon on the dashlet.
     */
    cogIconDefaultClass: 'sicon-settings',
    cogIconLoadingClass: 'sicon-refresh sicon-is-spinning',

    events: {
        'click [name=edit_button]': 'editClicked',
        'click [name=refresh_button]': 'refreshClicked',
        'click [data-action=newsClick]': 'trackNewsSelect'
    },

    /**
     * Load threshold indicates how many news should be fake-loaded initially and on scroll.
     */
    loadThreshold: 5,

    /**
     * Indicates if more news should be loaded. On initial load and on scroll
     * this variable should be true, but if we simply apply a search term, it should be false.
     */
    loadMore: true,

    /**
     * Shows that how much did we scroll down. When a search is done, the scroll is reset.
     * We reapply the amount of scroll we did with the help of the scrollPosition.
     */
    scrollPosition: 0,

    /**
     * Shows the order number of the last displayed news article.
     * searching will be done on all news.
     */
    lastDisplayedNewsIndex: 0,

    /**
     * Initially and on loading we would like to show a placeholder for the user.
     * Since we start with loading the dependencies, we show the placeholder by default.
     */
    isHintRequestLoading: true,

    /**
     * The list of news articles loaded.
     */
    news: [],

    /**
     * Cache. We save any new articles loaded by the auto-updater.
     * We will show these news if the user click on the notification.
     */
    newArticles: [],

    /**
     * Cache. We save the titles of the new artciles loaded by the auto-updater.
     * This will make it easier to identify if an article was already loeaded.
     */
    newArticleTitles: [],

    /**
     * The list of news articles based on the search term.
     * searchResults will contain all news when the search is reset or there is no search term.
     */
    searchResults: [],

    /**
     * The frequency of the auto-updater in seconds.
     */
    autoUpdaterIntervals: 60,

    isNotHintUser: false,

    /**
     * Holds the time duration of a valid data enrichment access token (1 hour).
     */
    tokenExpirationTimeOut: 60 * 60 * 1000,

    initDashlet: function() {
        this.moduleName = this.context.get('module');
        this.getDependencies();
        this.isDarkMode = app.hint.isDarkMode();
        app.events.on('hint-news-dashlet:search', this.applySearchTerm, this);
        this.runAutoUpdater();
    },

    /**
     * It changes the cog icon into a spinning set of arrows (default dashlet way)
     * if the news are being loaded, reverts on load.
     *
     * @param {boolean} loading Flag indicating if the news are being loaded.
     */
    toggleCogIcon: function(loading) {
        this.cogIconDefaultClass = 'sicon-settings';
        this.cogIconLoadingClass = 'sicon-refresh sicon-is-spinning';
        var iconToAdd = loading ? this.cogIconLoadingClass : this.cogIconDefaultClass;
        var iconToRemove = loading ? this.cogIconDefaultClass : this.cogIconLoadingClass;
        this.cogIcon.removeClass(iconToRemove).addClass(iconToAdd);
    },

    /**
     * On selecting the edit from the dashlet menu, display the notification preferences.
     */
    editClicked: function() {
        app.drawer.open({
            layout: 'stage2-news-preferences-drawer'
        });
    },

    /**
     * Dashlet Refresh event handler.
     */
    refreshClicked: function() {
        this.initNewsRequest();
    },

    /**
     * An error handler used by the stage2 dependency calls.
     *
     * @param {string} message Error message to log.
     * @param {Object} error Error object holding information about the failed request.
     */
    setStage2errorCode: function(message, error) {
        app.logger.error(message.concat(JSON.stringify(error)));
        if (_.isUndefined(this.stage2errorCode)) {
            this.stage2errorCode = error.status;
            this.render();
        }
    },

    /**
     * First dependency of the news request. The news will be get using the url retrieved here.
     */
    getStage2Url: function() {
        var self = this;
        app.api.call('GET', app.api.buildURL('stage2/params'), null, {
            success: function(data) {
                self.stage2url = data.enrichmentServiceUrl;
                self.notificationsServiceUrl = data.notificationsServiceUrl;
                self.initNewsRequest();
            },
            error: _.bind(this.setStage2errorCode, this, 'Failed to get Hint param: ')
        });
    },

    /**
     * Second dependency of the news request. News can be retrieved only with a valid access token.
     */
    getStage2AccessToken: function() {
        var self = this;

        app.api.call('create', app.api.buildURL('stage2/token'), null, {
            success: function(data) {
                self.stage2accessTokenExpireDate = Date.now() + self.tokenExpirationTimeOut;
                self.stage2accessToken = data.accessToken;
                self.initNewsRequest();
            },
            error: _.bind(this.setStage2errorCode, this, 'Failed to get Hint access token: ')
        });
    },

    getNotificationServiceToken: function() {
        var self = this;

        app.api.call('create', app.api.buildURL('stage2/notificationsServiceToken'), null, {
            success: function(data) {
                var now = new Date();
                now.setTime(now.getTime() + data.ttlMs);
                self.notificationsAccessTokenExpireDate = now;
                self.notificationsAccessToken = data.accessToken;
                self.initNewsRequest();
            },
            error: function(err) {
                self.setStage2errorCode('Failed to get Notifications Service Token: ', err);
            }
        });
    },

    /**
     * Checks the instance license AND user license to determine whether the hint dashlet should be displayed
     */
    getLicenseMetadataForHint: function() {
        var self = this;
        var url = app.api.buildURL('hint/license/check');
        app.api.call('GET', url, null, {
            success: function(data) {
                const instanceHasHintLicense = data.isHintUser;
                const userHasHintLicense = app.hint.isHintUser();
                const isHintUser = instanceHasHintLicense && userHasHintLicense;

                self.isNotHintUser = !isHintUser;
                self.render();
            },
            error: function(err) {
                app.logger.error('Failed to get Hint license metadata: ' + JSON.stringify(err));
            }
        });
    },

    /**
     * Will load every dependency of the news.
     * Without a context we will not be able to define what kind of news should be loaded.
     * In order to be able to retrieve some news, we need a url to address and an access token.
     */
    getDependencies: function() {
        this.getStage2Url();
        this.getStage2AccessToken();
        this.getLicenseMetadataForHint();
    },

    /**
     * Checks if the notifications service access token is expired or not.
     * A non existing token is considered an expired token.
     *
     * @return {boolean} True if the token is expired.
     */
    isNotificationsAccessTokenExpired: function() {
        var isExpired = true;
        if (this.notificationsAccessToken) {
            isExpired = this.notificationsAccessTokenExpireDate < new Date();
        }
        return isExpired;
    },

    isAccessTokenExpired: function() {
        var isExpired = true;
        if (this.stage2accessToken) {
            isExpired = this.stage2accessTokenExpireDate < new Date();
        }
        return isExpired;
    },

    /**
     * Checks all dependencies and if they are met a request will be made to retrieve the last 50 news.
     * In case the notifications service access token expires, it will get a new one and trigger this
     * method on a successful get.
     */
    initNewsRequest: function() {
        if (this.isAccessTokenExpired()) {
            this.stage2accessToken =  null;
            this.stage2accessTokenExpireDate =  null;
            this.getStage2AccessToken();
            return;
        } else {
            if (this.stage2url && this.stage2accessToken && this.notificationsServiceUrl) {
                if (this.isNotificationsAccessTokenExpired()) {
                    this.getNotificationServiceToken();
                } else {
                    this.getNews();
                }
            }
        }
    },

    /**
     * If there is no active request for getting news it will send out a request.
     */
    getNews: function() {
        if (this.activeRequest) {
            this.activeRequest.abort();
        }

        this.toggleCogIcon(true);

        var headers = {
            authtoken: this.notificationsAccessToken
        };

        if (this.etag) {
            headers['if-none-match'] = this.etag;
        }

        this.activeRequest = $.ajax({
            type: 'GET',
            url: this.notificationsServiceUrl.concat('/getNotifications'),
            headers: headers,
            context: self,
            success: _.bind(this.loadNews, this),
            error: _.bind(this.handleFailedNewsFetch, this)
        });
    },

    /**
     * Error handler for the news request. Error should be handled only if the
     * request was not aborted manually. Also if the retrieval fails it will try
     * to fetch the news again for a limited number of times.
     */
    handleFailedNewsFetch: function(error) {
        this.activeRequest = null;
        if (error && error.statusText !== 'abort') {
            app.logger.error('Failed to fetch news on Hint: ' + JSON.stringify(error));
            this.toggleCogIcon();
            // Retry to get new token duplets for accessToken and NotificationServiceToken. These
            // access tokens will be reset when the autoUpdater interval runs after 1 minute.
            this.stage2accessToken =  null;
            this.notificationsAccessToken =  null;
        }
    },

    /**
     * Each article should receive the following information:
     * an id, used for searching the news titles for a specific value
     * hide flag, used to show/hide news on the page - initially all news are hidden
     * Aditionally we create a map of the news' titles for being able to use search.
     *
     * @param {Array} news A list of news articles.
     */
    extendNews: function(news) {
        this.articleTitles = [];
        _.each(news, function(article, i) {
            article.id = i;
            article.hide = true;
            this.articleTitles.push(article.title);
        }, this);
        this.news = news;
    },

    /**
     * All the news will be marked as hidden.
     * Used when applying a search term.
     */
    markNewsAsHidden: function() {
        _.each(this.news, function(news) {
            news.hide = true;
        });
    },

    /**
     * This is where the actual search is happening. The searchResults will hold a list of news,
     * these news all contain the search term in their titles. Based on the searchResults
     * we will be able to tell, which  articles need to be marked visible.
     * Important note: here we make the search on ALL available news, however we display none,
     * it is the responsibility of `toggleNews` to display news that are found in the results saved here.
     *
     * @param {string} searchTerm A phrase or a single word.
     */
    searchNews: function(searchTerm) {
        if (searchTerm) {
            this.searchResults = [];
            _.each(this.news, function(article) {
                if (article.title.toLowerCase().indexOf(searchTerm.toLowerCase()) > -1) {
                    this.searchResults.push(article);
                }
            }, this);
        } else {
            this.searchResults = this.news;
        }
    },

    /**
     * If the user did search already the news assigned to the current context
     * we reapply it on the list. Note that `null` means that the user desires to see all news.
     */
    applyExistingSearchTerm: function() {
        var searchTerm = this.readFilter();

        if (searchTerm) {
            app.events.trigger('hint-news-dashlet:apply', searchTerm);
        }

        this.searchNews(searchTerm);
        this.toggleNews();
    },

    /**
     * Initializes the search process when the user changes the assigned field.
     *
     * @param {string|null} searchTerm
     */
    applySearchTerm: function(searchTerm) {
        if (this.news.length && this.readFilter() !== searchTerm) {
            this.loadMore = false;
            this.scrollPosition = 0;
            this.markNewsAsHidden();
            this.writeFilter(searchTerm);
            this.searchNews(searchTerm);
            this.toggleNews();
        }
    },

    /**
     * Saves the last known ETag for later use.
     * Some browsers may return the ETag in lowercase format.
     *
     * @param {Object} request The request for getting news notifications.
     */
    setEtag: function(request) {
        this.etag = request.getResponseHeader('ETag') || request.getResponseHeader('etag');
    },

    /**
     * News request success handler. If we managed to retrieve news,
     * they need to be processed, and displayed. Only a limited number of news will be displayed.
     * The rest of the news will get rendered on scroll. Render will happen through filtering.
     * In case the request returns 'notmodified' statues, we do not make any changes except cog icon.
     *
     * @param {Object} data The response to the news request.
     * @param {string} state Success or notmodified.
     * @param {Object} request The current request.
     */
    loadNews: function(data, state, request) {
        this.activeRequest = null;

        if (!this.disposed) {
            this.toggleCogIcon();
            this.isHintRequestLoading = false;

            if (!this.autoRefresh) {
                this.runAutoUpdater();
                this.extendNews(data);
                this.applyExistingSearchTerm();
            } else if (state !== 'notmodified') {
                this.setEtag(request);

                if (data && data.length) {
                    if (this.news.length) {
                        this.countAndCacheNewArticles(data);
                        if (this.scrollPosition === 0) {
                            this.insertLatestNewsIfAny();
                        }
                    } else {
                        this.extendNews(data);
                        this.applyExistingSearchTerm();
                    }
                } else {
                    app.events.trigger('hint-news-panel-filter:set', null);
                    this._render();
                }
            } else {
                this._render();
            }
        }
    },

    /**
     * An article is new if it has not yet been added to the list of articles
     * that can be displayed in the dashlet.
     *
     * @param {Object} article An article from the news request response.
     * @return {boolean} True if the article is new.
     */
    isNewArticle: function(article) {
        return !_.contains(this.articleTitles, article.title);
    },

    /**
     * Checks if there are any new articles and caches them for later use.
     *
     * @param {Array} data The newest news.
     */
    countAndCacheNewArticles: function(data) {
        var newArticles = [];
        var showNotifier = false;
        _.every(data, function(article) {
            var isNewArticle = !_.contains(this.articleTitles, article.title);
            if (isNewArticle && !_.contains(this.newArticleTitles, article.title)) {
                newArticles.push(article);
                this.newArticleTitles.push(article.title);
                showNotifier = true;
            }
            return isNewArticle;
        }, this);
        this.newArticles = _.union(newArticles, this.newArticles);

        if (showNotifier) {
            this.showUpdateNotifier();
        }
    },

    /**
     * If there are any new news, we will insert them on top of the list.
     * We need to update the counters which depend on the amount of the news.
     */
    insertLatestNewsIfAny: function() {
        if (this.newArticles.length) {
            var updatedNews = _.union(this.newArticles, this.news);
            this.lastDisplayedNewsIndex += this.newArticles.length;
            this.scrollPosition = 0;
            this.extendNews(updatedNews);
            this.applyExistingSearchTerm();
            this.newArticles = [];
            this.newArticleTitles = [];
        } else {
            this.render();
            /* The else part here should not happen with normal, standard workflow.
            Is is intended for use case, when etag status is not sent as expected.
            In other words: the same news are being sent again without a different status. */
        }
    },

    /**
     * In a given interval of times, we will fetch news again and
     * update the list accordingly.
     */
    runAutoUpdater: function() {
        if (!this.autoRefresh) {
            this.autoRefresh = setInterval(
                _.bind(this.initNewsRequest, this),
                this.autoUpdaterIntervals * 1000
            );
        }
    },

    /**
     * @return {string} A unique id for identifying the filter for the current context.
     */
    getFilterKey: function() {
        return 'news-filter-option*dashlet';
    },

    /**
     * Retrieves the filter applyed by the user the last time.
     */
    readFilter: function() {
        return app.user.lastState.get(this.getFilterKey());
    },

    /**
     * Saves the filter value as a user preference. `Null` means no filter.
     *
     * @param {string|null} filter
     */
    writeFilter: function(filter) {
        app.user.lastState.set(this.getFilterKey(), filter);
    },

    /**
     * This method may returns 2 kind of lists of news.
     * One is returned upon scrolling:
     * we need to apply the current search term on the news to be displayed.
     * The other just on searching/filtering:
     * we apply the search term on all the news.
     *
     * @return {Array} A list of news based on the last displayed news.
     */
    getNextSegmentForFilter: function() {
        var start = this.loadMore ? this.lastDisplayedNewsIndex : 0;
        this.lastDisplayedNewsIndex += this.loadMore ? this.loadThreshold : 0;
        return this.searchResults;
    },

    /**
     * News articles will be marked as hidden if their title does not contain the search term.
     * Applying a render will show/hide the corresponding news.
     */
    toggleNews: function() {
        var targetNews = this.getNextSegmentForFilter();
        _.each(targetNews, function(article) {
            if (_.findWhere(this.searchResults, {id: article.id})) {
                article.hide = false;
            }
        }, this);
        this.loadMore = false;
        this.render();
    },

    /**
     * Verifies if there are any news marked as visible among the news that have been already loaded.
     */
    checkNewsToDisplay: function() {
        var segment = this.searchResults;
        this.displayNews = _.find(segment, function(article) {
            return article.hide === false;
        });
    },

    /**
     * Dashlet component usually means just the content area, however we would like to identify
     * the content area together with the header above as the dashlet. In here we do that by assigning
     * a class to the wrapper element. We also search and remember the cog icon element.
     */
    setDashletElements: function() {
        if (!this.cogIcon || !this.cogIcon.length) {
            var fullDashlet = this.$el.parent().parent();
            fullDashlet.addClass('hint-news-dashlet');
            this.cogIcon = fullDashlet.find('[data-action=loading]');
        }
    },

    /**
     * Responsible for showing the message about new articles being ready to be shown.
     * The element is flushed on each render, so we need to attach the event each time.
     */
    showUpdateNotifier: function() {
        if (this.scrollPosition > 0) {
            var notifierFadeOutTimeSecs = 10;
            var notifierEl = this.$('.hint-news-dashlet-auto-notifier');

            notifierEl.removeClass('vanished');
            notifierEl.on('click', _.bind(function() {
                this.$('#newsarea').scrollTop(0);
                this.insertLatestNewsIfAny();
                notifierEl.addClass('vanished');
            }, this));
            setTimeout(function() {
                notifierEl.addClass('vanished');
            }, notifierFadeOutTimeSecs * 1000);
        }
    },

    /**
     * Scroll event has to be bound each time to the element because with each render
     * te attached event handler is cleared. Then again on each render we want to maintain
     * the last known scroll position.
     */
    applyScrollingAndNotifier: function() {
        var newsarea = this.$('#newsarea');
        newsarea.scrollTop(this.scrollPosition);
    },

    /**
     * @inheritdoc
     */
    _render: function(options) {
        this.setDashletElements();
        this.checkNewsToDisplay();
        this._super('_render', [options]);
        this.applyScrollingAndNotifier();
    },

    /**
     * @inheritdoc
     * Detach event handlers. Shut down the auto-updater.
     */
    _dispose: function() {
        clearInterval(this.autoRefresh);
        app.events.off('hint-news-dashlet:search', this.applySearchTerm, this);
        this._super('_dispose');
    },

    /**
     * Tracks which news has been opened by the user.
     *
     * @param {EventObject} event Event information
     */
    trackNewsSelect: function(event) {
        $.ajax({
            type: 'POST',
            data: {
                clickType: 'news',
                origin: this.context.get('module'),
                clickedURL: event.currentTarget.href,
                title: event.currentTarget.innerText,
                metricsToken: app.user.get('hintMetricsToken')
            },
            url: this.stage2url.concat('/url-click'),
            headers: {
                authToken: this.stage2accessToken
            },
            error: function(err) {
                app.logger.error('Failed to record news click event: ' + JSON.stringify(err));
            }
        });
    }
});
