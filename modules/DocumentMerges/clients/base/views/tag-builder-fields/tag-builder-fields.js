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
 * Used in tag builder for showing the current's module fields
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderFieldsView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderFieldsView
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click .field-options': 'showFieldOptions',
        'keyup #searhFields': 'search',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.listenTo(this.context, 'change:currentModule', this.refreshFields, this);
    },

    /**
     * Triggers the event that makes the field options visible.
     * @param {Event} evt
     */
    showFieldOptions: function(evt) {
        evt.preventDefault();
        let fieldName = $(evt.currentTarget.closest('td')).attr('attr-name');

        let field = _.find(this.fieldsMeta, function(_field) {
            return _field.name === fieldName;
        });

        this.context.trigger('tag-builder-options:show', field);
    },

    /**
     * retrieves fields for a module
     *
     * @param {app.Context} context
     * @param {string} module
     */
    refreshFields: function(context, currentModule) {
        this.currentModule = currentModule;

        // reset the fields if the module is unselected
        if (_.isEmpty(this.currentModule)) {
            this.fieldsMeta = [];
            this.render();
            return;
        }

        const fields = app.metadata.getModule(this.currentModule) ?
            app.metadata.getModule(this.currentModule).fields : [];

        if (!_.isEmpty(fields)) {
            this.fieldsMeta = _.filter(fields, function(field) {
                return field.type != 'link' &&
                    field.type != 'id' &&
                    field.name &&
                    typeof field.name == 'string' &&
                    field.name.length > 0;
            });

            // set the initial tag on all fields
            for (let fieldIndex in this.fieldsMeta) {
                let field = this.fieldsMeta[fieldIndex];
                field.tag = `{${field.name}}`;
                field.translatedLabel = app.lang.get(field.vname, this.currentModule);

                // if the label cannot be translated then just use the field name
                field.translatedLabel =
                    field.translatedLabel === field.vname ? field.name : field.translatedLabel;
            }
        } else {
            this.fieldsMeta = [];
        }

        this.render();
    },

    /**
     * Search the table
     *
     * @param {Event} evt
     */
    search: function(evt) {
        let searchTerm = evt.target.value.toLowerCase();
        this.$('.fieldsList tr').filter(_.bind(function(index, element) {
            if (index === 0) {
                // don't hide the search input
                return;
            }

            const tr = this.$(element).children()[0];
            const label = tr.getAttribute('label');

            const hide = label ? label.toLowerCase().indexOf(searchTerm) > -1 : false;
            this.$(element).toggle(hide);
        }, this));
    },
});
