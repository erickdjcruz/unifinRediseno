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

/**
* @class View.Views.Base.ContentsearchdashletView
* @alias SUGAR.App.view.views.BaseContentsearchdashletView
* @extends View.View
*/
({
    plugins: ['Dashlet', 'Previewable'],

    events: {
        'click [data-action="create-case"]': 'initCaseCreation',
        'keyup [data-action="search"]': 'searchCases'
    },

    /**
     * Search options.
     * @property {Object}
     */
    searchOptions: {
        max_num: 4,
        module_list: 'KBContents'
    },

    /**
     * Maximum number of characters of search results to display.
     * @property {number}
     */
    maxChars: 500,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._setPropertiesData();
    },

    /**
     * Set initial properties
     *
     * @private
     */
    _setPropertiesData: function() {
        this.module = 'Cases';
        this.caseDeflection = this.isCaseDeflectionEnabled();
        this.canCreateCase = app.acl.hasAccess('create', this.module);
        this.greeting = this._getGreeting();
        this.searchDropdown = null;

        this.enabled = _.isUndefined(app.config.searchBar) || app.config.searchBar;
        this.bannerBackgroundStyle = app.config.bannerBackgroundStyle || 'default';
        this.bannerBackgroundColor = app.config.bannerBackgroundColor || '#ffffff';
        this.bannerBackgroundImage = app.config.bannerBackgroundImage || '';

        this.context.on('page:clicked', this._search, this);
    },

    /**
     * Additional 'data:preview' callbacks to be invoked from Previewable
     *
     * @param hasChanges
     * @param data
     */
    dataPreviewCallbacks: function(hasChanges, data) {
        if (!hasChanges || !data.properties || _.isEmpty(data.properties)) {
            return;
        }
        var bannerProperties = ['bannerBackgroundStyle', 'bannerBackgroundColor', 'bannerBackgroundImage'];
        var isBannerProperty = _.intersection(data.properties, bannerProperties).length > 0;
        if (isBannerProperty) {
            this.updateBannerBackgroundStyle();
        }
    },

    /**
     * Util to get the text displayed above search bar
     * @return {string}
     * @private
     */
    _getGreeting: function() {
        var greeting = app.config.searchBarText || '';
        if (greeting) {
            return greeting.replace('{{full-name}}', app.user.get('full_name'));
        }
        return app.lang.get(this.meta.default_greeting, this.module, {
            username: app.user.get('full_name')
        });
    },

    /**
     * Gets search and display options from dashlet settings if exist.
     */
    initDashlet: function() {
        this.searchOptions = {
            module_list: this.settings.get('module_list') || this.searchOptions.module_list,
            max_num: this.settings.get('max_num') || this.searchOptions.max_num
        };
        this.maxChars = this.settings.get('max_chars') || this.maxChars;
    },

    /**
     * Checks if case deflection is enabled. In case it is enabled the dashlet
     * will render a search bar for the users, if not it will render a message
     * with the case creation button.
     *
     * @return {boolean} True if case deflection is enabled.
     */
    isCaseDeflectionEnabled: function() {
        return _.isUndefined(app.config.caseDeflection) ||
            app.config.caseDeflection === 'enabled';
    },

    /**
     * Will display the case creation drawer from where
     * the users are able to create a new case.
     */
    initCaseCreation: function() {
        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: 'Cases'
            }
        });
    },

    /**
     * Starts a new search and show the search results dropdown.
     */
    searchCases: _.debounce(function() {
        var $input = this.$('input[data-action=search]');
        var term = $input.val().trim();

        if (term === '') {
            if (this.searchDropdown) {
                this.searchDropdown.hide();
            }
            return;
        }

        this.searchOptions.q = term;

        if (_.isNull(this.searchDropdown)) {
            this.searchDropdown = app.view.createLayout({
                context: this.context,
                name: 'contentsearch-dropdown',
                module: 'Cases'
            });
            this.searchDropdown.initComponents();
            this.layout._components.push(this.searchDropdown);
            this.searchDropdown.render();
            $input.after(this.searchDropdown.$el);
        }

        this.searchDropdown.hide();
        this.context.trigger('data:fetching');
        this.searchDropdown.show();
        this._search();
    }, 400),

    /**
     * Calls search api
     *
     * @param {Object} options The search options
     * @private
     */
    _search: function(options) {
        var pageNumber = options && options.pageNum || 1;
        var offset = (pageNumber - 1) * this.searchOptions.max_num;
        var params = _.extend({}, this.searchOptions, {offset: offset});
        var url = app.api.buildURL('genericsearch', null, null, params);
        app.api.call('read', url, null, {
            success: _.bind(function(result) {
                if (this.disposed) {
                    return;
                }
                if (this.context) {
                    var data = this._parseData(result);
                    this.context.trigger('data:fetched', data);
                }
            }, this)
        });
    },

    /**
     * Parses search results.
     *
     * @param {Object} result The search result
     * @return {Object} parsed data
     * @private
     */
    _parseData: function(result) {
        var self = this;
        var totalPages = result.total > 0 ?
            Math.ceil(result.total / this.searchOptions.max_num) : 0;
        var currentPage = result.next_offset > 0 ?
            result.next_offset / this.searchOptions.max_num : totalPages;
        var records = _.map(result.records, function(record) {
            return {
                name: record.name,
                description: self._truncate(record.description),
                url: app.utils.buildUrl(record.url.replace(/^\/+/g, ''))
            };
        });
        return {
            options: this.searchOptions,
            currentPage: currentPage,
            records: records,
            totalPages: totalPages
        };
    },

    /**
     * Truncates search result so it is shorter than the maxChars
     * Only truncate on full words to prevent ellipsis in the middle of words
     * @param {string} text The search result entry to truncate
     * @return {string} the shortened version of an entry
     * @private
     */
    _truncate: function(text) {
        text = text || '';

        if (text.length > this.maxChars) {
            var cut = text.substring(0, this.maxChars);
            // cut at a full word
            while (!(/\s/.test(cut[cut.length - 1])) && cut.length > 0) {
                cut = cut.substring(0, cut.length - 1);
            }
            text = cut + '...';
        }

        return text;
    },

    /**
     * Update the banner background with the specified style.
     */
    updateBannerBackgroundStyle: function() {
        var backgrounds = {
            default: '',
            color: this.bannerBackgroundColor,
            image: 'url(' + this.bannerBackgroundImage + ') center/cover'
        };
        this.$el.css({
            background: backgrounds[this.bannerBackgroundStyle]
        });
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.updateBannerBackgroundStyle();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('page:clicked', null, this);
        }
    }
})
