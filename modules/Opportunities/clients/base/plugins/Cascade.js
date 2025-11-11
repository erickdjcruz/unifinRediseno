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

(function(app) {
    app.events.on('app:init', function() {
        /**
         * The Cascade plugin is used for Opportunity fields
         */
        app.plugins.register('Cascade', ['field'], {
            baseFieldName: null,
            baseFieldType: null,
            fieldNames: null,
            field: null,
            model: null,
            attachAction: null,
            readOnlyProp: null,

            createActions: ['create', 'create-nodupecheck'],
            editableActions: ['edit', 'create', 'create-nodupecheck'],

            /**
             * Set the appropriate field attribute for Opps+RLIs to handle
             * rendering the checkbox.
             *
             * Wrap "setMode" as it handles changing the field from detail to
             * edit modes. It now will also handle binding enable/disable
             * listeners to the checkbox.
             *
             * Listen to model.change events on this field, and set our model's
             * _cascade attribute.
             * @param component
             * @param plugin
             */
            onAttach: function(field, plugin) {
                // If we aren't using RLIs, there's no need for cascading
                let oppConfig = app.metadata.getModule('Opportunities', 'config');
                if (!oppConfig || oppConfig.opps_view_by !== 'RevenueLineItems') {
                    field.displayCheckbox = false;
                    return;
                }

                this._initPluginProperties(field);
                this._initModelProperties();
                this._initViewProperties();
                this._initFieldProperties();
                this._initDefaultValues();
                this._initListeners();
                this.view.cascadeFields[this.baseFieldName].initialized = true;
            },

            /**
             * Sets any needed properties on this
             *
             * @param {Field} field the field component this plugin is attaching to
             * @private
             */
            _initPluginProperties: function(field) {
                this.field = field;
                this.view = this.field.options.view;
                this.model = this.field.options.model;

                this.baseFieldName = this.field.options.def.name;
                this.baseFieldType = this.field.options.def.type;
                this.attachAction = this.field.action;
                this.hasCascadeError = false;

                if (this.baseFieldType === 'fieldset-cascade') {
                    this.fieldNames = _.map(this.field.options.def.fields, function(field) {
                        return field.name;
                    });
                } else {
                    this.fieldNames = [this.field.baseFieldName];
                }
            },

            /**
             * Sets any needed properties on the field's model
             *
             * @private
             */
            _initModelProperties: function() {
                if (this.createActions.includes(this.field.options.view.action)) {
                    this.cascadeValidationTask = `cascade_${this.field.name}_${this.field.cid}`;
                    this.model.addValidationTask(
                        this.cascadeValidationTask,
                        _.bind(this._validateCascadeOnCreate, this)
                    );
                }
            },

            /**
             * Sets any needed properties on the field's view
             *
             * @private
             */
            _initViewProperties: function() {
                // If the view re-renders, the field this plugin is attached to
                // will re-initialize, meaning this plugin will re-initialize
                // as well. That means we need to store persistent data on the
                // view to keep track of the state of things for this plugin
                this.view.cascadeFields = this.view.cascadeFields || {};
                this.view.cascadeFields[this.baseFieldName] = this.view.cascadeFields[this.baseFieldName] || {
                    initialized: false
                };
            },

            /**
             * Sets any needed properties on the field
             *
             * @private
             */
            _initFieldProperties: function() {
                // If this field is on the Leads convert view without RLIs enabled, keep it disabled
                // and hide the checkbox
                if (this._isOnLeadConvert() && !this._isOnLeadConvertWithProperty('enableRlis')) {
                    this.field.displayCheckbox = false;
                    this.field.action = 'disabled';
                    return;
                }
                this.field.displayCheckbox = true;
                this.field.lblString = this.getCheckboxLabel();

                // Wrap field methods
                this.field.setMode = _.wrap(this.field.setMode, _.bind(function(setMode, args) {
                    setMode.call(this.field, args);
                    this.handleModeChange(args);
                }, this));
                this.field._render = _.wrap(this.field._render, _.bind(function(_render, args) {
                    this._beforeRender();
                    _render.call(this.field, args);
                    this._afterRender();
                }, this));
            },

            /**
             * Initializes the field's values to '', and stores their original
             * default values for use later when the field is enabled
             *
             * @private
             */
            _initDefaultValues: function() {
                if (!this.view.cascadeFields[this.baseFieldName].initialized &&
                    this.createActions.includes(this.field.options.view.action)) {
                    this.view.cascadeFields[this.baseFieldName].defaultValues = {};
                    let valuesToSet = {};
                    this.fieldNames.forEach(fieldName => {
                        let defaultValue = this.model.get(fieldName);
                        this.view.cascadeFields[this.baseFieldName].defaultValues[fieldName] = defaultValue;
                        valuesToSet[fieldName] = '';
                    });
                    this.model.set(valuesToSet, {hideDbvWarning: true});
                }
            },

            /**
             * Sets up any listeners this plugin uses
             *
             * @private
             */
            _initListeners: function() {
                _.each(this.fieldNames, function(fieldName) {
                    this.listenTo(this.model, 'change:' + fieldName, this.setCascadeValue, this);
                }, this);
                this.listenTo(this.model, 'sync', this.clearCascadeValue, this);

                let hasDisableConditions = this.options && this.options.def && this.options.def.disable_field;
                if (hasDisableConditions && !this.createActions.includes(this.options.view.name)) {
                    let disableFieldName = this.options.def.disable_field;
                    // Adding listeners to all the fields that tend to calculate value to disable a field
                    // For example: we need to listen on total and closed RLI fields to calculate the open RLIs
                    // (open RLI = total - closed RLIs)
                    // and disable sales_stage and date_closed.
                    if (_.isArray(disableFieldName)) {
                        _.each(disableFieldName, function(fieldName) {
                            this.listenTo(this.model, 'change:' + fieldName, this.handleReadOnly, this);
                        }, this);
                    } else {
                        this.listenTo(this.model, 'change:' + disableFieldName, this.handleReadOnly, this);
                    }
                }

                this.listenTo(this.field, 'editable:toggle-field', this._handleEditableFieldToggled, this);
                this.listenTo(this.view, 'record:edit:cancel', this._handleEditableFieldToggled, this);
                this.listenTo(this.view, 'editable:toggle_fields', this._handleEditableFieldsToggled, this);
            },

            /**
             * When the field this plugin is attached to is toggled back into
             * non-edit mode, clear its cascade properties so that the next
             * time it enters edit mode, the user must check the checkbox
             * again
             *
             * @param {string} viewAction the view's current action state
             * @private
             */
            _handleEditableFieldToggled: function(viewAction) {
                if (!this.editableActions.includes(viewAction)) {
                    this.model.unset(`${this.baseFieldName}_cascade_checked`);
                    this.clearCascadeValue();
                }
            },

            /**
             * When a view that uses the Editable plugin (record, flex list,
             * etc.) toggles a set of fields to non-edit mode, reset the
             * cascade flag on this field if it is included in that list
             *
             * @param {Array} fieldsToggled the list of toggled field objects
             * @param {string} viewAction the view's current action state
             * @private
             */
            _handleEditableFieldsToggled: function(fieldsToggled, viewAction) {
                if (!this.editableActions.includes(viewAction)) {
                    _.each(fieldsToggled, function(field) {
                        if (field.name === this.baseFieldName) {
                            this._handleEditableFieldToggled(viewAction);
                        }
                    }, this);
                }
            },

            /**
             * Handles setting properties before the field renders
             *
             * @private
             */
            _beforeRender: function() {
                let checkboxChecked = this.model.get(this.baseFieldName + '_cascade_checked') || false;
                let fieldHasErrors = !_.isEmpty(this.field._errors) || this.hasCascadeError;

                // Make sure that the field is enabled/disabled as needed, and
                // that the checkbox is enabled/disabled/checked as needed
                if ((this.readOnlyProp || !checkboxChecked) && !fieldHasErrors) {
                    // Disable the field, and make sure its action is properly
                    // set
                    let oldAction = this.field._previousAction || this.field.action;
                    if (oldAction !== undefined || !this.field.view || this.field.view.name !== 'multi-line-list') {
                        this.field._removeViewClass(oldAction);
                        this.field._previousAction = oldAction;
                        this.field.action = 'disabled';
                    }

                    // Uncheck the checkbox, and disable it if needed
                    this.field.shouldCascade = false;
                    this.field.cascadeCheckboxDisabled = this.readOnlyProp;
                } else {
                    // Check/uncheck the checkbox based on its model value
                    // and enable it
                    this.field.shouldCascade = checkboxChecked;
                    this.field.cascadeCheckboxDisabled = false;

                    if (_.isUndefined(this.field.action)) {
                        this.field.action = this.field.options.view.action;
                    }
                }

                // Hide the checkbox if the Opp is being created in a context where the RLI subpanel is
                // unavailable (search and select create, for example). We do this check during render as
                // the context is unavailable when the plugin attaches to the field.
                let isCreate = this.createActions.includes(this.field.options.view.action);
                let isLeadConvertWithOptionalRlis = this._isOnLeadConvert() &&
                    !this._isOnLeadConvertWithProperty('requireRlis');
                if (isCreate &&
                    this.field.displayCheckbox &&
                    (this._hasNoRliCollection() && !isLeadConvertWithOptionalRlis)
                ) {
                    this.field.displayCheckbox = false;
                }
            },

            /**
             * Handles actions needed after the field renders
             *
             * @private
             */
            _afterRender: function() {
                this.bindEditActions();
            },

            /**
             * If we're in edit or create mode, bind our event listeners to the checkbox.
             *
             * Otherwise, make sure the field is enabled so clicking it or
             * entering edit mode will display the checkbox.
             * @param toTemplate
             */
            handleModeChange: function(toTemplate) {
                if (!this.field.$el) {
                    return;
                }
                var action = toTemplate || this.field.action || this.field.view.action || 'detail';
                if (this.editableActions.includes(action)) {
                    this.handleReadOnly();
                } else {
                    this.field.setDisabled(false, {trigger: true});
                }
            },

            /**
             * Bind a "click" listener to the checkbox. This is done using
             * jQuery because this checkbox exists only in our template and not
             * as a field on our model.
             */
            bindEditActions: function() {
                // If the plugin attached directly on edit or create mode (e.g. if the user refreshed
                // while on the edit view), the checkbox won't have been rendered yet. Make
                // sure it is available to attach the listener to.
                if (this.editableActions.includes(this.attachAction)) {
                    this.attachAction = null;
                    this.field.render();
                }

                var checkbox = this.field.$el.find('input[type=checkbox]');
                var self = this;
                checkbox.click(function() {
                    if (this.checked === false) {
                        self.model.unset(self.field.name + '_cascade_checked');
                        self.field.setDisabled(true, {trigger: true});
                        self.resetModelValue();
                        self.model.trigger('cascade:checked:' + self.field.name, false);
                    } else {
                        self.model.set(self.field.name + '_cascade_checked', true);
                        self.field.setDisabled(false, {trigger: true});
                        self.setDefaultValue();
                        self.setCascadeValue();
                        self.model.trigger('cascade:checked:' + self.field.name, true);
                    }
                });
            },

            /**
             * Determines whether the field should be set to readonly or not,
             * and re-renders the field
             */
            handleReadOnly: function() {
                let hasDisableConditions = this.options && this.options.def && this.options.def.disable_field;
                if (this.model && hasDisableConditions && !this.createActions.includes(this.options.view.name)) {
                    let disableFieldName = this.options.def.disable_field;
                    let calculatedValue = null;
                    // When disableFieldName is an array, calculatedValue is fieldValue1 - fieldValue2
                    // For example: we need to calculate all open RLIs. fieldValue1 = total, fieldValue2 = closed RLI
                    // (open RLI = total - closed RLIs)
                    // and disable sales_stage and date_closed.
                    if (_.isArray(disableFieldName)) {
                        let fieldValue1 = this.model.get(disableFieldName[0]);
                        let fieldValue2 = this.model.get(disableFieldName[1]);
                        // if either fieldValue1 or fieldValue2 is undefined set calculatedValue to null
                        calculatedValue = (!_.isUndefined(fieldValue1) && !_.isUndefined(fieldValue2)) ?
                            (fieldValue1 - fieldValue2) : null;
                    } else if (typeof disableFieldName === 'string') {
                        var disableFieldValue = this.model.get(disableFieldName);
                        calculatedValue = !_.isUndefined(disableFieldValue) ? disableFieldValue : null;
                    }

                    if (calculatedValue !== null) {
                        if (!_.isUndefined(this.field.def) && _.isUndefined(this.field.def.readOnlyProp)) {
                            this.field.def.readOnlyProp = false;
                        }

                        let setReadOnly = calculatedValue <= 0;
                        if (this.options.def.disable_positive) {
                            setReadOnly = calculatedValue > 0;
                        }

                        this.readOnlyProp = setReadOnly || this.field.def.readOnlyProp;
                    }
                }

                this.render();
            },

            /**
             * Util function to reset model to synced values and stop any cascades.
             * Used when un-checking the checkbox in edit mode.
             */
            resetModelValue: function() {
                _.each(this.fieldNames, function(fieldName) {
                    this.model.set(fieldName, this.model.getSynced(fieldName) || '');
                    this.model.unset(`${fieldName}_cascade`);
                    this.setRliValueForField(fieldName, '');
                }, this);
            },

            /**
             * When on create view, if the field has a default value, set that when
             * the checkbox is checked
             */
            setDefaultValue: function() {
                let defaultValues = this.view.cascadeFields[this.baseFieldName].defaultValues;
                if (!this.createActions.includes(this.options.view.name) || _.isEmpty(defaultValues)) {
                    return;
                }

                let valuesToSet = {};
                this.fieldNames.forEach(fieldName => {
                    if (!_.isUndefined(defaultValues[fieldName])) {
                        valuesToSet[fieldName] = defaultValues[fieldName];
                    } else {
                        valuesToSet[fieldName] = '';
                    }
                });
                this.model.set(valuesToSet);
            },

            /**
             * Called on model.change events for our field. This sets the model
             * property needed to cause cascading changes.
             */
            setCascadeValue: function() {
                // Do not cascade the field's value if the change was not the result of a user
                // directly editing the field and/or the cascade checkbox is unchecked
                if (!this.editableActions.includes(this.action)) {
                    return;
                }
                _.each(this.fieldNames, function(fieldName) {
                    let fieldValue = this.model.get(fieldName);
                    this.model.set(fieldName + '_cascade', fieldValue);
                    this.setRliValueForField(fieldName, fieldValue);
                }, this);
            },

            /**
             * Gets the RLI collection for the Opp
             * @return {*}
             * @private
             */
            _getRliCollection: function() {
                let rliContext = this.context.getChildContext({link: 'revenuelineitems'});
                rliContext.prepare();
                return rliContext.get('collection');
            },

            /**
             * For create view, sets the value of the given field for all child RLIs, when applicable
             * @param fieldName
             * @param fieldValue
             */
            setRliValueForField: function(fieldName, fieldValue) {
                if (!this.createActions.includes(this.options.view.name)) {
                    return;
                }

                let rliCollection = this._getRliCollection();
                if (_.isEmpty(rliCollection.models)) {
                    return;
                }

                rliCollection.models.forEach(model => {
                    if (app.utils.isRliFieldValidForCascade(model, fieldName)) {
                        model.set(fieldName, fieldValue);
                    }
                });
            },

            /**
             * Clear cascade field
             */
            clearCascadeValue: function() {
                if (this.context && this.context.get('layout')  && this.context.get('layout') === 'record') {
                    _.each(this.fieldNames, function(fieldName) {
                        this.model.unset(`${fieldName}_cascade`);
                        this.setRliValueForField(fieldName, '');
                    }, this);
                }
            },

            /**
             * Returns the correct checkbox label for the view
             * @return {string}
             */
            getCheckboxLabel: function() {
                let labelKey = this.createActions.includes(this.field.options.view.action) ?
                    'LBL_CASCADE_RLI_CREATE' :
                    'LBL_CASCADE_RLI_EDIT';
                return app.lang.get(labelKey, 'Opportunities');
            },

            /**
             * Checks if the field is on the leads convert view
             * @return {boolean}
             * @private
             */
            _isOnLeadConvert: function() {
                return this.field.options &&
                    this.field.options.context &&
                    this.field.options.context.parent &&
                    this.field.options.context.parent.get('convertModuleList');
            },

            /**
             * Checks if the field is on the leads convert view and the specified lead convert
             * RLI property is set
             * @param {string} property property name to check for - ex: 'enableRlis'
             * @return {boolean}
             * @private
             */
            _isOnLeadConvertWithProperty: function(property) {
                if (!this._isOnLeadConvert()) {
                    return false;
                }

                let convertModuleList = this.field.options.context.parent.get('convertModuleList');
                return convertModuleList.find(moduleMeta => moduleMeta.id === 'Opportunities')[property];
            },

            /**
             * Checks if the Opp has any RLIs in its collection. This will be false when creating an Opp
             * from search and select or other contexts in which the RLI subpanel does not exist.
             * @return {boolean}
             * @private
             */
            _hasNoRliCollection: function() {
                let rliCollection = this._getRliCollection();
                return _.isEmpty(rliCollection) || rliCollection.length === 0;
            },

            /**
             * Validate that required cascade fields are provided
             * @param {Object} fields
             * @param {Object} errors
             * @param {Function} callback
             * @private
             */
            _validateCascadeOnCreate: function(fields, errors, callback) {
                this.hasCascadeError = false;

                let isChecked = this.model.get(`${this.name}_cascade_checked`);
                let rliCollection = this._getRliCollection();
                let fieldValues = this.fieldNames.map(field => this.model.get(field));
                let isEmpty = fieldValues.some(value => !value);

                // Cascade fields should be marked as required if the checkbox is checked, RLIs exist,
                // the cascade field has an empty value, and at least one RLI is valid to cascade to
                // for that field
                if (isChecked && !_.isEmpty(rliCollection) && isEmpty) {

                    // For most fields this only runs through once, but for fieldset-type fields
                    // like service duration we need to check each subfield
                    this.fieldNames.forEach(field => {
                        if (this.model.get(field)) {
                            return;
                        }

                        if (rliCollection.models.some(model => app.utils.isRliFieldValidForCascade(model, field))) {
                            errors[field] = errors[field] || {};
                            errors[field].required = true;
                            this.hasCascadeError = true;
                        }
                    });
                }

                callback(null, fields, errors);
            },

            /**
             * Performs cleanup needed when the field is disposed and/or
             * the plugin is detached
             */
            onDetach: function() {
                // Remove listeners set up by this plugin
                this.stopListening(null, null, this.setCascadeValue);
                this.stopListening(null, null, this.clearCascadeValue);
                this.stopListening(null, null, this.handleReadOnly);
                this.stopListening(null, null, this._handleEditableFieldToggled);
                this.stopListening(null, null, this._handleEditableFieldsToggled);

                // Remove the validation set up on the model by this plugin
                this.model.removeValidationTask(this.cascadeValidationTask);
            }
        });
    });
})(SUGAR.App);
