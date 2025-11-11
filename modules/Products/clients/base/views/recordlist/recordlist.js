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
 * @class View.Views.Base.Products.RecordlistView
 * @alias SUGAR.App.view.views.BaseProductsRecordlistView
 * @extends View.Views.Base.RecordlistView
 */
({
    extendsFrom: 'RecordlistView',

    /**
     * @inheritdoc
     *
     * Tracks the last row where the view was changed to non-edit
     */
    toggleRow: function(modelId, isEdit) {
        this._super('toggleRow', [modelId, isEdit]);
        if (!isEdit) {
            this.lastToggledModel = this.collection.get(modelId);
        }
    },

    /**
     * Adds a secondary reverting of model attributes when cancelling an edit
     * view of a row. This fixes issues with service fields not properly
     * clearing when cancelling the edit
     */
    cancelClicked: function() {
        if (this.lastToggledModel) {
            this.lastToggledModel.revertAttributes();
        }
        this.resize();
    }
})

