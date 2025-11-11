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
(function(app) {
    app.analytics = app.analytics || {};
    app.analytics.connectors = app.analytics.connectors || {};

    app.analytics.connectors.Pendo  = {
        // disabling of IP address/geolocation tracking is handled by talking with Pendo directly
        // see https://help.pendo.io/resources/support-library/analytics/disable-ip-address-and-geo-location-logging.html

        /**
         * List of default values needed for Pendo analytics
         */
        serverInfoDefaults: {
            'si_id': 'unknown_si_id',
            'si_name': 'unknown_si_name',
            'si_type': 'unknown_si_type',
            'si_license_current': false,
            'si_license_serve': false,
            'si_license_sell': false,
            'si_tier': 'unknown_si_tier',
            'si_customer_since': 'unknown_si_customer_since',
            'si_sic_code': 'unknown_si_sic_code',
            'si_employees_no': 'unknown_si_employees_no',
            'si_managing_team': 'unknown_si_managing_team',
            'si_partner_name': 'unknown_si_partner_name',
            'si_partner_type': 'unknown_si_partner_type',
            'si_account_record': 'unknown_si_account_record',
            'si_customer_region': 'unknown_si_customer_region',
            'si_billing_country': 'unknown_si_billing_country',
            'si_billing_state': 'unknown_si_billing_state',
            'si_billing_city': 'unknown_si_billing_city',
            'si_postal_code': 'unknown_si_postal_code',
            'si_cloud_instance': 'unknown_si_cloud_instance',
            'si_usage_designation': 'unknown_si_usage_designation',
            'si_no_of_licenses': 'unknown_si_no_of_licenses',
            'si_cloud_region': 'unknown_si_cloud_region',
            'si_upgrade_frequency': 'unknown_si_upgrade_frequency',
            'si_db_size': 'unknown_si_db_size',
            'si_file_system_size': 'unknown_si_file_system_size',
            'si_sum_size': 'unknown_si_sum_size',
            'si_rli_enabled': 'unknown_rli_enabled',
            'si_forecasts_is_setup': 'unknown_forcasts_is_setup',
            'si_product_list': 'unknown_product_list',
            'portal_active': 'unknown_portal_activated'
        },

        /*
         * Called on app:init.
         *
         * @member SUGAR.App.analytics.connectors.Pendo
         */
        initialize: function() {
            // do nothing. pendo agent will be loaded by start() when id (apiKey) is available
        },

        /*
         * Called on app:start, prepare or open the connection to the analytics system.
         *
         * @param {string} id Tracking id for the analytics system.
         * @param {Object} options SUGAR.App.config.analytics configuration.
         * @member SUGAR.App.analytics.connectors.Pendo
         */
        start: function (id, options) {
            // this code is taken directly from Pendo
            /* eslint-disable */
            (function(apiKey){
                (function(p,e,n,d,o){var v,w,x,y,z;o=p[d]=p[d]||{};o._q=[];
                    v=['initialize','identify','updateOptions','pageLoad'];for(w=0,x=v.length;w<x;++w)(function(m){
                        o[m]=o[m]||function(){o._q[m===v[0]?'unshift':'push']([m].concat([].slice.call(arguments,0)));};})(v[w]);
                    y=e.createElement(n);y.async=!0;y.src='https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js';
                    z=e.getElementsByTagName(n)[0];z.parentNode.insertBefore(y,z);})(window,document,'script','pendo');
            })(id);
            /* eslint-enable */
        },

        /*
         * Send user and account data.
         *
         * @member SUGAR.App.analytics.connector.Pendo
         */
        configure: function() {
            // check consent for portal user
            if (app.config.platform === 'portal' && !app.user.get('cookie_consent')) {
                return;
            }
            pendo.initialize(this.getPendoMetadata());
        },

        /**
         * Returns the pendo visitor and account info object
         * @return {Object} visitor object, account-info
         */
        getPendoMetadata: function() {
            // user data
            const visitorId = app.user.get('site_user_id') || 'unknown_user';
            const userType = app.user.get('type') || 'unknown_user_type';
            const language = app.user.getLanguage() || 'unknown_language';
            let rolesArray = app.user.get('roles');
            let roles = Array.isArray(rolesArray)
                ? (rolesArray.length >= 1 ? rolesArray.join(',') : 'no_roles')
                : 'unknown_roles';
            let licensesArray = app.user.get('licenses');
            let licenses = Array.isArray(licensesArray) && licensesArray.length > 0
                ? licensesArray.join(',')
                : 'no_licenses';

            // account data
            const activityStreamsEnabled = app.config.activityStreamsEnabled ? 'True' : 'False';
            const editablePreviewEnabled = app.config.previewEdit ? 'True' : 'False';
            const listMaxEntriesPerPage = app.config.maxQueryResult || 'unknown_list_view_items_per_page';
            const listMaxEntriesPerSubpanel = app.config.maxSubpanelResult || 'unknown_list_view_items_per_page';
            const leadConversionOptions = app.config.leadConvActivityOpt || 'unknown_lead_conversion_options';
            const systemDefaultCurrencyCode = app.currency.getBaseCurrency().iso4217 ||
                'unknown_system_default_currency_code';
            const systemDefaultLanguage = app.lang.getLanguage() || 'unknown_system_default_language';
            const awsConnectInstanceName = app.config.awsConnectInstanceName || 'unknown_connect_instance_name';
            const awsConnectUrl = app.config.awsConnectUrl || 'unknown_connect_url';

            const serverInfo = app.metadata.getServerInfo();
            const accountId = serverInfo.site_id || 'unknown_account';
            const siteUrl = _.isFunction(app.utils.getSiteUrl) ? app.utils.getSiteUrl() : 'unknown_domain';
            const version = serverInfo.version || 'unknown_version';
            const flavor = serverInfo.flavor || 'unknown_edition';
            const hostEnvironment = app.config.host_environment || 'on-premise';
            const hostDesignation = app.config.host_designation || 'production';
            const accountBasicInfo = {
                id: accountId,
                domain: siteUrl,
                edition: flavor,
                version: version,
                activity_streams_enabled: activityStreamsEnabled,
                editable_preview_enabled: editablePreviewEnabled,
                list_view_items_per_page: listMaxEntriesPerPage,
                subpanel_items_per_page: listMaxEntriesPerSubpanel,
                lead_conversion_options: leadConversionOptions,
                system_default_currency_code: systemDefaultCurrencyCode,
                system_default_language: systemDefaultLanguage,
                aws_connect_instance_name: awsConnectInstanceName,
                aws_connect_url: awsConnectUrl,
                host_environment: hostEnvironment,
                host_designation: hostDesignation,
            };
            const accountServerInfo = _.each(this.serverInfoDefaults, function (value, name, serverInfoList) {
                serverInfoList[name] = serverInfo[name] || value;
                return serverInfoList;
            });

            return {
                visitor: {
                    id: visitorId,
                    user_type: userType,
                    language: language,
                    roles: roles,
                    licenses: licenses,
                    host_environment: hostEnvironment,
                    host_designation: hostDesignation
                },
                account: _.extend(accountBasicInfo, accountServerInfo)
            };
        },

        /*
         * Track an activity.
         *
         * Pendo auto-tracks most page interactions.
         * See https://help.pendo.io/resources/support-library/api/index.html?bash#track-events
         * @param {string} trackType Activity type.
         * @param {Object} trackData Activity metadata.
         * @member SUGAR.App.analytics.connectors.Pendo
         */
        track: function(trackType, trackData) {
            app.logger.debug(`Pendo track => ${trackType}: ${trackData} `);

            if (_.has(pendo, 'track')) {
                pendo.track(trackType, trackData);
            }
        },

        /*
         * Track a change of page.
         *
         * @param {string} pageUri Uri of the page viewed.
         * @member SUGAR.App.analytics.connectors.Pendo
         */
        trackPageView: function(pageUri) {
            // Pendo automatically collects page view data
            // see https://help.pendo.io/resources/support-library/api/index.html?bash#browser-interactions
            this.track(pageUri);
        },

        /*
         * Track an event on the page.
         *
         * @param {Object} event to track.
         * @param {string} event.action Action of the event (ex. 'click').
         * @param {string} event.category Category of the event (ex. 'quick_create').
         * @param {string} event.label Human readable name of the event for Pendo.
         * @param {string} event.currentLocation Always set to the route the user is on.
         * @param {number} [event.value] Value of the event.
         * @member SUGAR.App.analytics.connectors.Pendo
         */
        trackEvent: function(event) {
            // Pendo Agent API allows us to track more events if needed
            // see https://help.pendo.io/resources/support-library/api/index.html?bash#track-events
            // see app.analytics.track();
            if (!event?.action) {
                throw new Error(`Invalid track event, event.action not valid. ${event}`);
            }

            // These two variables create a more readable event label from the action and category properties.
            // For example, event.action = 'click' becomes 'Click' and event.category = 'quick_create' becomes 'quick
            // create'. Therefore, 'click:quick_create' becomes 'Click quick create'.
            const formattedAction = `${event?.action[0].toUpperCase()}${event?.action.slice(1)}`;
            const parsedCategory = event?.category.split('_').join(' ');
            const eventName = event?.label || `${formattedAction} ${parsedCategory}`;
            app.logger.debug(`Pendo trackEvent => ${eventName}: ${event}`);
            this.track(eventName, event);
        },

        /*
         * Set tracker params.
         *
         * Currently do nothing.
         * @param {string} key The param name.
         * @param {*} value The configuration value to send to the tracker.
         * @member SUGAR.App.analytics.connectors.Pendo
         */
        set: function(key, value) {
            // do nothing
        }
    };
})(SUGAR.App);
