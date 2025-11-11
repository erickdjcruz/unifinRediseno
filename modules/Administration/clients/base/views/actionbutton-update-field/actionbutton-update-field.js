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
 * Update Field action configuration view
 *
 * @class View.Views.Base.AdministrationActionbuttonUpdateFieldView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonUpdateFieldView
 * @extends View.View
 */
({
    events: {
        'change input[type="checkbox"][data-fieldname="calculated"]': 'calculatedChanged',
        'click [data-action="remove-field"]': 'removeField',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Quick initialization of field properties
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        options.fieldModule = options.fieldModule || 'Accounts';
        options.fieldName = options.fieldName || 'name';

        this._properties = {
            _isCalculated: options.isCalculated,
            _fieldName: options.fieldName,
            _value: options.value,
            _formula: options.formula,
        };

        if (this._properties._value === '') {
            this._properties._value = {};
        }

        this._fieldDef = app.metadata.getModule(options.fieldModule).fields[options.fieldName];
        this._fieldLabel = app.lang.get(this._fieldDef.vname, options.fieldModule);

        this._callback = options.callback;
        this._deleteCallback = options.deleteCallback;
        this._module = options.fieldModule;
    },

    /**
     * Property initialization, nothing to do for this view
     *
     */
    _initProperties: function() {
    },

    /**
     * Context event registration, nothing to do for this view
     *
     */
    _registerEvents: function() {
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // setup all the css we could not add via hbs
        this.$el.addClass('span6 ab-update-field-wrapper ' + this._properties._fieldName);

        this._createController();
    },

    /**
     * Remove field handler
     *
     * @param {UIEvent} e
     *
     */
    removeField: function(e) {
        if (this._deleteCallback) {
            this._deleteCallback(this._properties._fieldName);
        }
    },

    /**
     * Some basic validation of properties
     *
     * @return {bool}
     */
    canSave: function() {
        if (this._properties._isCalculated) {
            return this._controller.isValid();
        }

        return true;
    },

    /**
     * Handler for calculated URL checkbox selection
     *
     * @param {UIEvent} e
     *
     */
    calculatedChanged: function(e) {
        this._properties._isCalculated = e.currentTarget.checked;

        this._createController();

        if (this._callback) {
            this._callback(this._properties);
        }
    },

    /**
     * Event handler for whenever the formula changes.
     *
     * @param {UIEvent} e
     *
     */
    formulaChanged: function(data) {
        this._properties._formula = data;

        if (this._callback) {
            this._callback(this._properties);
        }
    },

    /**
     * Event handler for field value change
     *
     * @param {Object} data
     *
     */
    valueChanged: function(data) {
        _.each(data.changed, function storeData(fieldValue, fieldName) {
            this._properties._value[fieldName] = fieldValue;
        }, this);

        if (this._callback) {
            this._callback(this._properties);
        }
    },

    /**
     * Create field value sidecar component
     *
     */
    _createController: function() {
        var fieldContainer = this.$('div[data-container="field"]');
        fieldContainer.empty();

        if (this._properties._isCalculated) {
            // we simply create the formula builder field
            this._controller = app.view.createField({
                def: {
                    type: 'formula-builder',
                    name: 'ABCustomAction'
                },
                view: this,
                viewName: 'edit',
                targetModule: this._module,
                callback: _.bind(this.formulaChanged, this),
                formula: this._properties._formula,
                matchField: this._properties._fieldName
            });
        } else {
            // get the field meta
            var moduleMeta = app.metadata.getModule(this._module);
            if (_.isEmpty(moduleMeta) || _.isEmpty(moduleMeta.fields)) {
                return;
            }

            var fieldsMeta = moduleMeta.fields;
            var fieldMeta = fieldsMeta[this._properties._fieldName];
            var fieldDef = app.utils.deepCopy(fieldMeta);
            var controllerModel = app.data.createBean(this._module);

            // populate the model with all the data needed
            controllerModel.set(this._properties._value);
            controllerModel.on('change', _.bind(this.valueChanged, this));

            // if we have a link type field we have to set it's type to relate
            if (fieldDef.type === 'link') {
                if (!fieldDef.module) {
                    _.each(fieldsMeta, function getValidDef(def) {
                        if (def.link === fieldDef.name) {
                            fieldDef = def;
                        }
                    });
                }

                if (fieldDef.module) {
                    fieldDef.type = 'relate';
                }
            } else if (fieldDef.type === 'multienum') {
                fieldDef.type = 'enum';
            } else if (fieldDef.type === 'text') {
                fieldDef.type = 'textarea';
            } else if (fieldDef.type === 'html') {
                fieldDef.type = 'htmleditable_tinymce';
            }

            fieldDef.required = false;

            // simply create a controller matching the field metadata
            this._controller = app.view.createField({
                def: fieldDef,
                view: this,
                layout: this.layout,
                viewName: 'edit',
                model: controllerModel
            });
        }

        if (this.layout && this.layout.layout) {
            this.layout = this.layout.layout;
        }

        this._controller.render();

        fieldContainer.append(this._controller.$el);
    },

    /**
     * Remove the current field value sidecar component
     * Can be either a FormulaBuilder field for calculated values,
     * or any sugar field type field for a static value
     */
    _disposeField: function() {
        if (this._controller) {
            // we have to force the _hasDatePicker to false in order to avoid
            // an issue in the date/timecombo dispose method
            // _dispose: function() {
            //     // FIXME: new date picker versions have support for plugin removal/destroy
            //     // we should do the upgrade in order to prevent memory leaks

            //     if (this._hasDatePicker) {
            //         $(window).off('resize', this.$(this.fieldTag).data('datepicker').place);
            //     }

            //     this._super('_dispose');
            // }
            // As it happens, it goes off to error when trying to access the place property, because
            // the .data('datepicker') call returns undefined
            this._controller._hasDatePicker = false;

            this._controller.dispose();

            this._controller = null;
        }
    },

    /**
     * @inheritdoc
     *
     */
    _dispose: function() {
        this._disposeField();

        this._super('_dispose');
    },
});
