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
 * SugarCRM Analytics API allows user actions to be tracked via an analytics engine.
 *
 * The application event "app:analytics:init" will fire on app.start(). It is recommended that applications use a
 * universal handler to handle pave view changes. Please see the Page Tracking section below for an example of this.
 *
 * Connectors:
 *
 * Analytics connectors are loaded from SUGAR.App.analytics.connectors.
 *
 * Connectors implement the following functions:
 *
 * initialize()
 *
 * - called on app:init, load the analytics api.
 *
 * start(id, options)
 *
 * - called on app:start, prepare or open the connection to the analytics system.
 *
 * @param  {String}id, tracking id of for analytics system
 * @param  {Object}options, SUGAR.App.config.analytics configuration.
 *
 * trackPageView(pageURI)
 *
 * - track a change of page.
 *
 * @param  {String} pageURI.
 *
 * trackEvent()
 *
 * - track an event on the page.
 *
 * @param  {Object} event, with the following attributes: category, action, label, value. Label is always set as the current route the user is on.
 *
 * Below is a sample connector.
 * <pre>
 * (function(app) {
 * SUGAR.App.analytics = SUGAR.App.analytics || {};
 * SUGAR.App.analytics.connectors = SUGAR.App.analytics.connectors || {};
 * SUGAR.App.analytics.connectors["FakeConnector"]  = {
 *     initialized: false,
 *     connectorId: null,
 *       pageLog: [],
 *       eventLog: [],
 *       initialize: function() {
 *           this.initialized = true;
 *       },
 *       start: function(id, options) {
 *           this.connectorId = id;
 *       },
 *       trackPageView: function(pageUri) {
 *           this.pageLog.push(pageUri);
 *       },
 *       trackEvent: function(event) {
 *           this.eventLog.push(event);
 *       }
 *   };
 * })(SUGAR.App);
 * </pre>
 *
 *
 * Configuration:
 *
 * Set the following in SUGAR.App.config.analytics. The entire config is passed to the connector start method as options.
 *
 * <pre>
 * {
 *     enabled: true,
 *     connector: "GoogleAnalytics",
 *     id: 'UA-XXXXXXXX-X'
 * }
 * </pre>
 *
 *
 * Page Tracking:
 *
 * Use SUGAR.App.analytics.trackPageView(uri); to track page views. An example of a universal way of handling page
 * tracking would be the following.
 *
 * <pre>
 * app.on("app:analytics:init", function() {
 *     app.routing.before('route', function(options){
 *          options = options || {};
 *          var args = options.args || [];
 *          var subroute;
 *          if (args[0]) {
 *              var qpos = args[0].indexOf('?');
 *              subroute = qpos > -1 ? args[0].substring(0, args[0].indexOf('?')) : args[0];
 *          }
 *          var viewId = options.route + (subroute ? '/' + subroute : '');
 *
 *          app.analytics.currentViewId = viewId;
 *          app.analytics.trackPageView(app.analytics.currentViewId);
 *      }, this);
 * });
 * app.start();
 * </pre>
 *
 *
 * Event Tracking:
 *
 * Use SUGAR.App.analytics.trackEvent(category, action, event, value); to track events.
 *
 * The label will always be the app.analytics.currentViewId for any event.
 *
 * In JavaScript, this method can be called directly to track any event.
 *
 * In Templates, the analytics engine plugs into all views to look for elements with a track attribute on them.
 * The value of the attribute will contain the event data <category>:<action>.<css class to check>.
 * The event value is sent as 1 or 0 depending on if that css class is present on the element.
 *
 * <pre>
 *    <a href="#" class="trackedClass" track="click:home.trackedClass">Home</a>
 * </pre>
 *
 * @class SUGAR.analytics
 */
