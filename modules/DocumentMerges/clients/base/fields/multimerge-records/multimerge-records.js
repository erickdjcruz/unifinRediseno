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
 * @class View.Fields.Base.DocumentMerges.MultimergeRecordsField
 * @alias SUGAR.App.view.fields.BaseDocumentMergesMultimergeRecordsField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    format: function(value) {
        this._super('format', arguments);

        try {
            this.records = JSON.parse(value);
        } catch (error) {
            this.records = [];
        }

        return value;
    }
})
