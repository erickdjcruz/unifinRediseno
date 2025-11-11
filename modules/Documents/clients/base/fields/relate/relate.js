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
 * @class View.Fields.Base.Documents.RelateField
 * @alias SUGAR.App.view.fields.BaseDocumentsRelateField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'BaseRelateField',

    /**
     * Formats the filter options for related_doc_rev_number field.
     *
     * @param {boolean} force `true` to force retrieving the filter options whether or not it is available in memory.
     * @return {Object} The filter options.
     */
    getFilterOptions: function(force) {
        if (this.name && this.name === 'related_doc_rev_number' &&
            this.model && !_.isEmpty(this.model.get('related_doc_id'))) {
            return new app.utils.FilterOptions()
                .config({
                    'initial_filter': 'revisions_for_doc',
                    'initial_filter_label': 'LBL_REVISIONS_FOR_DOC',
                    'filter_populate': {
                        'document_id': [this.model.get('related_doc_id')],
                    },
                })
                .format();
        } else {
            return this._super('getFilterOptions', [force]);
        }
    },

    /**
     * Provide date formatting for relate field in list view 'last_rev_create_date' since it is both a relate field
     * and datetime
     * @inheritdoc
     */
    format: function(value) {
        value = this._super('format', [value]);
        // Checking for metadata param set to determine if it should be displayed as a normal date
        if (this.action === 'list' && this.def.list_display_type === 'datetime') {
            value = app.date(value);

            if (!value.isValid()) {
                return '';
            }

            value = value.formatUser(false);
        }
        return value;
    },
})
