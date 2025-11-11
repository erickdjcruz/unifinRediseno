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
 * @class View.Fields.Base.ChangeTimers.Field_nameField
 * @alias SUGAR.App.view.fields.BaseChangeTimersField_nameField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     *
     * We want to show the field name's translated value instead of the vardef field name
     */
    format: function(value) {
        var module = this.context.get('parentModule');
        var defs = app.metadata.getModule(module, 'fields');
        // No field def found
        if (!defs[value]) {
            return value;
        }
        var fieldDef = defs[value];
        var label = fieldDef.vname || fieldDef.label;
        return app.lang.get(label, module);
    }
})
