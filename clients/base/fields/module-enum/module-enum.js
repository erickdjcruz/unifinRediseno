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
 * @class View.Fields.Base.ModuleEnumField
 * @alias SUGAR.App.view.fields.BaseModuleEnumField
 * @extends View.Fields.Base.BaseEnumField
 */
 ({
    extendsFrom: 'BaseEnumField',

    /**
     * Modules which can't be used to create events from
     */
    denyModules: [
        'Home',
        'Calendar',
        'Forecasts',
        'Reports',
        'ops_Backups',
        'Documents',
        'Campaigns',
        'pmse_Inbox',
        'BusinessCenters',
        'DataPrivacy',
        'Emails',
        'OutboundEmail',
        'ProductTemplates',
        'Shifts',
        'Tags',
        'pmse_Business_Rules',
        'pmse_Emails_Templates',
        'pmse_Project',
        'RevenueLineItems',
        'ProspectLists',
        'PurchasedLineItems',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        // filter-rows does not know about this field type, so we have to handle it ourself
        if (options.view.name == 'filter-rows') {
            options.def.isMultiSelect = true;//atm we have $in and $not_in operators, so multi select
            options.def.searchBarThreshold = -1;// a solution in sugar10 to hide the search input
        }
        this._super('initialize', [options]);
    },

    /**
     * @override
     */
    _loadTemplate: function() {
        this.type = 'enum';
        this._super('_loadTemplate');
        this.type = 'module-enum';
    },

    /**
     * Load enum list
     */
    loadEnumOptions: function() {
        this.items = {};

        if (typeof this.options.def.options == 'undefined') {
            this.items = this.getModuleItems();
        } else {
            this.items = this.options.def.options;
        }
    },

    /**
     * Get modules which can be represented as events
     *
     * @return {Object} Dictionary with modules
     */
    getModuleItems: function() {
        var modules = {};
        var moduleList = app.metadata.getModuleNames({
            filter: 'display_tab',
            access: true
        });

        _.each(moduleList, function(module) {
            if (this.denyModules.indexOf(module) == -1 && this.hasStartDate(module)) {
                modules[module] = app.lang.getModuleName(module, {plural: true});
            }
        }, this);

        return modules;
    },

    /**
     * Has Start Date
     *
     * Checks whether module given has date or datetime fields except of deny list
     *
     * @param {string} module
     * @return {boolean}
     */
    hasStartDate: function(module) {
        let moduleMetadata = app.metadata.getModule(module);
        let fieldTypesAllowed = ['date', 'datetime'];

        if (moduleMetadata) {
            let hasStartDate = false;
            let fieldsMetadata = moduleMetadata.fields;

            _.each(fieldsMetadata, function(fieldMetadata) {
                if (typeof fieldMetadata == 'object') {
                    let fieldType = fieldMetadata.dbType || fieldMetadata.dbtype || fieldMetadata.type;
                    let fieldSource = fieldMetadata.source || '';
                    if (
                        fieldTypesAllowed.indexOf(fieldType) >= 0 &&
                        fieldSource != 'non-db' &&
                        this.model.denyFields.indexOf(fieldMetadata.name) == -1
                    ) {
                        hasStartDate = true;
                    }
                };
            }, this);

            if (hasStartDate) {
                return true;
            }
        }

        return false;
    }
});
