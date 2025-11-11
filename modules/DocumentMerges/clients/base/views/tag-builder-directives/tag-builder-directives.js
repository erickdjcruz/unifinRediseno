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
 * The view used for directives builder.
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderDirectivesView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderDirectivesView
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'change .directivesList': 'changeDirective',
        'change .dm-tag-attribute': 'applyAttribute',
        'change .customDateOption': 'toggleDateOption',
        'change .relationshipsModuleList': 'updateRelationshipFieldsList',
        'change .tableRelationshipFieldsList': 'updateTableHeader',
    },

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
    ],

    /**
     * List of directives.
     * @var array
     */
    directives: [
        {
            value: 'date',
            name: 'Date',
        },
        {
            value: 'list',
            name: 'List',
        },
        {
            value: 'table',
            name: 'Table',
        },
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.listenTo(this.context, 'change:currentModule', this.updateRelationships, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        this.initializeDropDowns();
        this.hideCustomDate();
        this.initColorPicker();
    },

    /**
     * Initialize color picker
     */
    initColorPicker: function() {
        var field = this.$('.hexvar[rel=colorpicker]');
        var preview = this.$('.color-preview');

        field.colorpicker();
        field.on('blur', _.bind(function() {
            var value = field.val();
            preview.css('backgroundColor', value);
            this.applyAttribute(field);
        }, this));
    },

    /**
     * Initializes the select2 dropdowns.
     */
    initializeDropDowns: function() {
        let dropDownList = this.$('select');

        for (let dropDown of dropDownList) {
            let placeholder = this.$(dropDown).attr('placeholder');
            let type = this.$(dropDown).attr('multiple');

            if (type === 'multiple') {
                this.$(dropDown).select2({
                    allowClear: true,
                    placeholder: placeholder,
                    containerCssClass: 'select2-choices-pills-close',
                    closeOnSelect: false,
                    width: '100%',
                });
            } else {
                this.$(dropDown).select2({
                    allowClear: true,
                    placeholder: placeholder,
                    width: '100%',
                });
            }
        }
    },

    /**
     * Hides custom date.
     */
    hideCustomDate: function() {
        this.$('.customDate').hide();
    },

    /**
     * Changes the directive view.
     * @param {Event} evt
     */
    changeDirective: function(evt) {
        this.resetDirectiveValues();
        this.initTag();

        this.currentDirective = evt.target.value;

        for (let directive of this.directives) {
            if (directive.value === this.currentDirective) {
                directive.selected = true;
            } else {
                directive.selected = false;
            }
        }

        this.tag.setName(this.currentDirective);

        this.render();
    },

    /**
     * Initializes the tag.
     */
    initTag: function() {
        let tagBuilderFactory = new App.utils.DocumentMerge.TagBuilderFactory();
        let tagBuilder = tagBuilderFactory.getTagBuilder('directive');
        let tag = tagBuilder.newTag().get();
        this.tag = tag;
    },

    /**
     * Applies the attribute to the tag.
     *
     * @param {Event} evt
     */
    applyAttribute: function(evt) {
        let field = evt.target || evt[0];
        const inputType = field.type;
        let inputValue = this.$(field).val();
        const inputName = this.$(field).attr('name');

        if (inputType === 'checkbox') {
            inputValue = field.checked;
        }

        if (inputName === 'sort') {
            let fieldToSortBy = this.$('select.sortByRelationshipFields').val();
            let sortBy = this.$('select.sortBy').val();
            inputValue = fieldToSortBy + ':' + sortBy;
        }

        option = {};
        option[inputName] = inputValue;
        this.tag.setAttribute(option);

        if (!inputValue || inputValue.length === 0) {
            this.tag.removeAttribute(inputName);
        }

        let tagValue = this.tag.compile().getTagValue();
        this.$('.preview').text(tagValue);
    },

    /**
     * Changes the custom date input visibility.
     * @param {Event} evt
     */
    toggleDateOption: function(evt) {
        let checkedStatus = evt.currentTarget.checked;
        let customDate = this.$('.customDate');

        if (checkedStatus) {
            customDate.show();
            this.toggleFormatSelects(true);
        } else {
            customDate.hide();
            this.toggleFormatSelects(false);
            this.clearCustomValues();
        }
    },

    /**
     * When choosing the custom option, we disable the format selects for date
     *
     * @param {bool} status
     */
    toggleFormatSelects(status) {
        this.$('.dateFormatSelect').attr('disabled', status);
    },

    /**
     * Clears the custom date input.
     */
    clearCustomValues: function() {
        let customValues = this.$('.customDateValue');
        customValues.val('');
        customValues.trigger('change');
    },

    /**
     * Updates the relationships dropdown when the module gets changed.
     * @param {app.Context} context
     * @param {string} module
     */
    updateRelationships: function(context, module) {
        if (!module) {
            return [];
        }

        this.currentModule = module;
        const moduleMeta = app.metadata.getModule(module) || {};

        const relationships = _.filter(moduleMeta.fields, function(field) {
            return field.type === 'link' && field.link_type !== 'one';
        });

        this.moduleRelationships = _.map(relationships, _.bind(function(relationship) {
            return {
                name: relationship.name,
                relationshipName: relationship.relationship,
                module: relationship.module || this.getRelationshipModule(relationship.relationship, module),
                moduleLabelTranslation: app.lang.getModuleName(
                    relationship.module || this.getRelationshipModule(relationship.relationship, module)),
            };
        }, this));

        this.render();
    },

    /**
     * Gets the relationship's module when it's not defined in the relationship.
     * @param {string} relationship
     * @param {string} module
     */
    getRelationshipModule: function(relationship, module) {
        let relationshipMeta = app.metadata.getRelationship(relationship);

        if (!relationshipMeta) {
            return null;
        }

        return relationshipMeta.rhs_module !== module ? relationshipMeta.rhs_module : relationshipMeta.lhs_module;
    },

    /**
     * Updates the relationship fields list.
     *
     * @param {Event} evt
     */
    updateRelationshipFieldsList: function(evt) {
        this.resetDirectiveValues();
        let fields = [];
        const module = this.$(evt.target).find(':selected').attr('module');
        const moduleMeta = app.metadata.getModule(module);

        _.find(this.moduleRelationships, _.bind(function(relationship) {
            if (relationship.selected === true) {
                relationship.selected = false;
            }

            if (relationship.module === module) {
                relationship.selected = true;
            }
        }), this);

        if (moduleMeta) {
            let mappedFields = _.map(moduleMeta.fields, function(field) {
                let label = app.lang.get(field.vname, module);

                return {
                    name: field.name,
                    label: label || field.name,
                    type: field.type,
                    module: field.type === 'relate' ? field.module : module
                };
            });

            fields = _.filter(mappedFields, function(field) {
                return field.type !== 'link' && field.type !== 'id' && field.name &&
                    typeof field.name === 'string' && field.name.length > 0;
            });
        }

        this.relationshipFields = fields;

        this.render();
    },

    /**
     * Resets the relationship fields when changing directive.
     */
    resetDirectiveValues: function() {
        this.headerFields = [];
        this.resetSelected();
        this.relationshipFields = [];
    },

    /**
     * Updates the table header.
     * @param {Event} evt
     */
    updateTableHeader: function(evt) {
        this.cocatRelationshipFields = '';
        this.$('.tableHeader').attr('readonly', false);

        if (evt.added) {
            this.addRelationshipField(evt.added);
        }

        if (evt.removed) {
            this.removeRelationshipField(evt.removed);
        }

        _.each(this.headerFields, _.bind(function(field) {
            if (_.isEmptyValue(this.cocatRelationshipFields)) {
                this.cocatRelationshipFields = field.label;
            } else {
                this.cocatRelationshipFields = this.cocatRelationshipFields.concat(',', field.label);
            }
        }, this));

        let tableHeader = this.$('.tableHeader');
        let tableHeaderName = tableHeader.attr('name');
        tableHeader.html(this.cocatRelationshipFields);

        if (this.cocatRelationshipFields.length === 0) {
            this.tag.removeAttribute(tableHeaderName);
        } else {
            let option = {};
            option[tableHeaderName] = this.cocatRelationshipFields;
            this.tag.setAttribute(option);
        }

        let tagValue = this.tag.compile().getTagValue();
        this.$('.preview').html(tagValue);
    },

    /**
     * Adds the field label to the header.
     * @param {Object} field
     */
    addRelationshipField: function(field) {
        let fieldLabel = this.$(field.element).attr('label');
        let addedField = {
            id: field.id,
            label: fieldLabel
        };

        this.headerFields.push(addedField);
    },

    /**
     * Remove the field label from the header.
     * @param {Object} field
     */
    removeRelationshipField: function(field) {
        this.headerFields = _.filter(this.headerFields, function(headerField) {
            return headerField.id !== field.id;
        });
    },

    /**
     * Resets the selected relationship when changing directives.
     */
    resetSelected: function() {
        _.find(this.moduleRelationships, function(relationship) {
            relationship.selected = false;
        });
    }
});
