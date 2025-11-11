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
 * @class View.Fields.Base.CjDRIWorkflowTaskTemplateAllowActivityByField
 * @alias SUGAR.App.view.fields.BaseCjDRIWorkflowTaskTemplateAllowActivityByField
 * @extends View.Fields.Base.CjSelectToField
 */
 ({
    extendsFrom: 'CjSelectToField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'cj-select-to';
        this.dropdownName = 'cj_allow_activity_by_list';

        this.subFieldsMapping = {
            'users': {
                name: this.namePrefix + 'user_name',
                type: 'relate',
                id_name: this.namePrefix + 'user_id',
                module: 'Users',
                isMultiSelect: true
            },
            'teams': {
                name: this.namePrefix + 'team_name',
                type: 'relate',
                id_name: this.namePrefix + 'team_id',
                module: 'Teams',
                isMultiSelect: true
            },
            'roles': {
                name: this.namePrefix + 'role_name',
                type: 'relate',
                id_name: this.namePrefix + 'role_id',
                module: 'ACLRoles',
                isMultiSelect: true
            }
        };

        this._prepareOptionsList();
    },
})
