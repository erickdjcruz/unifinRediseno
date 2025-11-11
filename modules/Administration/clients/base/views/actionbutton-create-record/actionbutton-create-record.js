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
 * Create Record action configuration view
 *
 * @class View.Views.Base.ActionbuttonCreateRecordView
 * @alias SUGAR.App.view.views.BaseActionbuttonCreateRecordView
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
        'change [data-fieldname=module]': 'moduleChanged',
        'change [data-fieldname=link]': 'linkChanged',
        'change input[type=checkbox][data-fieldname]': 'boolPropChanged',
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
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        var ctxModel = options.context.get('model');
        this._buttonId = options.buttonId;
        this._actionId = options.actionId;

        this._modules = {};
        _.each(ctxModel.get('modules'), function buildModulesList(moduleName) {
            this._modules[moduleName] = app.lang.getModuleName(moduleName, {
                plural: true
            });
        }, this);

        this._module = ctxModel.get('module');

        if (options.actionData &&
            options.actionData.properties &&
            Object.keys(options.actionData.properties).length !== 0) {

            this._properties = options.actionData.properties;
        } else {
            this._properties = {
                attributes: {},
                parentAttributes: {},
                module: 'Accounts',
                link: '',
                mustLinkRecord: false,
                copyFromParent: false,
                autoCreate: false
            };
        };

        this._properties.parentAttributes =
            _.omit(this._properties.parentAttributes, function filterEmptyParentAttributes(a) {
                return _.isEmpty(a.fieldName) && _.isEmpty(a.parentFieldName);
            });

        this._properties.mustLinkRecord = app.utils.isTruthy(this._properties.mustLinkRecord);
        this._properties.copyFromParent = app.utils.isTruthy(this._properties.copyFromParent);
        this._properties.autoCreate = app.utils.isTruthy(this._properties.autoCreate);

        this._populateRelationships();
        this._populateAttributes(this._properties.module);
        this._populateParentAttributes(this._module);
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

        this._select2 = null;
        this.select2('module', '_queryModules');
        this.select2('link', '_queryLinks');
        this.select2('attributes', '_queryAttributes', true, _.bind(this.addAttribute, this));
        this.select2('parent-attributes', '_queryAttributes', true, _.bind(this.addParentAttribute, this));

        if (this._properties.module !== '') {
            this.select2('module').data({
                id: this._properties.module,
                text: this._modules[this._properties.module]
            });
        }

        if (this._properties.link !== '') {
            this.select2('link').data({
                id: this._properties.link,
                text: this._properties.link
            });
        }

        this.$('[data-fieldname=must-link-record]').attr('disabled', _.isEmpty(this._links));

        this._createExistingControllers();
    },

    /**
     * View setup, nothing to do for this view
     *
     */
    setup: function() {
    },

    /**
     * Some basic validation of properties
     *
     */
    canSave: function() {
        if (this._properties.module === '') {
            app.alert.show('alert_actionbutton_create_nomodule', {
                level: 'error',
                title: app.lang.get('LBL_ACTIONBUTTON_INVALID_DATA'),
                messages: app.lang.get('LBL_ACTIONBUTTON_SELECT_MODULE'),
                autoClose: true,
                autoCloseDelay: 5000
            });

            return false;
        }

        if (this._properties.link === '' && this._properties.mustLinkRecord) {
            app.alert.show('alert_actionbutton_create_nolink', {
                level: 'error',
                title: app.lang.get('LBL_ACTIONBUTTON_INVALID_DATA'),
                messages: app.lang.get('LBL_ACTIONBUTTON_SELECT_LINK'),
                autoClose: true,
                autoCloseDelay: 5000
            });

            return false;
        }

        return true;
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
     * Add parent attribute
     *
     * @param {Object} data
     *
     */
    addParentAttribute: function(data) {
        this._addParentField(data.id);
    },

    /**
     * Add attribute
     *
     * @param {Object} data
     *
     */
    addAttribute: function(data) {
        this._addAttributesField(data.id);
    },

    /**
     * Model link field change event handler
     *
     * @param {UIEvent} e
     *
     */
    linkChanged: function(e) {
        var fieldname = $(e.currentTarget).data('fieldname');
        fieldname = this.kebabToCamelCase(fieldname);

        this._properties[fieldname] = e.currentTarget.value;
        this._updateActionProperties();
    },

    /**
     * Converts kebab-case to camelCase
     *
     * @param {string} str
     * @return {string}
     */
    kebabToCamelCase: function(str) {
        str = str.split('-')
            .map(function(a, i) {
                return i === 0 ? a : app.utils.capitalize(a);
            }).join('');

        return str;
    },

    /**
     * Event handler for checkboxes
     *
     * @param {UIEvent} e
     *
     */
    boolPropChanged: function(e) {
        var fieldname = $(e.currentTarget).data('fieldname');
        fieldname = this.kebabToCamelCase(fieldname);

        this._properties[fieldname] = e.currentTarget.checked;
        this._updateActionProperties();

        if (fieldname === 'mustLinkRecord') {
            this.render();
        }
    },

    /**
     * Event handler for module selection change
     *
     * @param {UIEvent} e
     *
     */
    moduleChanged: function(e) {
        var fieldname = $(e.currentTarget).data('fieldname');
        fieldname = this.kebabToCamelCase(fieldname);

        if (this._properties[fieldname] !== e.currentTarget.value) {
            this._properties = {
                attributes: {},
                parentAttributes: {},
                module: 'Accounts',
                link: '',
                mustLinkRecord: false,
                copyFromParent: false,
                autoCreate: false
            };
        }

        this._properties[fieldname] = e.currentTarget.value;

        this._updateActionProperties();
        this._populateRelationships();
        this._populateAttributes(this._properties.module);
        this._populateParentAttributes(this._module);

        this.render();
    },

    /**
     * Updates the currently selected module fields array with anything that can be updated
     *
     * @param {string} module
     */
    _populateAttributes: function(module) {
        var fields = _.chain(app.metadata.getModule(module).fields).values();
        fields = fields.filter(
            function filterField(field) {
                return (
                    !_.isEmpty(field.name) &&
                    !_.isEmpty(field.vname) &&
                    !_.contains(this.badFields, field.name) &&
                    !_.contains(this.badFieldTypes, field.type) &&
                    field.link_type !== 'relationship_info' &&
                    field.readonly !== true &&
                    field.calculated !== true
                );
            }, this
        ).map(function fieldToTuple(field) {
            return [field.name, app.lang.get(field.vname, module)];
        }).value();

        this._attributes = _.object(fields);
    },

    /**
     * Update the parent module fields array that can be copied over
     *
     * @param {string} module
     */
    _populateParentAttributes: function(module) {
        var fields = _.chain(app.metadata.getModule(module).fields).values();
        fields = fields.filter(
            function filterField(field) {
                return (
                    !_.isEmpty(field.name) &&
                    !_.isEmpty(field.vname) &&
                    this.badFields.indexOf(field.name) === -1 &&
                    this.badFieldTypes.indexOf(field.type) === -1 &&
                    field.link_type !== 'relationship_info' &&
                    field.studio !== false
                );
            }, this
        ).map(function fieldToTuple(field) {
            return [field.name, app.lang.get(field.vname, module)];
        }).value();

        this._parentAttributes = _.object(fields);
    },

    /**
     * Update action configuration
     *
     * @param {Object} data
     *
     */
    updateProperties: function(data) {
        this._properties.attributes[data._fieldName] = {
            fieldName: data._fieldName,
            isCalculated: data._isCalculated,
            formula: data._formula,
            value: data._value
        };

        this._updateActionProperties();
    },

    /**
     * Update parent field updates configuration
     *
     * @param {Object} data
     *
     */
    updateParentProperties: function(data) {
        this._properties.parentAttributes[data._fieldName] = {
            fieldName: data._fieldName,
            parentFieldName: data._parentFieldName,
        };

        this._updateActionProperties();
    },

    /**
     * Remove a selected field from the parent/record update
     *
     * @param {string} fieldId
     *
     */
    removeParentField: function(fieldId) {
        this.disposeField(fieldId);

        delete this._properties.parentAttributes[fieldId];

        this._updateActionProperties();
    },

    /**
     * Remove a selected field from the record update
     *
     * @param {string} fieldId
     *
     */
    removeAttributesField: function(fieldId) {
        this.disposeField(fieldId);

        delete this._properties.attributes[fieldId];

        this._updateActionProperties();
    },

    /**
     * Create subviews based on action configuration
     *
     */
    _createExistingControllers: function() {
        // create all the controllers that were previously saved
        _.each(this._properties.attributes, function create(data, name) {
            this._createAttributesFieldController(data);
        }, this);

        _.each(this._properties.parentAttributes, function create(data, name) {
            this._createParentFieldController(data);
        }, this);
    },

    /**
     * Add a new parent field update configuration
     *
     * @param {string} fieldName
     *
     */
    _addParentField: function(fieldName) {
        this._properties.parentAttributes[fieldName] = {
            fieldName: fieldName,
            parentFieldName: '',
        };

        this._updateActionProperties();
        this._createParentFieldController(this._properties.parentAttributes[fieldName]);
    },

    /**
     * Add a new field update configuration
     *
     * @param {string} fieldName
     *
     */
    _addAttributesField: function(fieldName) {
        this._properties.attributes[fieldName] = {
            fieldName: fieldName,
            isCalculated: false,
            formula: '',
            value: ''
        };

        this._updateActionProperties();
        this._createAttributesFieldController(this._properties.attributes[fieldName]);
    },

    /**
     * Creates the parent field value selection view
     *
     * @param {Object} fieldData
     *
     */
    _createParentFieldController: function(fieldData) {
        this.disposeField(fieldData.fieldName);

        var fieldController = app.view.createView({
            name: 'actionbutton-parent-field',
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            fieldName: fieldData.fieldName,
            parentFieldName: fieldData.parentFieldName,
            fieldModule: this._properties.module,
            deleteCallback: _.bind(this.removeParentField, this),
            callback: _.bind(this.updateParentProperties, this)
        });

        this.$('[data-container=preset-fields]').prepend(fieldController.$el);

        fieldController.render();

        if (!this._subComponents) {
            this._subComponents = [];
        }

        this._subComponents.push(fieldController);
    },

    /**
     * Creates the normal field value selection view
     *
     * @param {Object} fieldData
     *
     */
    _createAttributesFieldController: function(fieldData) {
        this.disposeField(fieldData.fieldName);

        var fieldController = app.view.createView({
            name: 'actionbutton-update-field',
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            isCalculated: fieldData.isCalculated,
            fieldName: fieldData.fieldName,
            value: fieldData.value,
            formula: fieldData.formula,
            fieldModule: this._properties.module,
            deleteCallback: _.bind(this.removeAttributesField, this),
            callback: _.bind(this.updateProperties, this)
        });

        this.$('[data-container=preset-fields]').prepend(fieldController.$el);

        fieldController.render();

        if (!this._subComponents) {
            this._subComponents = [];
        }

        this._subComponents.push(fieldController);
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
     * Create generic Select2 component or return a cached select2 element
     *
     * @param {string} fieldname
     */
    select2: function(fieldname, queryFunc, reset, callback) {
        if (this._select2 && this._select2[fieldname]) {
            return this._select2[fieldname];
        };

        var el = this.$('[data-fieldname=' + fieldname + ']')
            .select2(this._getSelect2Options(queryFunc))
            .data('select2');

        this._select2 = this._select2 || {};
        this._select2[fieldname] = el;

        if (reset) {
            el.onSelect = (function select(fn) {
                return function returnCallback(data, options) {
                    if (callback) {
                        callback(data);
                    }

                    if (arguments) {
                        arguments[0] = {
                            id: 'select',
                            text: app.lang.get('LBL_ACTIONBUTTON_SELECT_OPTION')
                        };
                    }

                    return fn.apply(this, arguments);
                };
            })(el.onSelect);
        }

        return el;
    },

    /**
     * Create generic Select2 options object
     *
     * @param {string} queryFunc
     *
     * @return {Object}
     */
    _getSelect2Options: function(queryFunc) {
        var select2Options = {};

        select2Options.placeholder = app.lang.get('LBL_ACTIONBUTTON_SELECT_OPTION');
        select2Options.dropdownAutoWidth = true;

        if (queryFunc && this[queryFunc]) {
            select2Options.query = _.bind(this[queryFunc], this);
        }

        return select2Options;
    },

    /**
     * Populate parent field select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryParentAttributes: function(query) {
        return this._query(query, '_parentAttributes');
    },

    /**
     * Populate normal field select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryAttributes: function(query) {
        return this._query(query, '_attributes');
    },

    /**
     * Populate the module list select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryModules: function(query) {
        return this._query(query, '_modules');
    },

    /**
     * Populate module link fields list select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryLinks: function(query) {
        return this._query(query, '_links');
    },

    /**
     * Generic select2 selection list builder
     *
     * @param {Object} query
     * @param {string} list
     *
     */
    _query: function(query, list) {
        var listElements = this[list];
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
     * Populate links data with label values
     *
     */
    _populateRelationships: function() {
        const targetModule = this._properties.module;
        const moduleName = app.lang.getModuleName(targetModule, {
            plural: true
        });

        var currentModuleName = this._module;
        var currentModuleFields = app.metadata.getModule(currentModuleName).fields;
        var relationships = {};

        _.each(currentModuleFields, _.bind(function getLinks(linkData) {
            const moduleLabel = app.lang.get(linkData.vname, currentModuleName);

            if (linkData.type === 'link' && (
                    moduleLabel === targetModule ||
                    moduleLabel === moduleName ||
                    linkData.module === targetModule)) {
                relationships[linkData.name] = moduleLabel + ' (' + linkData.name + ')';
            }
        }, this));

        this._links = relationships;
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
        _.each(this._subComponents, function disposeChild(component) {
            component.dispose();
        });

        this._subComponents = [];

        this._super('_dispose');
    },
});
