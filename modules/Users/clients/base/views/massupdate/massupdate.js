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
 * @class View.Views.Base.Users.MassupdateView
 * @alias SUGAR.App.view.views.BaseUsersMassupdateView
 * @extends View.Views.Base.MassupdateView
 */
({
    extendsFrom: 'MassupdateView',

    /**
     * @inheritdoc
     *
     * Extends the parent function to also check for fields that are not
     * editable while in IDM mode
     */
    checkFieldAvailability: function(field) {
        let available = this._super('checkFieldAvailability', [field]);
        let idmProtected = app.config.idmModeEnabled && field.idm_mode_disabled;
        let isPreferenceField = field.user_preference;

        return available && !idmProtected && !isPreferenceField;
    },

    /**
     * @override
     */
    getDeleteMessage: function() {
        return app.lang.get('LBL_DELETE_USER_CONFIRM', this.module);
    },

    /**
     * @override
     */
    deleteModelsSuccessCallback: function(data, response, options) {
        this.layout.trigger('list:records:deleted', this.lastSelectedModels);
        this.lastSelectedModels = null;
        if (options.status === 'done') {
            this.layout.context.reloadData({showAlerts: false});
        } else if (options.status === 'queued') {
            app.alert.show('jobqueue_notice', {
                level: 'success',
                title: app.lang.get('LBL_MASS_UPDATE_JOB_QUEUED'),
                autoClose: true
            });
        }
        this._modelsToDelete = null;
        let url = app.bwc.buildRoute('Users', null, 'reassignUserRecords');
        app.router.navigate(url, {trigger: true});
    },
})
