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
 * @class View.Fields.Base.GeocodeStatusField
 * @alias SUGAR.App.view.fields.BaseGeocodeStatusField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._registerEvents();
    },

    /**
     * Quick initialization of field properties
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        this._failedStatus = 'FAILED';
        this._statuses = {
            'LBL_MAPS_GEOCODED': 'COMPLETED',
            'LBL_MAPS_QUEUED': 'QUEUED',
            'LBL_MAPS_REQUEUED': 'REQUEUE',
            'LBL_MAPS_NOT_GEOCODED': 'NOT_GEOCODED',
            'LBL_MAPS_NOT_FOUND': 'NOT_FOUND',
        };
        this._status = (options && options.status) ? options.status : 'LBL_MAPS_NOT_GEOCODED';
    },

    /**
     * Listening to external events
     */
    _registerEvents: function() {
        this.listenTo(this.model, 'sync', _.bind(this._updateStatus, this));
        this.listenTo(this.view, 'maps-manual-geocoding-finished', _.bind(this._getGeocodeStatus, this, true));
    },

    /**
    * Update the status field
    *
    * @private
    */
    _updateStatus: function() {
        if (this.disposed) {
            return;
        }

        const newStatus = this.model.get('geocode_status');

        if (newStatus && newStatus !== this._statuses[this._status]) {
            this._status = this.getLabelByStatus(newStatus);
            this.render();
        }
    },

    /**
     * Get the geocode status from the related geocode record
     *
     * @param {boolean} force
     */
    _getGeocodeStatus: function(force) {
        if (this.disposed) {
            return;
        }

        const targetModule = this.model.module;

        if (!app.utils.maps.isMapsModuleEnabled(targetModule)) {
            return;
        }

        const moduleData = app.config.maps.modulesData[targetModule];

        if (!moduleData) {
            return;
        }

        const mappingType = moduleData.mappingType;

        // if the geocoding is dependent on a related record we don't want to store that value, only display it
        if (mappingType === 'relateRecord') {
            this._status = 'LBL_MAPS_RELATED_RECORD';

            this.render();
            return;
        }

        const geocodeStatus = this.model.get(this.name);
        this._status = geocodeStatus ? this.getLabelByStatus(geocodeStatus) : '';

        // we only fetch the status if it has never been initialized or if forced
        if (this._status && !force) {
            this.render();
            return;
        }

        const geocodeCollection = app.data.createBeanCollection('Geocode');

        geocodeCollection.filterDef = [
            {
                'parent_id': {
                    '$in': [this.model.get('id')]
                }
            },
            {
                'deleted': 0
            }
        ];

        geocodeCollection.fetch({
            limit: 1,
            success: _.bind(this.storeGeocodeStatus, this),
        });
    },

    /**
     * Store the geocode status and display it
     *
     * @param {Array} collection
     */
    storeGeocodeStatus: function(collection) {
        if (this.disposed || !collection) {
            return;
        }

        const targetModel = _.first(collection.models);

        // if we can't find any geocode record that matches our record then it is not geocoded
        if (!targetModel) {
            this._status = 'LBL_MAPS_NOT_GEOCODED';

            this.saveStatus();
            return;
        }

        this._status = this.getLabelByStatus(targetModel.get('status'));

        this.saveStatus();
    },

    /**
     * Returns the label depending on a given geocode status
     *
     * @param {string} geocodeStatus
     * @return string
     */
    getLabelByStatus: function(geocodeStatus) {
        let label = '';

        switch (geocodeStatus) {
            case 'COMPLETED':
                label = 'LBL_MAPS_GEOCODED';
                break;
            case 'QUEUED':
                label = 'LBL_MAPS_QUEUED';
                break;
            case 'REQUEUE':
                label = 'LBL_MAPS_REQUEUED';
                break;
            case 'NOT_GEOCODED':
                label = 'LBL_MAPS_NOT_GEOCODED';
                break;
            case 'NOT_FOUND':
                label = 'LBL_MAPS_NOT_FOUND';
                break;
            default:
                label = 'LBL_MAPS_GEOCODING_FAILED';
                break;
        }

        return label;
    },

    /**
     * Save the status we got from the geocode record
     */
    saveStatus: function() {
        this.model.set(this.name, this._statuses[this._status] ? this._statuses[this._status] : this._failedStatus);
        this.model.save({},{
            showAlerts: false,
            success: _.bind(function() {
                this.render();
            }, this),
        });
    },
})
