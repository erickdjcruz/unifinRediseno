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
 * Update Record action configuration view
 *
 * @class View.Views.Base.AdministrationActionbuttonUpdateRecordView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonUpdateRecordView
 * @extends View.View
 */
({
    /**
     * Fields which should not be available to automatically update
     */
    badFields: [
        'deleted',
        'team_count',
        'account_description',
        'opportunity_role_id',
        'opportunity_role_fields',
        'opportunity_role',
        'email_and_name1',
        'dnb_principal_id',
        'email1',
        'email2',
        'email_addresses',
        'email_addresses_non_primary',
        'email_addresses_primary',
        'email_and_name1',
        'primary_address_street_2',
        'primary_address_street_3',
        'alt_address_street_2',
        'alt_address_street_3',
        'portal_app',
        'portal_user_company_name',
        'mkto_sync',
        'mkto_id',
        'mkto_lead_score',
        'cookie_consent',
        'cookie_consent_received_on',
        'dp_consent_last_updated',
        'accept_status_id',
        'sync_key',
        'locked_fields',
        'billing_address_street_2',
        'billing_address_street_3',
        'billing_address_street_4',
        'shipping_address_street_2',
        'shipping_address_street_3',
        'shipping_address_street_4',
        'related_languages',
    ],

    /**
     * Field types which should not be available to automatically update
     */
    badFieldTypes: [
        'link',
        'id',
        'collection',
        'widget',
        'html',
        'htmleditable_tinymce',
        'image',
        'teamset',
        'team_list',
        'email',
        'password',
        'file'
    ],

    /**
     * Event listeners
     */
    events: {
        'change input[type="checkbox"][data-fieldname="auto-save"]': 'autoSaveFlagChanged',
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
     * Quick initialization of action properties
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        var ctxModel = options.context.get('model');

        this._actionId = options.actionId;
        this._buttonId = options.buttonId;
        this._module = ctxModel.get('module');
        this._fields = {};

        if (options.actionData && options.actionData.properties &&
            Object.keys(options.actionData.properties).length !== 0) {
            this._properties = options.actionData.properties;
        } else {
            this._properties = {
                fieldsToBeUpdated: {},
                autoSave: false,
            };
        }

        this._properties.autoSave = app.utils.isTruthy(this._properties.autoSave);
        this._populateFields(this._module);
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
     * Updates the currently selected module fields with anything that can be updated
     *
     * @param {string} module
     *
     */
    _populateFields: function(module) {
        var fields = _.chain(app.metadata.getModule(module).fields).values();
        fields = fields.filter(
            function filterField(field) {
                return (
                    !_.isEmpty(field.name) &&
                    !_.isEmpty(field.vname) &&
                    !_.contains(this.badFields, field.name) &&
                    !_.contains(this.badFieldTypes, field.type) &&
                    field.studio !== false &&
                    field.readonly !== true &&
                    field.calculated !== true
                );
            }, this
        ).map(function fieldToTuple(field) {
            return [field.name, app.lang.get(field.vname, module)];
        }).value();

        this._fields = _.object(fields);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createSelect2(_.bind(this._addUpdateField, this));
        this._createExistingControllers();
    },

    /**
     * View setup, nothing to do for this view
     *
     */
    setup: function() {
    },

    /**
     * Update action configuration
     *
     * @param {Object} data
     *
     */
    updateProperties: function(data) {
        this._properties.fieldsToBeUpdated[data._fieldName] = {
            fieldName: data._fieldName,
            isCalculated: data._isCalculated,
            formula: data._formula,
            value: data._value
        };

        this._updateActionProperties();
    },

    /**
     * Return action configuration
     *
     * @return {Object}
     */
    getProperties: function() {
        return this._properties;
    },

    /**
     * Handler for autosave checkbox change event
     *
     * @param {UIEvent} e
     *
     */
    autoSaveFlagChanged: function(e) {
        this._properties.autoSave = e.currentTarget.checked;
        this._updateActionProperties();
    },

    /**
     * Create field value sidecar components for configured fields
     *
     */
    _createExistingControllers: function() {
        _.each(this._properties.fieldsToBeUpdated, function create(data, name) {
            this._createUpdateFieldController(data);
        }, this);
    },

    /**
     * Create the select2 field selector control
     *
     * @param {Function} callback
     *
     */
    _createSelect2: function(callback) {
        this.$field = this.$('[data-fieldname="field"]')
            .select2(this._getSelect2Options())
            .data('select2');

        this.$field.onSelect = (function select(fn) {
            return function returnCallback(data, options) {
                if (callback) {
                    callback(data);
                }

                // after each select we set the default label
                if (arguments) {
                    arguments[0] = {
                        id: 'select',
                        text: app.lang.get('LBL_ACTIONBUTTON_SELECT_FIELD')
                    };
                }

                return fn.apply(this, arguments);
            };
        })(this.$field.onSelect);
    },

    /**
     * Create generic Select2 options object
     *
     * @param {string} queryFunc
     *
     * @return {Object}
     */
    _getSelect2Options: function() {
        var select2Options = {};

        select2Options.placeholder = app.lang.get('LBL_ACTIONBUTTON_SELECT_FIELD');
        select2Options.query = _.bind(this._queryFields, this);
        select2Options.dropdownAutoWidth = true;

        return select2Options;
    },

    /**
     * Wrapper for querying fields for select2 components
     *
     * @param {string} query
     *
     */
    _queryFields: function(query) {
        this._query(query, '_fields');
    },

    /**
     * Wrapper for querying functions for select2 components
     *
     * @param {string} query
     *
     */
    _query: function(query, options) {
        var listElements = this[options];
        var data = {
            results: [],
            more: false
        };

        if (_.isObject(listElements)) {
            _.each(listElements, function pushValidResults(element, index) {
                if (query.matcher(query.term, element)) {
                    data.results.push({id: index, text: element});
                }
            });
        } else {
            listElements = null;
        }

        query.callback(data);
    },

    /**
     * Adds a new field update setting
     *
     * @param {Object} data
     *
     */
    _addUpdateField: function(data) {
        this._properties.fieldsToBeUpdated[data.id] = {
            fieldName: data.id,
            isCalculated: false,
            formula: '',
            value: ''
        };

        this._updateActionProperties();
        this._createUpdateFieldController(this._properties.fieldsToBeUpdated[data.id]);
    },

    /**
     * Removes a field update setting
     *
     * @param {string} fieldId
     *
     */
    removeUpdateField: function(fieldId) {
        // remove from data
        delete this._properties.fieldsToBeUpdated[fieldId];

        this._updateActionProperties();

        this.disposeField(fieldId);
    },

    /**
     * Create field value edit component
     *
     * @param {Object} fieldData
     *
     */
    _createUpdateFieldController: function(fieldData) {
        this.$('.' + fieldData.fieldName).remove();

        var updateFieldController = app.view.createView({
            name: 'actionbutton-update-field',
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            isCalculated: fieldData.isCalculated,
            fieldName: fieldData.fieldName,
            value: fieldData.value,
            formula: fieldData.formula,
            fieldModule: this._module,
            deleteCallback: _.bind(this.removeUpdateField, this),
            callback: _.bind(this.updateProperties, this)
        });

        this.$('div[data-container="fields"]').prepend(updateFieldController.$el);

        updateFieldController.render();

        if (!this._subComponents) {
            this._subComponents = [];
        }

        this._subComponents.push(updateFieldController);
    },

    /**
     * Update action properties in context
     *
     */
    _updateActionProperties: function() {
        var ctxModel = this.context.get('model');
        var buttonsData = ctxModel.get('data');
        buttonsData.buttons[this._buttonId].actions[this._actionId].properties = this._properties;

        // update action data into the main data container
        ctxModel.set('data', buttonsData);
    },

    /**
     * Dipose and remove a given field from the record update list
     *
     * @param {string} fieldId
     *
     */
    disposeField: function(fieldId) {
        var field = _.find(this._subComponents, function checkField(controller, index) {
            if (controller && controller._properties) {
                return controller._properties._fieldName === fieldId;
            }

            return false;
        });

        if (field) {
            this._subComponents = _.chain(this._subComponents).without(field).value();
            field.dispose();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        _.each(this._subComponents, function(component) {
            component.dispose();
        });

        this._super('_dispose');
    },
});
