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
 * The modules view in tag-builder.
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderModulesView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderModulesView
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'change .dm-tag-builder-modules': 'setCurrentModule',
    },

    /**
     * List of modules to be excluded from tag builder
     *
     * @var array
     */
    denyModules: [
        'Login', 'Home', 'WebLogicHooks', 'UpgradeWizard',
        'Styleguide', 'Activities', 'Administration', 'Audit',
        'Calendar', 'MergeRecords', 'Quotas', 'Teams', 'TeamNotices', 'TimePeriods', 'Schedulers', 'Campaigns',
        'CampaignLog', 'CampaignTrackers', 'Documents', 'DocumentRevisions', 'Connectors', 'ReportMaker',
        'DataSets', 'CustomQueries', 'WorkFlow', 'EAPM', 'Users', 'ACLRoles', 'InboundEmail', 'Releases',
        'EmailMarketing', 'EmailTemplates', 'SNIP', 'SavedSearch', 'Trackers', 'TrackerPerfs', 'TrackerSessions',
        'TrackerQueries', 'SugarFavorites', 'OAuthKeys', 'OAuthTokens', 'EmailAddresses',
        'Sugar_Favorites', 'VisualPipeline', 'ConsoleConfiguration', 'SugarLive',
        'iFrames', 'Sync', 'DataArchiver', 'MobileDevices',
        'PushNotifications', 'PdfManager', 'Dashboards', 'Expressions', 'DataSet_Attribute',
        'EmailParticipants', 'Library', 'Words', 'EmbeddedFiles', 'DataPrivacy', 'CustomFields', 'ArchiveRuns',
        'KBDocuments', 'KBArticles', 'FAQ', 'Subscriptions', 'ForecastManagerWorksheets', 'ForecastWorksheets',
        'pmse_Business_Rules', 'pmse_Project', 'pmse_Inbox', 'pmse_Emails_Templates',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.getModules();
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render', arguments);

        this.initializeDropDown();

        // trigger this, so the options will hide
        this.context.set('currentModule', null);
    },

    /**
     * apply select2 to all selects
     */
    initializeDropDown: function() {
        let dropDown = this.$('.select2');

        dropDown.select2({
            allowClear: true,
            placeholder: dropDown.attr('placeholder')
        });
    },

    /**
     * Returns a list of modules
     *
     * @return array
     */
    getModules: function() {
        const url = app.api.buildURL('DocumentMerge', 'mergeModules');

        app.api.call('read', url, null, {
            success: _.bind(function(response) {
                this.modules = _.map(response, function(value, key) {
                    return {
                        moduleName: key,
                        moduleDisplay: value,
                    };
                });

                this.render();
            }, this),
            error: function() {
                app.alert.show('merges-error', {
                    level: 'error',
                    autoClose: true,
                    messages: app.lang.getModString('LBL_DOCUMENT_MERGE_COULD_NOT_RETRIEVE_MODULES', this.module),
                });
            }
        });
    },

    /**
     * Sets the current module on the context
     *
     * @param {Event} evt
     */
    setCurrentModule: function(evt) {
        const module = this.$('select.dm-tag-builder-modules').val();

        this.context.trigger('tag-builder:reset-relationships', module);

        this.context.set({
            currentModule: module,
            currentRelationshipsModule: module
        });
    },

});
