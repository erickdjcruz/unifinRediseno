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
 * @class View.Fields.Base.CJ_Forms.CJTemplateTriggerField
 * @alias SUGAR.App.view.fields.BaseCJTemplateTriggerField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'RelateField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'relate';
    },

    /**
     * @inheritdoc
     */
    getPlaceHolder: function() {
        let placeHolder = app.lang.get(this.def.placeholder, this.model.module);

        // if placeholder label translation is not found then set default select placeholder
        if (_.isEqual(placeHolder, this.def.placeholder)) {
            placeHolder = this._super('getPlaceHolder');
        }

        return placeHolder;
    },
});
