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
 * @class View.Views.Base.AdministrationTimelineConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationTimelineConfigView
 * @extends View.Views.Base.View
 */
({
    /**
     * The api path.
     * @property {string}
     */
    apiPath: 'config/timeline',

    /**
     * The main setting name.
     * @property {string}
     */
    configName: '',

    /**
     * Config module.
     * @property {string}
     */
    configModule: '',

    /**
     * The css class used for the main element.
     * @property {string}
     */
    className: 'admin-config-body',

    /**
     * Enabled modules
     * @property {Object}
     */
    enabledModules: [],

    /**
     * Available modules
     * @property {Object}
     */
    availableModules: [],

    /**
     * List of modules enabled by default
     * @property {Object}
     */
    defaultModules: [
        'Meetings',
        'Calls',
        'Notes',
        'Emails',
        'Messages',
        'Tasks',
        'Audit',
        // Market Modules
        'sf_webActivity',
        'sf_Dialogs',
        'sf_EventManagement',
    ],

    /**
     * Event listeners
     */
    events: {
        'change input[type=checkbox]': 'changeHandler',
    },

    /**.
     * @inheritdoc
     */
    render: function(options) {
        this._super('render', [options]);

        this._processLimitAlert();
    },

    /**.
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.configModule = this.context.get('target');
        if (this.configModule) {
            this.configName = 'timeline_' + this.configModule;
            this.boundSaveHandler = _.bind(this.saveSettings, this);
            this.context.on('save:config', this.boundSaveHandler);
            this.getAvailableModules();
        }
    },

    /**
     * Set available modules from subpanel metadata.
     */
    getAvailableModules: function() {
        let self = this;
        let configedModules = null;
        if (app.config.timeline && app.config.timeline[this.configModule]) {
            configedModules = app.config.timeline[this.configModule].enabledModules || [];
            configedModules = _.filter(configedModules, function(link) {
                // make sure the link is still valid
                return self._isValidLink(link);
            });
        }
        let enabledModules = configedModules ? _.clone(configedModules) : [];
        let availableModules = [];
        let meta = app.metadata.getModule(this.configModule);
        let subpanels = meta && meta.layouts && meta.layouts.subpanels &&
            meta.layouts.subpanels.meta && meta.layouts.subpanels.meta.components || [];

        _.each(subpanels, function(subpanel) {
            let link = subpanel.context && subpanel.context.link || '';
            if (self._isValidLink(link)) {
                let label = app.lang.get(subpanel.label, self.configModule);
                let relatedModule = app.data.getRelatedModule(self.configModule, link);
                availableModules.push({link: link, label: label});
                if (_.isNull(configedModules) && _.contains(self.defaultModules, relatedModule)) {
                    enabledModules.push(link);
                }
            }
        });
        this.enabledModules = enabledModules;
        this.availableModules = _.sortBy(availableModules, (module) => module.label.toLowerCase());
    },

    /**
     * Check if a link is valid.
     * @param {string} link
     * @return {boolean}
     * @private
     */
    _isValidLink: function(link) {
        if (!link) {
            return false;
        }
        const hiddenSubpanels = app.metadata.getHiddenSubpanels();
        const relatedModule = app.data.getRelatedModule(this.configModule, link);
        return relatedModule && !_.contains(hiddenSubpanels, relatedModule.toLowerCase());
    },

    /**
     * Save the settings.
     */
    saveSettings: function() {
        let options = {
            error: _.bind(this.saveErrorHandler, this),
            success: _.bind(this.saveSuccessHandler, this)
        };
        let settings = {};
        settings[this.configName] = {enabledModules: this.enabledModules};
        let url = app.api.buildURL(this.module, this.apiPath);
        app.api.call('create', url, settings, options);
    },

    /**
     * Enable/disable a module.
     * @param {UIEvent} e
     */
    changeHandler: function(e) {
        let link = $(e.currentTarget).data('link');
        let enabled = e.currentTarget.checked;
        if (_.contains(this.enabledModules, link) && !enabled) {
            this.enabledModules = _.without(this.enabledModules, link);
        } else if (!_.contains(this.enabledModules, link) && enabled) {
            this.enabledModules.push(link);
        }
        this._processLimitAlert();
    },

    /**
     * On a successful save, a message will be shown indicating that the settings have been saved.
     *
     * @param {Object} settings
     */
    saveSuccessHandler: function(settings) {
        app.config.timeline = app.config.timeline || {};
        app.config.timeline[this.configModule] = app.config.timeline[this.configModule] || {};
        app.config.timeline[this.configModule].enabledModules = this.enabledModules;
        this.closeView();
        app.alert.show(this.settingPrefix + '-info', {
            autoClose: true,
            level: 'success',
            messages: app.lang.get('LBL_ACTIVITY_TIMELINE_SETTINGS_SAVED', this.module)
        });
    },

    /**
     * Show an error message if the settings could not be saved.
     */
    saveErrorHandler: function() {
        app.alert.show(this.settingPrefix + '-warning', {
            level: 'error',
            title: app.lang.get('LBL_ERROR')
        });
    },

    /**
     * On a successful save return to the Administration page.
     */
    closeView: function() {
        // Config changed... reload metadata
        app.sync();
        if (app.drawer && app.drawer.count()) {
            app.drawer.close(this.context, this.context.get('model'));
        } else {
            app.router.navigate(this.module, {trigger: true});
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('save:config', this.boundSaveHandler);
        }
        this._super('_dispose');
    },

    /**
     * Dissallow to enable more modules then allowed.
     *
     * @private
     */
    _processLimitAlert: function() {
        const state = this.enabledModules.length >= 10;
        this.$('.timeline-alert').toggleClass('hidden', !state);
        this.$('input:checkbox:not(:checked)').attr('disabled', state);
    },
})
