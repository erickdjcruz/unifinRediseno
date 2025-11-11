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
 * EnumColorcodedForeBkgdField extends EnumColorCodedField to set both foreground and background color
 * based on its value.
 *
 * @class View.Fields.Base.EnumColorcodedForeBkgdField
 * @alias SUGAR.App.view.fields.BaseEnumColorcodedForeBkgdField
 * @extends View.Fields.Base.EnumColorCodedField
 */
({
    extendsFrom: 'EnumColorcodedField',

    /**
     * Number of default colors
     *
     * @property {number}
     */
    colorCount: 12,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        // set default colors
        this._defaultColorCodes = [];
        _.times(this.colorCount, function(n) {
            this._defaultColorCodes.push('enum-color' + (n + 1));
        }, this);
    }
})
