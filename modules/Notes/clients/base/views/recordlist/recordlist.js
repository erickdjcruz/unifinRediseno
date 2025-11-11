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
 * @class View.Views.Base.NotesRecordlistView
 * @alias SUGAR.App.view.views.BaseNotesRecordlistView
 * @extends View.Views.Base.RecordlistView
 */
({
    extendsFrom: 'RecordlistView',

    /**
     * Get row fields (except 'multi-attachments') of the model
     *
     * @param {string} modelId Model Id.
     * @return {Array} list of fields objects
     */
    getModelRowFields: function(modelId) {
        var fields = _.filter(this.rowFields[modelId], _.bind(function(field) {
            return (field.type !== 'multi-attachments');
        }, this));

        return fields;
    },
})
