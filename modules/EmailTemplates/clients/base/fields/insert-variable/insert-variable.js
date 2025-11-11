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
 * @class View.Fields.Base.EmailTemplates.InsertVariableField
 * @alias SUGAR.App.view.fields.BaseEmailTemplatesInsertVariableField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    /**
     * List of fields blacklisted from inclusion in Email Template variables
     */
    badFields: [
        'team_id',
        'account_description',
        'contact_id',
        'lead_id',
        'opportunity_amount',
        'opportunity_id',
        'opportunity_name',
        'opportunity_role_id',
        'opportunity_role_fields',
        'opportunity_role',
        'campaign_id',
        // User objects
        'id',
        'date_entered',
        'date_modified',
        'user_preferences',
        'accept_status',
        'user_hash',
        'authenticate_id',
        'sugar_login',
        'reports_to_id',
        'reports_to_name',
        'is_admin',
        'receive_notifications',
        'modified_user_id',
        'modified_by_name',
        'created_by',
        'created_by_name',
        'accept_status_id',
        'accept_status_name',
        'acl_role_set_id',
        'sync_key'
    ],

    /**
     * List of field types blacklisted from inclusion in Email Template Variables
     */
    badFieldTypes: [
        'assigned_user_name', 'link', 'bool'
    ],

    /**
     * Initial empty object to hold our data structure used for variable lookup.
     * After loading this will be in the form of:
     *
     * {
     *     ModuleDropdown: [
     *         // list of options for variable dropdown
     *         {name: '...', 'value'...},
     *         ...
     *     ],
     *     ...
     * }
     *
     * This list of field names and variable values is generated based on the
     * `variable_source` array passed in the moduleList metadata
     */
    variableOptions: {},

    /**
     * Event listeners
     */
    events: {
        'click [name="insert_button"]': 'insertClicked'
    },

    /**
     * @inheritdoc
     *
     * Prepare human-readable labels for module dropdown, and generate the
     * variable option object.
     *
     * @param {Object} options
     */
    initialize: function(options) {
        // We prepare the labels before calling "Super" so everything can be
        // translated before options.moduleList is bound to this.moduleList.
        options.moduleList = this._prepareLabels(options);
        this._super('initialize', [options]);
        this._generateOptions();
    },

    /**
     * On changing into edit mode, we bind dropdown event listeners. On changing
     * out of edit mode, we hide the record cell.
     */
    bindDomChange: function() {
        this._super('bindDomChange');
        this.$el.closest('.record-cell').toggle(this.action === 'edit');
        var $moduleDropdown = this.$el.children('[name="variable_module"]');
        var onModuleChange = _.bind(this._updateVariables, this);
        $moduleDropdown.on('change', onModuleChange);

        var $variableDropdown = this.$el.children('[name="variable_name"]');
        var onVariableChange = _.bind(this._showVariable, this);
        $variableDropdown.on('change', onVariableChange);
        this._updateVariables();
    },

    /**
     * If moduleList label is a "LBL_" string, translate it. If it is an array
     * of module names, filter it by ACL access and join module names with '/'
     *
     * @param options
     * @return {Object}
     * @private
     */
    _prepareLabels: function(options) {
        return _.map(options.def.moduleList, function(module) {
            if (_.isArray(module.label)) {
                module.label = _.filter(module.label, function(module) {
                    return app.acl.hasAccess('view', module);
                }, this);
                module.label = _.map(module.label, function(label) {
                    return app.lang.getModuleName(label);
                }).join('/');
            } else {
                module.label = app.lang.get(module.label);
            }
            return module;
        });
    },

    /**
     * Iterate over list of modules, setting variableOptions by module key.
     * Delegates heavy lifting to _getVariablesByModule.
     *
     * @private
     */
    _generateOptions: function() {
        _.each(this.def.moduleList, function(module) {
            this.variableOptions[module.value] = this._getVariablesByModule(module);
        }, this);
    },

    /**
     * Generate a list of variables available based on the provided module metadata.
     * Metadata comes in the form of
     * {
     *     'value': 'Contacts',
     *     'variable_source': ['Contacts', 'Leads', 'Prospects']
     *     'variable_prefix': 'contact_'
     * }
     *
     * For each module in the `variable_source` array, all of the fields not
     * blacklisted are added to the array of available dropdown options.
     *
     * For `variable_source` modules where the user lacks ACL access,
     * app.data.createBean returns a model with no fields defined, so no
     * variables are added.
     *
     * @param module Module metadata telling us which modules' fields to load,
     *               and what prefix to use when inserting fields from the second
     *               dropdown
     * @return {Array} Array of {name, value} options for the provided module
     *                 dropdown value
     * @private
     */
    _getVariablesByModule: function(module) {
        // Cache with fast insertion and lookup to avoid repeat
        // fields
        var variableCache = new Set();
        var variables = [];
        _.each(module.variable_source, function(moduleKey) {
            var bean = app.data.createBean(moduleKey);
            _.each(bean.fields, function(field) {
                if (variableCache.has(field.name) || this._shouldOmitField(field)) {
                    // If we should omit this field, store it in our cache so
                    // the next time we see it we can skip it faster
                    variableCache.add(field.name);
                    return;
                }
                // prepend our variable_prefix to the field name
                var key = module.variable_prefix + field.name;
                key = key.toLowerCase();
                var label = app.lang.get(field.vname, moduleKey);
                variables.push({
                    name: key,
                    value: label
                });
                variableCache.add(field.name);
            }, this);
        }, this);
        return variables;
    },

    /**
     * Util for determining if a field should be omitted. This is intended to
     * improve readability over short-circuit evaluation while still performing
     * the cheapest checks up front.
     *
     * @param field Field metadata from module bean.
     * @return {boolean} True if field should be omitted
     * @private
     */
    _shouldOmitField: function(field) {
        if (_.isEmpty(field.name) || _.isEmpty(field.type)) {
            return true;
        }
        if (field.type === 'relate' && _.isEmpty(field.custom_type)) {
            return true;
        }
        // badFieldTypes is smaller, so check it first
        if (_.contains(this.badFieldTypes, field.type)) {
            return true;
        }
        // Finally the most expensive check
        return _.contains(this.badFields, field.name);
    },

    /**
     * Callback for when the module dropdown changes. This empties the variable
     * dropdown, and appends a new list of options created during _generateOptions.
     * @private
     */
    _updateVariables: function() {
        var selection = this.$el.children('[name="variable_module"]');
        var options = this.variableOptions[selection.val()];
        var variableDropdown = selection.siblings('[name="variable_name"]');
        variableDropdown.empty();
        _.each(options, function(option) {
            var newOption = document.createElement('option');
            newOption.value = '$' + option.name;
            newOption.text = option.value;
            variableDropdown.append(newOption);
        });
        this._showVariable();
    },

    /**
     * Show selected variable in the `variable` input box
     * @private
     */
    _showVariable: function() {
        var selection = this.$el.children('[name="variable_name"]');
        var variableInput = selection.siblings('[name="variable_text"]');
        variableInput.val(selection.val());
    },

    /**
     * Trigger `insertClicked` event on the view with variable input value so
     * listeners can handle the actual variable insertion
     */
    insertClicked: function() {
        var variableInput = this.$el.children('[name="variable_text"]');
        this.view.trigger('insertClicked', variableInput.val());
    }
})
