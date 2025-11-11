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
 * @class View.Fields.Base.Opportunities.FieldsetCascadeField
 * @alias SUGAR.App.view.fields.BaseOpportunitiesFieldsetCascadeField
 * @extends View.Fields.Base.FieldsetField
 */
({
    extendsFrom: 'FieldsetField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['Cascade']);
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    _loadTemplate: function() {
        // If the field isn't editable or disabled, fall back to fieldset's
        // base templates.
        if (this.action !== 'edit' && this.action !== 'disabled') {
            this.type = 'fieldset';
        }

        // If this field is disabled, setDisabled will cascade that down
        // to the subfields
        if (this.action === 'disabled') {
            this.setDisabled(true);
        }

        this._super('_loadTemplate');
        this.type = this.def.type;
    }
})
