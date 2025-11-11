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
 * @class View.Views.Base.Audit.ActivityCardHeaderCreateView
 * @alias SUGAR.App.view.views.BaseAuditActivityCardHeaderCreateView
 * @extends View.Views.Base.ActivityCardHeaderView
 */
({
    extendsFrom: 'ActivityCardHeaderView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        const module = this.activity.get('parent_model').module;
        this.moduleName = app.lang.getModuleName(module);
        const name = `${this.moduleName} ${app.lang.get('LBL_CREATED', this.module)}`;

        this.createModel = app.data.createBean(module, {
            id: this.activity.get('parent_id'),
            name: name,
            created_by_name: this.activity.get('created_by_name'),
            created_by: this.activity.get('created_by'),
        });
    },

    /**
     * @inheritdoc
     */
    getActivityCardLayout: function() {
        return this.closestComponent('activity-card-create');
    },

    /**
     * @inheritdoc
     */
    setUsersFields: function() {
        const panel = this.getUsersPanel();

        this.userField = _.find(panel.defaultFields, function(field) {
            return field.name === 'created_by_name';
        });

        this.hasAvatarUser = !!this.userField;
    },
})