(function(app) {
    const EVENT_ACTION = 0;
    const EVENT_CATEGORY = 1;
    const TYPE_PAGE_VIEW = 'pageview';
    const TYPE_EVENT = 'event';

    const _analytics = {

        eventSplitter: /\s{2,}/,
        currentViewId: '',

        /**
         * Object containing the list of events to track, mapped to their
         * respective DOM selector.
         *
         * @protected
         * @type {Object}
         */
        _eventsHash: {
            click: '[track^=click]',
            swipeleft: '[track^=swipeleft]',
            swiperight: '[track^=swiperight]'
        },

        init: function () {
            if (!app.config.analytics || !app.config.analytics.enabled ||
                !app.config.analytics.connector || app.config.analytics.connector === 'GoogleAnalytics' ||
                !app.analytics.connectors[app.config.analytics.connector]
            ) {
                this.trackPageView = () => {};
                this.trackEvent = () => {};
                this.track = () => {};
                return;
            }

            const self = this;
            app.on('app:init', function () {
                app.events.register('app:analytics:init', this);
                app.analytics.connector = app.analytics.connectors[app.config.analytics.connector];
                app.analytics.connector.initialize();

                _.each(self._eventsHash, function (val, key) {
                    $('html').on(key, val, function (e) {
                        const data = self._parseTrackTag($(this).attr('track'));
                        app.analytics.trackEvent(key, data?.action, e, data?.value);
                    });
                });
            }).on('app:start', function () {

                // Apps should do their initialization of page tracking based on the following event.
                app.trigger('app:analytics:init');

                let id = app.config.analytics.id;
                // Keep backwards compatible with nomad
                if (_.isObject(id)) {
                    app.logger.warn('Analytics config id needs to be a valid tracking ID.');
                    id = app.isNative ? id.native : id.web;
                }

                app.analytics.connector.start(id, app.config.analytics);

            }).on('app:sync:complete', function () {
                app.analytics.configure();
            }).on('app:locale:change', function () {

                app.analytics.connector.set('language', app.user.getLanguage());
            }).on('app:view:change', function (layout, params) {
                app.analytics.currentViewId = layout + (params.module ? '/' + params.module : '');
                app.analytics.trackPageView(app.analytics.currentViewId);
            });
        },

        /*
         * Send user and account data.
         *
         * @member SUGAR.App.analytics
         */
        configure: function () {
            if (_.isFunction(app.analytics.connector.configure)) {
                app.analytics.connector.configure();
                return;
            }
            // old connector
            const serverInfo = app.metadata.getServerInfo();

            app.analytics.connector.set('appName',
                'SugarCRM:' + app.config.platform + ':' + (serverInfo.flavor || '').toLowerCase()
            );
            app.analytics.connector.set('appVersion', serverInfo.version + ':' + serverInfo.build);
        },

        /*
         * Track an activity.
         *
         * @param {string} trackType Activity type (name).
         * @param {Object} trackData Activity metadata.
         * @member SUGAR.App.analytics
         */
        track: function (trackType = '', trackData = {}) {
            if (_.isFunction(app.analytics.connector.track)) {
                app.analytics.connector.track(trackType, trackData);
                return;
            }
            // old connector
            if (trackType === TYPE_PAGE_VIEW && !_.isEmpty(trackData.page)) {
                app.analytics.trackPageView(trackData.page);
            } else if (trackType === TYPE_EVENT) {
                const category = _.isEmpty(trackData.category) ? '' : trackData.category;
                const action = _.isEmpty(trackData.action) ? '' : trackData.action;
                const event = _.isEmpty(trackData.event) ? null : trackData.event;
                const value = _.isEmpty(trackData.value) ? '' : trackData.value;
                app.analytics.trackEvent(category, action, event, value);
            }
        },

        /**
         * Track a page view.
         *
         * @param {string} page URI.
         * @member SUGAR.analytics
         */
        trackPageView: function (page) {
            app.logger.debug(`Page: ${page}`);
            app.analytics.connector.trackPageView(page);
        },

        /**
         * Track an event. Example:
         * {
         *     category: 'click',
         *     action: 'quick_create',
         *     location: '/Accounts/<id>',
         *     event: '',
         *     value: '',
         * }
         *
         * @param {string} action Action of the event (ex. 'click').
         * @param {string} category Category of the event (ex. 'quick_create').
         * @param {Object} event Event to track.
         * @param {number} [value] Value associated with the event.
         * @member SUGAR.analytics
         */
        trackEvent: function (action, category, event, value) {
            action = (_.isEmpty(action) && event ? event.currentTarget.id : action) || '[unknown]';
            app.analytics.connector.trackEvent({
                action: action,
                category: category,
                currentLocation: this.currentViewId,
                value: value
            });
        },

        /**
         * Parses the `track` attribute.
         *
         * @param {String} track The value of the `track` attribute (ex. 'click:quick_create').
         * @return {Object} The hash containing the data to pass to
         *   {@link SUGAR.analytics#trackEvent}
         * @return {string} return.action The action (example: 'mass_delete').
         * @return {number|null} return.value The value corresponding to the action.
         * @private
         */
        _parseTrackTag: function (track) {
            const result = {action: '', value: null};
            const pieces = track.split(':');
            if (!pieces[EVENT_CATEGORY]) {
                return result;
            }
            const actionValue = pieces[EVENT_CATEGORY].split('.');
            result.action = actionValue[0];
            if (actionValue[1]) {
                result.value = actionValue[1];
            }

            return result;
        },

        /**
         * @private
         * @member SUGAR.analytics
         */
        _attachEvents: function (events, action, css, $el) {
            if (events) {
                $el.off(events);
                $el.on(events, function (e) {
                    app.analytics.trackEvent(e.type, action, e, (css && $(this).hasClass(css) ? 1 : 0));
                });
            }
        },

        dispose: function () {
            app.off(null, null, this);
        }
    };

    app.augment('analytics', _analytics);

})(SUGAR.App);
