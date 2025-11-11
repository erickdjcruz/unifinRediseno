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
 * @class View.Fields.Base.Colorpicker
 * @alias SUGAR.App.view.fields.BaseColorpicker
 * @extends View.Fields.Base.BaseField
 */
({
    direction: 'ltr',

    /**
     * @inheritdoc
     */
    shouldInitDefaultValue: true,

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        var field = this.$('.hexvar[rel=colorpicker]');
        var preview = this.$('.color-preview');

        field.colorpicker();
        field.on('blur', _.bind(function() {
            var value = field.val();
            preview.css('backgroundColor', value);
            this.model.set(this.name, value);
        }, this));
    }
})
