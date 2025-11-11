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
 * @class View.Views.Base.AdministrationMapsModuleMappingsView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsModuleMappingsView
 */
({
    /**
     * Event listeners
     */
    events: {
        'change [data-action=mapping-changed]': 'mappingChanged',
        'change [data-action=mapping-type-changed]': 'mappingTypeChanged',
        'change [data-action=related-record-changed]': 'relatedRecordChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);
        this._super('initialize', [options]);
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._mappingData = {
            locality: {
                label: 'LBL_ADDRESS_CITY',
                id: 'locality'
            },
            countryRegion: {
                label: 'LBL_ADDRESS_COUNTRY',
                id: 'country-region'
            },
            addressLine: {
                label: 'LBL_ADDRESS_STREET',
                id: 'address-line'
            },
            postalCode: {
                label: 'LBL_ADDRESS_POSTALCODE',
                id: 'postal-code'
            },
            adminDistrict: {
                label: 'LBL_ADDRESS_STATE',
                id: 'admin-district'
            },
        };

        this._mappingTypes = {
            moduleFields: 'LBL_MAPS_MODULE_FIELDS',
            relateRecord: 'LBL_MAPS_RELATE_RECORD',
        };

        this._relatedRecords = {};

        if (options.widgetModule) {
            this.widgetModule = options.widgetModule;
            this.widgetFields = app.metadata.getModule(this.widgetModule).fields;

            if (!_.isEmpty(this.widgetFields)) {
                this._fields = app.utils.maps.arrayToObject(
                    _.chain(this.widgetFields)
                    .filter(function getAddressLikeFields(field) {
                        return field.vname &&
                            (field.type === 'varchar' || field.type === 'text' || field.type === 'dropdown') &&
                            (field.source !== 'non-db');
                    })
                    .map(function buildFields(field) {
                        let data = {};

                        data[field.name] = app.lang.get(field.vname, this.widgetModule);

                        return data;
                    }, this)
                    .value()
                );
            }

            const moduleData = app.metadata.getModule(this.widgetModule).fields;

            _.chain(moduleData)
                .filter(function getRelatedFields(field) {
                    const isValidModule = _.contains(this.model.get('maps_enabled_modules'), field.module);
                    const linkTypeCstmKey = 'link-type';
                    const linkTypeKey = 'link_type';

                    return (field[linkTypeKey] === 'one' || field[linkTypeCstmKey] === 'one') &&
                            field.type === 'link' &&
                            isValidModule &&
                            (field.vname || field.label);
                }, this)
                .each(function mapFields(field) {
                    this._relatedRecords[field.name] = {
                        label: field.vname ? field.vname : field.label,
                        module: field.module,
                        rel: field.relationship
                    };
                }, this)
                .value();
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createMappingTypeSelect();
        this._createRelatedRecordSelect();

        _.each(this._mappingData, function createSelect2(data, addressKey) {
            this.$('[data-fieldname=' + data.id + ']').select2();
            this._updateSelect2El(addressKey, data.id);
        }, this);

        this._updateMappingVisibility();
    },

    /**
     * Transform Related Record  into Select2
     *
     */
    _createRelatedRecordSelect: function() {
        const widgetModule = this.widgetModule;

        let modulesData = this.context.safeRetrieveModulesData(widgetModule);
        let mappingRecord = modulesData[widgetModule].mappingRecord;

        const relField = _.chain(mappingRecord)
                        .keys()
                        .first()
                        .value();

        const relatedRecordEl = this.$('[data-action=related-record-changed]');

        const relRecordLabel = mappingRecord[relField] ? mappingRecord[relField].label : 'LBL_MAPS_SELECT_FIELD';
        let relRecordTxt = app.lang.getModString(relRecordLabel, widgetModule);

        if (!relRecordTxt) {
            relRecordTxt = app.lang.get(relRecordLabel);
        }

        relatedRecordEl.select2();
        relatedRecordEl.select2('data', {
            id: relField,
            text: relRecordTxt
        });
    },

    /**
     * Transform Mapping Type into Select2
     *
     */
    _createMappingTypeSelect: function() {
        const widgetModule = this.widgetModule;

        let modulesData = this.context.safeRetrieveModulesData(widgetModule);
        let mappingType = modulesData[widgetModule].mappingType;

        const mappingTypeEl = this.$('[data-action=mapping-type-changed]');

        mappingTypeEl.select2();
        mappingTypeEl.select2('data', {
            id: mappingType,
            text: app.lang.get(this._mappingTypes[mappingType]),
        });
    },

    /**
     * Update select2 value
     *
     * @param {string} addressType
     * @param {string} elId
     */
    _updateSelect2El: function(addressType, elId) {
        const widgetModule = this.widgetModule;

        let modulesData = this.context.safeRetrieveModulesData(widgetModule);
        let mappedAddress = modulesData[widgetModule].mappings[addressType];

        if (mappedAddress) {
            this.$('[data-fieldname=' + elId + ']').select2('data', {
                id: mappedAddress,
                text: this._fields[mappedAddress]
            });
        } else {
            this.$('[data-fieldname=' + elId + ']').select2('data', {
                id: 'chooseMappingField',
                text: app.lang.getModString('LBL_MAPS_CHOOSE_FIELD', this.module)
            });
        }
    },

    /**
     * Event handler for mapping type selection change
     *
     * @param {UIEvent} e
     *
     */
    mappingTypeChanged: function(e) {
        const mappingType = e.currentTarget.value;
        const widgetModule = this.widgetModule;

        if (widgetModule) {
            let modulesData = this.context.safeRetrieveModulesData(widgetModule);

            modulesData[widgetModule].mappingType = mappingType;

            this.model.set('maps_modulesData', modulesData);
            this.model.trigger('change', this.model);
        }

        this._updateMappingVisibility();
    },

    /**
     * Updates mapping el visibility
     *
     */
    _updateMappingVisibility: function() {
        const widgetModule = this.widgetModule;

        let modulesData = {};
        let mappingType = 'moduleFields';

        if (widgetModule) {
            modulesData = this.context.safeRetrieveModulesData(widgetModule);
            mappingType = modulesData[widgetModule].mappingType;
        }

        if (mappingType === 'moduleFields') {
            this.$('[data-container="mappings-container"]').show();
            this.$('[data-container="mapping-related-record"]').hide();
        } else {
            this.$('[data-container="mappings-container"]').hide();
            this.$('[data-container="mapping-related-record"]').show();
        }
    },

    /**
     * Event handler for mapping field selection change
     *
     * @param {UIEvent} e
     *
     */
    relatedRecordChanged: function(e) {
        const value = e.currentTarget.value;
        const widgetModule = this.widgetModule;

        if (widgetModule) {
            let modulesData = this.context.safeRetrieveModulesData(widgetModule);

            modulesData[widgetModule].mappingRecord = {};
            modulesData[widgetModule].mappingRecord[value] = this._relatedRecords[value];

            this.model.set('maps_modulesData', modulesData);
            this.model.trigger('change', this.model);
        }
    },

    /**
     * Event handler for mapping field selection change
     *
     * @param {UIEvent} e
     *
     */
    mappingChanged: function(e) {
        const value = e.currentTarget.value;
        const addressType = app.utils.kebabToCamelCase(e.currentTarget.dataset.fieldname);
        const widgetModule = this.widgetModule;

        if (widgetModule) {
            let modulesData = this.context.safeRetrieveModulesData(widgetModule);

            modulesData[widgetModule].mappings[addressType] = value;

            this.model.set('maps_modulesData', modulesData);
            this.model.trigger('change', this.model);
        }
    },
});
