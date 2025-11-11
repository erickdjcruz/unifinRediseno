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
 * This field is used in Populate Fields.
 * As we have to display the date fields
 * differently on the view of Populate fields
 * with having different options and logic.
 *
 * Like, there is first dropdown where user
 * select the type (Fixed Date or Relative Date),
 * then base of this next fields appear.
 *
 * Relative Date will display further two
 * fields, one is dropdown and other one is
 * int field. First dropdown have values
 * (Minutes, Hours, Days, Months).
 *
 * Fixed Date will display further a field
 * depending on the type of selected field,
 * i.e Date type or Date Time.
 *
 * @class View.Fields.Base.CJFieldsetForDateInPopulateFields
 * @alias SUGAR.App.view.fields.BaseCJFieldsetForDateInPopulateFields
 * @extends View.Fields.Base.FieldsetField
 */
({
    extendsFrom: 'FieldsetField',
    /**
     * Initializes the fieldset field component.
     *
     * Initializes the fields property.
     *
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'fieldset';
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.bindDomChange();
        this.hideOrShowRelatedDateFields();
    },

    /**
     * hide/show fields on field change
     * event
     *
     * @inheritdoc
     */
    bindDomChange: function() {
        this._super('bindDomChange');

        // on change event trigger for selective dropdown
        let $el = this.$('.cj_selective_date_type');
        if ($el.length) {
            $el.on('change', _.bind(this.toggle_selective_date_type, this));
        }
    },

    /**
     * Toggle hide or show cj_selective_date_type based on if value is 'relative'
     *
     * @param {Object} e jQuery Change Event Object
     */
    toggle_selective_date_type: function(e) {
        let showOrHide = false;
        if (e.val === 'relative') {
            showOrHide = true;
        }
        this.hideOrShowRelatedDateFields(showOrHide, true);
    },

    /**
     * hide/show fields on field property
     *
     * @param {boolean} showOrHide
     * @param {boolean} callFromBindDom
     */
    hideOrShowRelatedDateFields: function(showOrHide, callFromBindDom = false) {
        if (this.def.selectiveDateFieldName && !callFromBindDom) {
            showOrHide = (this.model.get(this.def.selectiveDateFieldName) === 'relative');
        }

        showOrHide = !!showOrHide;
        let childFields = this._getChildFields();
        let field = this.$('.fieldset-field[data-type="datetimecombo"]');

        if (this.action === 'edit') {
            if (showOrHide) {
                field.removeClass('w-3/4');
            } else {
                field.addClass('w-3/4');
            }
        }

        _.each(childFields, function(field) {
            if (field.def.css_class === 'cj_relative_date_type' || field.def.css_class === 'cj_int_date_type') {
                if (showOrHide) {
                    field._show();
                } else {
                    field._hide();
                    this.model.set(field.def.name, '', {silent: true});
                }
                this._callPopulateAddedFieldsDefsHelper(showOrHide ? 'add' : 'remove', field.name, field.def);
            }
            if (field.def.css_class === 'cj_main_date') {
                if (showOrHide) {
                    field._hide();
                    this.model.set(field.def.name, '', {silent: true});
                } else {
                    field._show();
                }
                this._callPopulateAddedFieldsDefsHelper(showOrHide ? 'remove' : 'add', field.name, field.def);
            }
        }, this);
    },

    /**
     * Call the Populate Fields helper, where these
     * dynamic fields will be added/removed in the
     * actual array for track
     *
     * @param {string} op
     * @param {string} fieldName
     * @param {Object} field
     */
    _callPopulateAddedFieldsDefsHelper: function(op, fieldName, field) {
        if (!this.view) {
            return;
        }
        let populateField = this.view.getField('populate_fields');

        if (!populateField || !_.isFunction(populateField.populateAddedFieldsDefsHelper)) {
            return;
        }
        populateField.populateAddedFieldsDefsHelper(op, fieldName, field);
    }
})

