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
    /**
     * @inheritdoc
     *
     * The direction for this field should always be `ltr`.
     */
    direction: 'ltr',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super("initialize", arguments);
        //Generated URL's should not be editable
        if (app.utils.isTruthy(this.def.gen)) {
            this.def.readonly = true;
        }
    },

    format:function(value){
        if (value) {
            if (!value.match(/^([a-zA-Z]+):/)) {
                value = 'http://' + value;
            }
            let whiteList = app.config.allowedLinkSchemes;
            this.def.isClickable = true;
            if (!whiteList.filter(function(scheme) {
                return value.toLowerCase().indexOf(scheme + ':') === 0;
            }).length) {
                this.def.isClickable = false;
            }
        }
        return value;
    },
    unformat:function(value){
        value = (value!='' && value!='http://') ? value.trim() : "";
        return value;
    },
    _render: function() {
        this.def.link_target = _.isUndefined(this.def.link_target) ? '_blank' : this.def.link_target;
        app.view.Field.prototype._render.call(this);
    }
})
