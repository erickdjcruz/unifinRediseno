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
 * Parent field view selection
 *
 * @class View.Views.Base.AdministrationActionbuttonParentFieldView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonParentFieldView
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
        'click [data-action=remove-field]': 'removeField',
        'change [data-fieldname=field]': 'parentFieldChanged',
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
        this._properties = {
            _fieldName: options.fieldName,
            _parentFieldName: options.parentFieldName,
        };

        this._callback = options.callback;
        this._deleteCallback = options.deleteCallback;

        if (options.fieldModule) {
            this._fieldDef = app.metadata.getModule(options.fieldModule).fields[options.fieldName];
            this._fieldType = this._fieldDef.type;
            this._module = options.fieldModule;
            this._fieldLabel = app.lang.get(this._fieldDef.vname, this._module);
            this._parentModule = options.context.get('model').get('module');

            this._populateParentFields(this._parentModule);

            if (options.parentFieldName) {
                this._parentFieldDef = app.metadata.getModule(this._parentModule).fields[options.parentFieldName];
                this._parentFieldLabel = app.lang.get(this._parentFieldDef.vname, this._parentModule);
            }
        }
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
     *  Update list of available fields to copy from the parent module
     *
     * @param {string} module Parent module
     */
    _populateParentFields: function(module) {
        var fields = _.chain(app.metadata.getModule(module).fields).values();
        fields = fields.filter(this._checkFieldCompatibility, this)
            .map(function fieldToTuple(field) {
                return [field.name, app.lang.get(field.vname, module)];
            }).value();

        this._parentFields = _.object(fields);
    },

    /**
     * Check field copy value compatibility
     *
     * @param {string} field
     *
     * @return {bool}
     */
    _checkFieldCompatibility: function(field) {
        if (
            this._normalizeType(this._fieldType) === 'parent' &&
            this._normalizeType(field.type) === 'relate'
        ) {
            const modules = app.lang.getAppListStrings(this._fieldDef.options);

            return this.badFields.indexOf(field.name) === -1 &&
            modules.hasOwnProperty(field.module) &&
            !_.isEmpty(field.vname);
        };

        if (
            this._normalizeType(this._fieldType) === 'relate' &&
            this._normalizeType(field.type) === 'relate'
        ) {
            return this._fieldDef.module === field.module;
        };

        return (
            !_.isEmpty(field.name) &&
            !_.isEmpty(field.vname) &&
            !_.contains(this.badFields, field.name) &&
            !_.contains(this.badFieldTypes, field.type) &&
            this._normalizeType(field.type) === this._normalizeType(this._fieldType) &&
            field.studio !== false &&
            field.calculated !== true
        );
    },

    /**
     *  Normalize a field type
     *
     * @param {string} field
     *
     * @return {string}
     */
    _normalizeType: function(type) {
        if (type === 'name') {
            type = 'varchar';
        }

        return type;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // add the style that couldn't be added via hbs
        this.$el.addClass('span6 ab-parent-field-wrapper ' + this._properties._fieldName);

        this._createSelect2();

        if (this._properties._parentFieldName !== '') {
            this.$field.select2('data', {
                id: this._properties._parentFieldName,
                text: this._parentFieldLabel
            });
        }
    },

    /**
     * Remove field selection handler
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
     * Parent field change handler
     *
     * @param {UIEvent} e
     *
     */
    parentFieldChanged: function(e) {
        this._properties._parentFieldName = e.currentTarget.value;

        if (this._callback) {
            this._callback(this._properties);
        }
    },

    /**
     * Create select2 control
     *
     */
    _createSelect2: function() {
        this.$field = this.$('[data-fieldname=field]');

        this.$field.select2(this._getSelect2Options())
            .data('select2');
    },

    /**
     * Build select2 options
     *
     */
    _getSelect2Options: function() {
        var select2Options = {};

        select2Options.placeholder = app.lang.get('LBL_ACTIONBUTTON_SELECT_FIELD');
        select2Options.query = _.bind(this._queryFields, this);
        select2Options.dropdownAutoWidth = true;

        return select2Options;
    },

    /**
     * Build select2 query function
     *
     * @param {Function} query
     *
     * @return {Function}
     */
    _queryFields: function(query) {
        this._query(query, '_parentFields');
    },

    /**
     * Generic select2 query function implementation
     *
     * @param {string} query
     * @param {Object} options
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
});
