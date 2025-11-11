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
 * @class View.Fields.Base.MapsDistanceField
 * @alias SUGAR.App.view.fields.BaseMapsDistanceField
 * @extends View.Fields.Base.BaseField
 */
({

    /**
     * Event listeners
     */
    events: {
        'change [data-fieldname=countries]': 'mapParamsChanged',
        'change [data-fieldname=unitType]': 'mapParamsChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this._countries = this._getCountryLabels();
        this._unitTypes = {
            'miles': app.lang.getModString('LBL_MAPS_UNIT_TYPE_MILES', 'Administration'),
            'km': app.lang.getModString('LBL_MAPS_UNIT_TYPE_KM', 'Administration')
        };

        if (!this.model.get(this.name)) {
            this.model.set(
                this.name, {
                    radius: 0,
                    countries: 'United States',
                    zipCode: '',
                    unitType: 'miles'
                },{
                    slient: true
                }
            );
        }

        this.DEBOUNCE_DELAY = 400;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._select2 = {};
        this.select2('countries', '_queryCountries', {
            formatResult: _.bind(this._getSelect2CountryOption, this),
            formatSelection: _.bind(this._getSelect2CountrySelection, this),
        });
        this.select2('unitType', '_queryUnitTypes', {
            minimumResultsForSearch: Infinity
        });

        this._unregisterJQueryEvents();
        this._registerJQueryEvents();

        this._updateUIElements();

        if (!app.user.hasMapsLicense()) {
            this._showNoAccessAlert();
        }
    },

    /**
     * Update UI Elements
     */
    _updateUIElements: function() {
        const mapsDistance = this.model.get(this.name);

        this.$('[data-fieldname="radius"]').val(mapsDistance.radius);
        this.$('[data-fieldname="zipCode"]').val(mapsDistance.zipCode);

        this._select2.countries.data({
            id: _.invert(this._countries)[mapsDistance.countries],
            text: mapsDistance.countries
        });
        this._select2.unitType.data({
            id: mapsDistance.unitType,
            text: this._unitTypes[mapsDistance.unitType]
        });
    },

    /**
     * Register jquery elements events
     */
    _registerJQueryEvents: function() {
        this.$('[data-fieldname="radius"]').on(
            'keyup',
            _.debounce(_.bind(this.mapParamsChanged, this), this.DEBOUNCE_DELAY)
        );
        this.$('[data-fieldname="zipCode"]').on(
            'keyup',
            _.debounce(_.bind(this.mapParamsChanged, this), this.DEBOUNCE_DELAY)
        );
    },

    /**
     * Unregister jquery elements events
     */
    _unregisterJQueryEvents: function() {
        this.$('[data-fieldname="radius"]').off(
            'keyup',
            _.debounce(_.bind(this.mapParamsChanged, this), this.DEBOUNCE_DELAY)
        );
        this.$('[data-fieldname="zipCode"]').off(
            'keyup',
            _.debounce(_.bind(this.mapParamsChanged, this), this.DEBOUNCE_DELAY)
        );
    },

    /**
     * Handle radius changes
     *
     * @param {jQuery} e
     */
    mapParamsChanged: function(e) {
        let value = e.currentTarget.value;
        const fieldName = e.currentTarget.dataset.fieldname;

        if (fieldName === 'countries') {
            value = this._countries[e.currentTarget.value];
        }

        let mapsDistance = app.utils.deepCopy(this.model.get(this.name));

        mapsDistance[fieldName] = value;

        if (app.user.hasMapsLicense()) {
            this.model.set(this.name, mapsDistance, {silent: true});
            this.model.trigger('change');
        } else {
            this._showNoAccessAlert();
        }
    },

    /**
     * Stop default behavior
     *
     * @param {jQuery} e
     */
    stopKeyUpEvent: function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        return false;
    },

    /**
     * Populate the countries list select2 component
     *
     * @param {Object} query
     *
     */
    _queryCountries: function(query) {
        this._query(query, '_countries');
    },

    /**
     * Populate the unit types list select2 component
     *
     * @param {Object} query
     *
     */
    _queryUnitTypes: function(query) {
        this._query(query, '_unitTypes');
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
     * Format the select2 element
     *
     * @param {Object} item
     * @return {string}
     */
    _getSelect2CountryOption: function(item) {
        return `<div>
                    <i class="flag-icon flag-icon-${item.id}"></i>
                    <span class="map-small-left-padding">${item.text}</span>
                </div>`;
    },

    /**
     * Format the select2 element
     *
     * @param {Object} item
     * @return {string}
     */
    _getSelect2CountrySelection: function(item) {
        return `<div>
                    <i class="flag-icon flag-icon-${item.id}"></i>
                </div>`;
    },

    /**
     * Create generic Select2 options object
     *
     * @return {Object}
     */
    _getSelect2Options: function(additionalOptions) {
        var select2Options = _.extend({}, additionalOptions);

        select2Options.placeholder = app.lang.get('LBL_MAPS_SELECT_NEW_MODULE_TO_GEOCODE', 'Administration');
        select2Options.dropdownAutoWidth = true;

        return select2Options;
    },

    /**
     * Create generic Select2 component or return a cached select2 element
     *
     * @param {string} fieldname
     * @param {string} queryFunc
     */
    select2: function(fieldname, queryFunc, additionalOptions) {
        if (this._select2 && this._select2[fieldname]) {
            return this._select2[fieldname];
        };

        this._disposeSelect2(fieldname);

        if (queryFunc && this[queryFunc]) {
            additionalOptions.query = _.bind(this[queryFunc], this);
        }

        var el = this.$('[data-fieldname=' + fieldname + ']')
            .select2(this._getSelect2Options(additionalOptions))
            .data('select2');

        this._select2 = this._select2 || {};
        this._select2[fieldname] = el;

        return el;
    },

    /**
     * Dispose a select2 element
     */
    _disposeSelect2: function(name) {
        if (this._select2 && _.isObject(this._select2)) {
            delete this._select2[name];
        }

        this.$('[data-fieldname=' + name + ']').select2('destroy');
    },

    /**
     * Get a list with mapping for countries
     *
     * @return {Object}
     */
    _getCountryLabels: function() {
        return {
            'us': 'United States',
            'ae': 'United Arab Emirates', 'al': 'Albania', 'am': 'Armenia', 'ar': 'Argentina', 'at': 'Austria',
            'au': 'Australia', 'az': 'Azerbaijan', 'ba': 'Bosnia and Herzegovina', 'be': 'Belgium', 'bg': 'Bulgaria',
            'bh': 'Bahrain', 'bo': 'Bolivia', 'br': 'Brazil', 'ca': 'Canada', 'ch': 'Switzerland', 'cl': 'Chile',
            'cn': 'China', 'co': 'Colombia', 'cr': 'Costa Rica', 'cz': 'Czechia', 'de': 'Germany', 'dk': 'Denmark',
            'do': 'Dominican Republic', 'dz': 'Algeria', 'ec': 'Ecuador', 'ee': 'Estonia', 'es': 'Spain', 'eg': 'Egypt',
            'fi': 'Finland', 'fr': 'France', 'gb': 'United Kingdom', 'ge': 'Georgia', 'gr': 'Greece', 'gt': 'Guatemala',
            'hk': 'Hong Kong SAR', 'hn': 'Honduras', 'hr': 'Croatia', 'hu': 'Hungary', 'id': 'Indonesia',
            'ie': 'Ireland', 'il': 'Israel', 'in': 'India', 'iq': 'Iraq', 'ir': 'Iran', 'is': 'Iceland', 'it': 'Italy',
            'jo': 'Jordan', 'jp': 'Japan', 'ke': 'Kenya', 'kr': 'Korea', 'kw': 'Kuwait', 'lb': 'Lebanon',
            'lt': 'Lithuania', 'lv': 'Latvia', 'lu': 'Luxembourg', 'ly': 'Libya', 'ma': 'Morocco',
            'mk': 'North Macedonia', 'mt': 'Malta', 'my': 'Malaysia', 'mx': 'Mexico', 'ni': 'Nicaragua',
            'nl': 'Netherlands', 'nz': 'New Zealand', 'no': 'Norway', 'om': 'Oman', 'pa': 'Panama', 'pe': 'Peru',
            'ph': 'Philippines', 'pl': 'Poland', 'pk': 'Pakistan', 'pr': 'Puerto Rico', 'pt': 'Portugal',
            'py': 'Paraguay', 'qa': 'Qatar', 'ro': 'Romania', 'ru': 'Russia', 'sa': 'Saudi Arabia', 'se': 'Sweden',
            'sg': 'Singapore', 'sk': 'Slovakia', 'sl': 'Slovenia', 'sp': 'Serbia', 'sv': 'El Salvador', 'sy': 'Syria',
            'tw': 'Taiwan', 'th': 'Thailand', 'tn': 'Tunisia', 'tr': 'Turkey', 'ua': 'Ukraine', 'vn': 'Vietnam',
            'ye': 'Yemen', 'za': 'South Africa'
        };
    },

    /**
     * Prevent triggering the model change to avoid the api call for filtering
     * before our values are collected
     *
     * @inheritdoc
     */
    unformat: function() {
        return this.model.get(this.name);
    },

    /**
     * Show no access alert
     */
    _showNoAccessAlert: function() {
        app.alert.show('maps_invalid_license', {
            level: 'error',
            messages: app.lang.get('LBL_MAPS_NO_LICENSE_ACCESS'),
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._unregisterJQueryEvents();
        this._disposeSelect2('countries');
        this._disposeSelect2('unitType');

        this._select2 = {};

        this._super('_dispose');
    },
});
