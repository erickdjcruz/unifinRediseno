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
 * @class View.Views.Base.Audit.ActivityCardDetailView
 * @alias SUGAR.App.view.views.BaseAuditActivityCardDetailView
 * @extends View.Views.Base.ActivityCardDetailView
 */
({
    extendsFrom: 'ActivityCardDetailView',

    /**
     * @inheritdoc
     */
    getModulesCardMeta: function(baseModule) {
        this.setBaseModule();

        const customName = 'activity-card-definition-for-' + baseModule.toLowerCase();
        return app.metadata.getView(this.baseModule, customName) ||
            app.metadata.getView(this.baseModule, 'activity-card-definition');
    },

    /**
     * Set up base module variable
     */
    setBaseModule: function() {
        if (this.baseModule) {
            return;
        }

        const parentModel = this.activity.get('parent_model');
        this.baseModule = (this.activity.module === 'Audit' && parentModel && parentModel.module) ?
            parentModel.module :
            this.activity.module;
    },
})
