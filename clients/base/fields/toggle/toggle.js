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
 * @class View.Fields.Base.ToggleField
 * @alias SUGAR.App.view.fields.BaseToggleField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * This field is used in Toggle Switch
     */
    extendsFrom: 'BoolField',

    /**
     * @inheritdoc
     *
     * Bypass `render` when action is only `massupdate`.
     */
    bindDataChange: function() {
        if (!this.model) {
            return;
        }

        this.listenTo(this.model, 'change:' + this.name, function(model, value) {
            if (this.action === 'massupdate') {
                this.$(this.select2fieldTag).val(this.format(value) ? '1' : '0');
            } else {
                if (this.action === 'edit') {
                    this.$(this.fieldTag).prop('checked', this.format(value));
                }
                this.render();
            }
        }, this);
    },
});
