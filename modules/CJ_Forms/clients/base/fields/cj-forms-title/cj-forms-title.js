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
 * @class View.Fields.Base.CJ_Forms.CJFormsTitleField
 * @alias SUGAR.App.view.fields.BaseCJFormsTitleField
 * @extends View.Fields.Base.LabelField
 */
({
    extendsFrom: 'LabelField',

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this._showHideField();
    },

    /**
     * Hide field on detail view and show it on edit view on the basis of trigger type
     */
    _showHideField: function() {
        let triggerType = this.model && this.model.get('main_trigger_type');
        let showField = _.isEqual(triggerType, 'sugar_action_to_smart_guide');
        showField = showField && [this.action, this.tplName].includes('edit');
        let $fieldElement = this.$el.closest('.panel_body');

        if (_.isEqual($fieldElement.length, 0)) {
            // for preview view field is rendered as record-cell
            $fieldElement = this.$el.closest('.record-cell');
            // for preview view hide label
            $fieldElement.find('.field-label').hide();
        }

        showField ? $fieldElement.show() : $fieldElement.hide();
    },

    /**
     * @inheritdoc
     */
    format: function(value) {
        value = app.lang.get(this.def.label, this.module);
        this.labelDescription = app.lang.get(this.def.label_description, this.module);

        return value;
    }
})
