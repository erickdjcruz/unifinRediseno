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
 * @class View.Fields.Base.UrlField
 * @alias SUGAR.App.view.fields.BaseUrlField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'UrlField',

    /**
     * @inheritdoc
     */
    format: function(value) {
        value = this._super('format', [value]);
        if (value) {
            this.def.isClickable = true;

            if (!(value.indexOf('http://') === 0 || value.indexOf('https://') === 0)) {
                this.def.isClickable = false;
            }
        }
        return value;
    },

    /**
     * Check to see if is standard view
     *
     * @return {boolean} true if is standard view
     */
    isStandardView: function() {
        return _.contains(['record', 'list', 'create'], this.view.name);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this.isStandardView() && this.name === 'hint_account_website' && this.module === 'Leads') {
            this.$el.parent().addClass('hidden');
        }
        this.def.link_target = (_.isUndefined(this.def.link_target) ||
             _.isEmpty(this.def.link_target)) ? '_blank' : this.def.link_target;
        app.view.Field.prototype._render.call(this);
    },
});
