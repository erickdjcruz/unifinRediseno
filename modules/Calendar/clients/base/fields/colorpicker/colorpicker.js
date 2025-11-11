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
 * @class View.Fields.Base.Calendar.ColorpickerField
 * @alias SUGAR.App.view.fields.BaseCalendarColorpickerField
 * @extends View.Fields.Base.ColorpickerField
 */
 ({
    /**
    * @override
    */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.once('render', function() {
            this.listenTo(this.model, 'change:color', _.bind(function() {
                let field = this.$('.hexvar[rel=colorpicker]');
                let preview = this.$('.color-preview');

                if (this.action == 'edit') {
                    var value = field.val();
                    preview.css('backgroundColor', value);
                }
            }, this));
        }, this);
    },
    /**
    * @override
    */
    _render: function() {
        this._super('_render');

        if (this.action != 'edit') {
            this.fillIconBackground();
        }
    },
    /**
     * Sets the background color for the colorpicker icon.
     */
    fillIconBackground: function() {
        if (this.action != 'edit') {
            if (typeof this.value == 'string' && this.value != '') {
                this.$('[data-content=color-picker-icon]').css({
                    'background-color': this.value
                });
            }
        }
    }
});
