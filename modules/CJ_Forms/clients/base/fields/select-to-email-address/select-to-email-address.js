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
 * @class View.Fields.Base.CJFormsSelectToEmailAddressField
 * @alias SUGAR.App.view.fields.BaseCJFormsSelectToEmailAddressField
 * @extends View.Fields.Base.CjSelectToField
 */
 ({
    extendsFrom: 'CjSelectToField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'cj-select-to';
    },

    /**
     * @inheritdoc
     */
    _getAvailableModulesApiURL: function() {
        let parameters = {
            parent_type: this.model.get('parent_type'),
            parent_id: this.model.get('parent_id')
        };

        if (_.isEmpty(parameters.parent_type) ||
            _.isEmpty(parameters.parent_id)) {
            // Should empty the values of all enum fields.
            return;
        }
        return app.api.buildURL('CJ_Forms', 'available-modules', null, parameters);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.model.off('change:parent_id', this._renderEnumFields, this);
        this._super('_dispose');
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        if (this.model) {
            this.model.on('change:parent_id', this._renderEnumFields, this);
        }
    },

})
