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
 * @class View.Fields.Base.OutboundEmail.AuthStatusField
 * @alias SUGAR.App.view.fields.OutboundEmailAuthStatusField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     *
     */
    bindDataChange: function() {
        if (this.model) {
            this.model.on('change:eapm_id', function(model, value) {
                this.render();
            }, this);
        }
    },

    /**
     * Uses the detail template.
     *
     * @inheritdoc
     */
    _loadTemplate: function() {
        this._super('_loadTemplate');
        this.template = app.template.getField('auth-status', 'detail', this.model.module);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.status = app.lang.get(this.model.get('eapm_id') ? 'LBL_EMAIL_AUTHORIZED' : 'LBL_EMAIL_NOT_AUTHORIZED');
        this._super('_render');
    }
})
