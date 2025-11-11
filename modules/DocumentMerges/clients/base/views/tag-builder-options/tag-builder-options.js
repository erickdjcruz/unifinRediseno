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
 * Used to display different options for
 *  different types of fields in the tag builder.
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderOptionsView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderOptionsView
 */
({
    /**
     * Field types with special options
     * @var array
     */
    specialFieldTypes: ['bool', 'date', 'datetime', 'datetimecombo',
        'image', 'multienum', 'relate', 'link',],

    paddingOnlyFieldTypes: ['int', 'currency', 'decimal', 'float', 'phone'],
    /**
     * List of predefined date formats.
     * @var array
     */
    dateFormats: [
        'MM-DD-YYYY',
        'MMM-YYYY',
        'MMMM-YYYY',
        'MM/DD/YYYY',
        'MM-Do-YYYY',
        'YYYY MMM',
        'dddd, MMMM Do YYYY',
        'dddd, MMMM Do YYYY, h:mm:ss a',
    ],

    /**
     * @inheritdoc
     */
    events: {
        'click [name=back]': 'backToFields',
        'change .dm-relate-field': 'setRelateTagName',
        'change .dm-tag-attribute': 'applyAttribute',
        'click .customOption': 'toggleCustomOption',
        'click .barcode': 'enableBarcode',
        'change [name=customStateOne]': 'customBoolOptionChange',
        'change [name=customStateTwo]': 'customBoolOptionChange',
        'click [name=copyTable]': 'copyTable',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.listenTo(this.context, 'tag-builder-options:show', this.showOptions, this);
    },

    /**
     * @inheritdoc
     */
    render: function(options) {
        this._super('render', arguments);

        this.initializeDropDowns();
        this.hideCustomOptions();
    },

    /**
     * Create select2 components from the available select HTML element
     */
    initializeDropDowns: function() {
        let dropDownList = this.$('select');

        for (let dropDown of dropDownList) {
            let $dropdown = this.$(dropDown);
            let placeholder = $dropdown.attr('placeholder');
            let type = $dropdown.attr('multiple');

            if (type === 'multiple') {
                $dropdown.select2({
                    allowClear: true,
                    placeholder: placeholder,
                    containerCssClass: 'select2-choices-pills-close',
                    closeOnSelect: false,
                    width: '100%',
                });
            } else {
                $dropdown.select2({
                    allowClear: true,
                    placeholder: placeholder,
                    width: '100%',
                });
            }
        }
    },

    /**
     * Shows the options to add to the tags.
     * @param {field} field
     */
    showOptions: function(field) {
        this.type = field.type;
        this.tableAssistant = {};

        if (this.paddingOnlyFieldTypes.includes(this.type)) {
            this.paddingOnly = true;
        }

        if (!this.specialFieldTypes.includes(this.type)) {
            this.type = 'string';
            this.barcode = true;
        }

        if (this.type === 'relate') {
            this.relateModule = field.module;
            this.relateFields = app.metadata.getModule(this.relateModule).fields;
        }

        if (this.type === 'link') {
            this.type = 'collection';
            let relationship = app.metadata.getRelationship(field.relationship);
            this.collectionModule = relationship.lhs_module !== field.currentModule ?
                        relationship.lhs_module : relationship.rhs_module;
            this.collectionFields = app.metadata.getModule(this.collectionModule).fields;
        }

        this.initTag();
        this.tag.setName(field.name).setAttributes({});

        let tagValue = this.tag.compile().getTagValue();
        this.preview = tagValue;

        this.render();
    },

    /**
     * Initializes the tag.
     */
    initTag: function() {
        let type = this.type === 'collection' ? 'collection' : 'base';

        let tagBuilderFactory = new app.utils.DocumentMerge.TagBuilderFactory();
        let tagBuilder = tagBuilderFactory.getTagBuilder(type);
        let tag = tagBuilder.newTag().get();
        this.tag = tag;
    },

    /**
     * Returns from the attributes view.
     */
    backToFields: function() {
        this.paddingOnly = false;
        this.context.trigger('tag-builder-options:hide');
    },

    /**
     * Applies the attribute to the current tag.
     * @param {Event} evt
     */
    applyAttribute: function(evt) {
        let option;
        const inputType = evt.target.type;
        let inputValue = evt.target.value;
        const inputName = evt.target.name;

        if (inputType === 'checkbox') {
            inputValue = evt.target.checked;
        }

        if (inputType === 'select-multiple') {
            // this returns an array if the input is a multiple select
            inputValue = this.$(evt.target).val();
        }

        if (inputName === 'sort') {
            let fieldToSortBy = this.$('select.sortByRelationshipFields').val();
            let sortDirection = this.$('select[data-action=sortDirection]').val();
            inputValue = fieldToSortBy ? fieldToSortBy + ':' + sortDirection : sortDirection;
        }

        //last chance to modify the inputValue
        const forcedInputValue = evt.target.getAttribute('format-value');
        inputValue =  forcedInputValue || inputValue;

        if (inputName === 'fields') {
            this.tag.setFields(inputValue);
            this.updateTableFields(inputValue);
        } else {
            option = {};
            option[inputName] = inputValue;
            this.tag.setAttribute(option);
        }

        // if the barcode checkbox was unchecked
        if (forcedInputValue &&
            inputType === 'checkbox' &&
            !evt.target.checked) {
            this.tag.removeAttribute(inputName);
            // we also need to delete all the other barcode attributes
            this._removeBarcodeAttributes();
        }

        if (!inputValue) {
            this.tag.removeAttribute(inputName);
        }

        this.createCopyTable(inputName, inputValue);
        let tagValue = this.tag.compile().getTagValue();
        this.$('.preview').text(tagValue);
    },

    /**
     * Set the tag name for the relate fields.
     * @param {Event} evt
     */
    setRelateTagName: function(evt) {
        let inputValue = this.$(evt.target).val();
        let baseName = this.tag.getName().split('.')[0];
        this.tag.setName(baseName, inputValue);
        let tagValue = this.tag.compile().getTagValue();

        for (let fieldName in this.relateFields) {
            let field = this.relateFields[fieldName];

            if (field.name === inputValue) {
                this.currentRelateType = field.type;

                if (!this.specialFieldTypes.includes(this.currentRelateType)) {
                    this.currentRelateType = 'string';
                }

                field.selected = true;
            } else {
                field.selected = false;
            }
        }
        this.preview = tagValue;

        this.render();
    },

    /**
     * Hides custom options.
     */
    hideCustomOptions: function() {
        const customOptions = this.$('.customOptions');

        for (let customOption of customOptions) {
            this.$(customOption).hide();
        }
    },

    /**
     * Toggles the visibility of the custom option.
     * @param {Event} evt
     */
    toggleCustomOption: function(evt) {
        let checkedStatus = evt.currentTarget.checked;
        let customOption = this.$('.customOptions');

        if (checkedStatus) {
            customOption.show();
            this.toggleFormatSelects(true);
        } else {
            customOption.hide();
            this.toggleFormatSelects(false);
            this.clearCustomValues();
        }
    },

    /**
     * Clears the custom options values.
     */
    clearCustomValues: function() {
        let customValues = this.$('.customValue');
        customValues.val('');
        customValues.trigger('change');
    },

    /**
     * When choosing the custom option, we disable the
     * format selects for date and bool
     *
     * @param {bool} status
     */
    toggleFormatSelects(status) {
        this.$('.boolFormatSelect').attr('disabled', status);
        this.$('.dateFormatSelect').attr('disabled', status);
    },

    /**
     * One of the custom bool options is activated
     *
     * @param {Event} evt
     */
    customBoolOptionChange: function(evt) {
        const customOptionOne = this.$('[name=customStateOne]').val() || '';
        const customOptionTwo = this.$('[name=customStateTwo]').val() || '';

        let option = {
            'format': `${customOptionOne}/${customOptionTwo}`,
        };
        this.tag.setAttribute(option);
        let tagValue = this.tag.compile().getTagValue();
        this.$('.preview').text(tagValue);
    },

    /**
     * If the barcode checkbox is unchecked,
     * we should be removing the other barcode attributes
     */
    _removeBarcodeAttributes: function() {
        for (const key in this.tag.attributes) {
            if (key.includes('barcode')) {
                this.tag.removeAttribute(key);
            }
        }
    },

    /**
     * Creates the table to be copied.
     */
    createCopyTable: function() {
        this.$('.dm-table-assistant').html(app.template.getView(
            'tag-builder-options.table',
            'DocumentMerges'
        )(this));
    },

    /**
     * Updates the field list for the table.
     *
     * @param {Array} list
     */
    updateTableFields: function(list) {
        _.each(this.collectionFields, _.bind(function setCollectionTableFields(field) {
            if (list.includes(field.name)) {
                this.tag.setCollectionTableFields(field.name, field.translatedLabel);
            }
        }, this));

        this.tableAssistant = this.tag.getCollectionTags();
    },

    /**
     * Copies the table, while validating the input
     *
     * @param {Event} evt
     */
    copyTable: function(evt) {
        if (_.isEmpty(this.tableAssistant.fields)) {
            evt.stopImmediatePropagation();

            app.alert.show('no_fields_error', {
                level: 'error',
                messages: app.lang.get('LBL_NO_SELECTED_FIELDS', this.module),
            });
        }
    },
});
