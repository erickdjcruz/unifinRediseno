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
    /**
     * CJBase helper.
     * These functions are to be used for our components and have generic functions
     *
     * Usage:
     *      app.CJBaseHelper.<func_name>()
     *
     * @class SUGAR.CJBaseHelper
     * @singleton
     * @alias SUGAR.App.CJBaseHelper
     */
    app.augment('CJBaseHelper', {

        /**
         * Store config values
         * @param {Object}
         */
        _config: {},

        /**
         * Default display type
         *
         * @property {string} _CJDefaultDisplay
         * @protected
         */
        _CJDefaultDisplay: 'panel_bottom',

        /**
         * Getter for _config object
         *
         * @return {Object}
         */
        getConfig: function() {
            return this._config;
        },

        /**
         * It will add/modify the CJ config
         *
         * @param {Object} config
         * @return {void}
         */
        setConfig: function(config) {
            this._config = this._config || {};
            this._config = _.extend(this._config, config);
        },

        /**
         * It will check if the value is true (true, 'true', 1, '1') or not
         *
         * @param {string} value
         * @return {boolean} `true` if the value is true `false` otherwise
         */
        isTrue: function(value) {
            return value === 1 || value === '1' || value === true || value === 'true';
        },

        /**
         * It will return the particular key value from cache
         *
         * @param {string} name
         * @param {string} lastStateId
         * @param {string} module
         * @param {string} lastStateIdModule
         * @return {string}
         */
        getValueFromCache: function(name, lastStateId, module, lastStateIdModule) {
            let key = app.user.lastState.buildKey(name, lastStateId, lastStateIdModule);
            let defaultCacheValue = app.user.lastState.defaults(key);
            let cacheKey = app.user.lastState.buildKey(name, lastStateId, module);
            let cacheValue = app.user.lastState.get(cacheKey);

            if (!defaultCacheValue && !cacheValue) {
                cacheValue = lastStateId === 'cj_active_or_archive_filter' ? 'active' : 'V';
            }

            return cacheValue || defaultCacheValue;
        },

        /**
         * Hide or show specific panel
         *
         * @param {string} label
         * @param {boolean} hideOrShow
         */
        showHidePanel: function(label, hideOrShow) {
            let panel = $(`div[data-panelname="${label}"]`);
            (!!hideOrShow) ? panel.show() : panel.hide();
        },

        /**
         * Get array of enabled modules from config
         *
         * @return {Array}
         */
        getCJEnabledModules: function() {
            if (_.isEmpty(app.config.customer_journey)) {
                return [];
            }

            let enabledModules = app.config.customer_journey.enabled_modules || '';
            enabledModules = enabledModules.split(',');

            if (_.isEmpty(enabledModules) || !_.isArray(enabledModules)) {
                return [];
            }

            return enabledModules.filter(function(val) {
                return String(val).trim();
            });
        },

        /**
         * Return CJ view setting i.e. panel_top / panel_bottom / tab_first /tab_last
         *
         * @param {string} module
         * @param {boolean} all
         * @return {string}
         */
        getCJRecordViewSettings: function(module = '', all = false) {
            if ((_.isEmpty(module) && !all) || _.isEmpty(app.config.customer_journey) ||
                !app.config.customer_journey.enabled_modules.split(',').includes(module)) {
                return '';
            }

            let cjRecordViewDisplaySettings = app.config.customer_journey.recordview_display_settings || [];

            if (all) {
                return cjRecordViewDisplaySettings;
            }
            return cjRecordViewDisplaySettings[module] || this._CJDefaultDisplay;
        },

        /**
         * Show the license error if invalid
         *
         * @param {string}
         * @param {string}
         */
        invalidLicenseError: function(alertName = 'invalid_license', message) {
            app.alert.show(alertName, {
                level: 'error',
                messages: !_.isUndefined(message) ? message : app.lang.get('LBL_CUSTOMER_JOURNEY_LICENSE_LOAD_ERROR'),
                autoClose: true,
            });
        },

        /**
         * Fetch active smart guides, create cj-forms-batch view
         * and call saveRecord callback
         *
         * @param {Object} context
         * @param {Object} layout
         * @param {string} module
         * @param {string} record
         * @param {Function} saveRecordCallback
         */
        fetchActiveSmartGuideCount: function(context, layout, module, record, saveRecordCallback) {
            const cjEnabledModules = this.getCJEnabledModules();

            if (_.isEmpty(cjEnabledModules) || !_.contains(cjEnabledModules, module)) {
                saveRecordCallback();
                return;
            }

            // for cj enable module first get active smart guides then save model
            const url = app.api.buildURL(module, 'activeSmartGuidesCount', {
                id: record,
            });

            app.api.call('read', url, null, {
                success: _.bind(function(count) {
                    let cjFormBatch;

                    if (count > 0) {
                        cjFormBatch = app.view.createView({
                            context: context,
                            type: 'cj-forms-batch',
                            module: module,
                            layout: layout
                        });

                        cjFormBatch.startRecordSaving();
                    }

                    saveRecordCallback(cjFormBatch);
                }, this),
                error: _.bind(function() {
                    saveRecordCallback();
                }, this),
            });
        },

        /**
         * Provide batch chunk count from config
         *
         * @return {number}
         */
        getBatchChunk: function() {
            return +app.config.customer_journey.sugar_action_batch_chunk || 1;
        },
    }, true);
})(SUGAR.App);
