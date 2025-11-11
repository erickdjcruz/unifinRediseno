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
 * @class View.Fields.Base.DriWorkflowTaskTemplates.CjPopulateFieldsField
 * @alias SUGAR.App.view.fields.DriWorkflowTaskTemplates.BaseCjPopulateFieldsField
 * @extends View.Fields.Base.CjPopulateFields
 */
({
    extendsFrom: 'CjPopulateFieldsField',

    /**
     * Fields that should not be shown in populate_fields option list
     */
    denyListFieldNames: [
        'following',
        'my_favorite',
        'base_rate',
        'deleted',
        '_hash',
        'team_id',
        'team_count',
        'date_end',
        'date_entered',
        'date_modified',
        'date_start',
        'parent_type',
        'direction',
        'description',
        'status',
        'recurring_source',
        'recurrence_id',
        'sequence',
        'name',
        'date_due_flag',
        'date_due',
        'date_start_flag',
        'assigned_user_name',
        'priority'
    ],

    /**
     * Get the module name
     *
     * @return {string|undefined}
     */
    getModuleName: function() {
        if (this.model && this.model.has('activity_type')) {
            return this.model.get('activity_type');
        }
    }
});
