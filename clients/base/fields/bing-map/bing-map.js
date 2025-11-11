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
 * Assign Record action configuration view
 *
 * @class View.Fields.Base.BingMapField
 * @alias SUGAR.App.view.fields.BaseBingMapField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click [data-action=map-close]': 'closeMap',
        'click [data-action=map-expand]': 'expandMap',
        'click [data-action=map-to-pdf]': 'saveMapAsPdf',
        'click [data-action=map-share]': 'emailMap',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties(options);
    },

    /**
     * Property initialization, nothing to do for this view
     *
     * @param {Object} options
     */
    _initProperties: function(options) {
        this._map = null;
        this._searchManager = null;
        this._directionsManager = null;
        this._infoBox = null;
        this._locations = [];
        this._pushPins = [];
        this._itinerary = {};
        this._showCloseButton = options.showCloseButton;
        this._showExpandButton = options.showExpandButton;
        this._showMapToPdfButton = options.showMapToPdfButton;
        this._showMapShareButton = options.showMapShareButton;
        this._showDirections = false;
        this._expanded = false;
        this._pushPinIcon = 'styleguide/assets/img/map-blue-pushpin.svg';
    },

    /**
     * Create the map
     */
    createMap: function() {
        const path = 'maps/getApiKey/bing';
        const requestType = 'read';

        const apiUrl = app.api.buildURL(path, requestType);

        const callbacks = {
            success: _.bind(this._buildMap, this)
        };

        app.api.call(requestType, apiUrl, null, callbacks);
    },

    /**
     * Trigger close map event
     */
    closeMap: function() {
        this._expanded = false;

        this.trigger('map:expand', this._expanded);
        this.trigger('map:close');
    },

    /**
     * Trigger expand map event
     */
    expandMap: function() {
        this._expanded = !this._expanded;
        this.trigger('map:expand', this._expanded);
    },

    /**
     * Trigger to save the map as pdf
     */
    saveMapAsPdf: function() {
        this.trigger('map:save:pdf');
    },

    /**
     * Trigger to share the map via email
     */
    emailMap: function() {
        this.trigger('map:share:email');
    },

    /**
     * Generate the Bing Map
     *
     * @param {string} bingMapKey
     */
    _buildMap: function(bingMapKey) {
        if (this.disposed) {
            return;
        }

        const $mapContainer = this.$('div[data-container="map-container"]');
        const mapHtmlEl = $mapContainer.get(0);

        $mapContainer.show();

        this._map = new Microsoft.Maps.Map(mapHtmlEl, {
            navigationBarMode: Microsoft.Maps.NavigationBarMode.square,
            credentials: bingMapKey,
            supportedMapTypes: [
                Microsoft.Maps.MapTypeId.road,
                Microsoft.Maps.MapTypeId.aerial,
                Microsoft.Maps.MapTypeId.roadDark,
                Microsoft.Maps.MapTypeId.streetside,
            ]
        });

        this._registerMapEvents();
        this._loadMapModules();
    },

    /**
     * Register map related events
     */
    _registerMapEvents: function() {
        this._map.getMapLoadedEvent().addOne(_.bind(this._onMapReady, this));

        Microsoft.Maps.Events.addHandler(this._map, 'click', _.bind(this._onMapPointClick, this));
    },

    /**
     * Called when map is completely loaded
     */
    _onMapReady: function() {
        if (!this._showDirections) {
            this.$('[data-widget=map-loading]').hide();
        }

        if (this._showCloseButton) {
            this.$('[data-action=map-close]').css('display', 'flex');
        }

        if (this._showExpandButton) {
            this.$('[data-action=map-expand]').css('display', 'flex');
        }

        if (this._showMapToPdfButton) {
            this.$('[data-action=map-to-pdf]').css('display', 'flex');
        }

        if (this._showMapShareButton) {
            this.$('[data-action=map-share]').css('display', 'flex');
        }

        this.trigger('map:load:complete');
    },

    /**
     * Called when map is being clicked
     *
     * @param {Event} e
     */
    _onMapPointClick: function(e) {
        this.trigger('map:map:click', e);
    },

    /**
     * Load additional BingMap modules
     */
    _loadMapModules: function() {
        Microsoft.Maps.loadModule('Microsoft.Maps.Search', {
            callback: _.bind(this._searchModuleLoaded, this)
        });

        Microsoft.Maps.loadModule('Microsoft.Maps.Directions', {
            callback: _.bind(this._directionsModuleLoaded, this)
        });
    },

    /**
     * Called when MapsSearch module is completely loaded
     */
    _searchModuleLoaded: function() {
        this._searchManager = new Microsoft.Maps.Search.SearchManager(this._map);

        this.trigger('map:search:load:complete');
    },

    /**
     * Called when MapsDirection module is completely loaded
     */
    _directionsModuleLoaded: function() {
        this._directionsManager = new Microsoft.Maps.Directions.DirectionsManager(this._map);

        Microsoft.Maps.Events.addHandler(
            this._directionsManager,
            'directionsError',
            _.bind(this._directionsError, this)
        );

        Microsoft.Maps.Events.addHandler(
            this._directionsManager,
            'directionsUpdated',
            _.bind(this._directionsUpdated, this)
        );

        this.trigger('map:directions:load:complete');
    },

    /**
     * Handler from when a direction cannot be computed
     *
     * @param {Object} e
     */
    _directionsError: function(e) {
        this.$('[data-widget=map-loading]').hide();
        this.$('[data-container=driving-directions]').hide();

        app.alert.dismiss('maps_calculating_route');
        app.alert.show('maps_invalid_route', {
            level: 'warning',
            messages: e.message,
        });

        this._map.setView({
            zoom: 1
        });
    },

    /**
     * Dismiss the popup when the directions are ready
     *
     * @param {Object} itinerary
     */
    _directionsUpdated: function(itinerary) {
        this._itinerary = itinerary;

        this.$('[data-widget=map-loading]').hide();
        app.alert.dismiss('maps_calculating_route');
    },

    /**
     * Show directions
     *
     * @param {Array} points
     * @param {Object} startingPoint
     */
    showDirections: function(points, startingPoint) {
        const $itineraryContainer = this.$('[data-container=driving-directions]');

        $itineraryContainer.show();

        app.alert.show('maps_calculating_route', {
            level: 'info',
            messages: app.lang.get('LBL_MAP_CALCULATING_DIRECTIONS'),
            autoClose: false,
        });

        this._directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({
            address: startingPoint.address,
            location: new Microsoft.Maps.Location(startingPoint.coords.latitude, startingPoint.coords.longitude)
        }));

        _.each(points, function addWaypoint(point) {
            this._directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({
                address: point.address,
                location: new Microsoft.Maps.Location(point.coords.latitude, point.coords.longitude)
            }));
        }, this);

        this._directionsManager.setRequestOptions({
            distanceUnit: Microsoft.Maps.Directions.DistanceUnit[app.config.maps.unitType],
        });

        this._directionsManager.setRenderOptions({
            itineraryContainer: $itineraryContainer[0],
            drivingPolylineOptions: {
                strokeColor: 'green',
                strokeThickness: 6
            },
        });

        this._directionsManager.calculateDirections();

        this._showDirections = true;
    },

    /**
     * Looking into BingMap for an address
     *
     * @param {string} address
     * @param {Function} callback
     * @param {Function} errorCallback
     */
    searchByAddress: function(address, callback, errorCallback) {
        if (!this._searchManager) {
            if (errorCallback) {
                const noResults = {};

                errorCallback(noResults);
            }

            return;
        }

        const geocodeRequest = {
            where: address,
            count: 20,
            callback,
            errorCallback,
        };

        this._searchManager.geocode(geocodeRequest);
    },

    /**
     * Reset current locations
     */
    resetLocations: function() {
        this._locations = [];
    },

    /**
     * Create new BingMaps locations
     *
     * @param {Array} locationsData
     */
    createLocations: function(locationsData) {
        _.each(locationsData, function createLocation(locationData) {
            this.createLocation(locationData);
        }, this);
    },

    /**
     * Generate a new location for PushPin
     *
     * @param {Object} locationData
     */
    createLocation: function(locationData) {
        if (!locationData.latitude || !locationData.longitude) {
            return;
        }

        const location = new Microsoft.Maps.Location(locationData.latitude, locationData.longitude);
        const assignedUserNameKey = 'parent_user_name';
        const parentNameKey = 'parent_name';
        const parentIdKey = 'parent_id';
        const parentTypeKey = 'parent_type';

        location.sugarRecordDetails = {
            address: locationData.address,
            country: locationData.country,
            assignedUserName: locationData[assignedUserNameKey],
            name: locationData[parentNameKey],
            id: locationData[parentIdKey],
            module: locationData[parentTypeKey],
        };

        this._locations.push(location);
    },

    /**
     * Reset current pushpins
     */
    resetPushPins: function() {
        this._pushPins = [];
    },

    /**
     * Generate a new PushPin based on long and lat from record.
     *
     * @param {Microsoft.Maps.Location} location
     */
    _createPushPin: function(location) {
        const recordDetails = location.sugarRecordDetails;

        const pushpinMeta = {
            title: recordDetails.name,
            username: recordDetails.assignedUserName,
            icon: this._pushPinIcon,
            address: recordDetails.address,
            country: recordDetails.country,
            id: recordDetails.id,
            recordRoute: '#' + app.router.buildRoute(recordDetails.module, recordDetails.id),
        };

        const pushPin = new Microsoft.Maps.Pushpin(location, pushpinMeta);

        Microsoft.Maps.Events.addHandler(pushPin, 'mouseover', _.bind(this.showInfoBox, this, pushpinMeta));
        Microsoft.Maps.Events.addHandler(this._map, 'click', _.bind(this.hideInfoBox, this));

        this._pushPins.push(pushPin);
    },

    /**
     * Generate an array of PushPins
     */
    createPushPins: function() {
        _.each(this._locations, function processRecord(location) {
            this._createPushPin(location);
        }, this);
    },

    /**
     * Add pushpins to be drawed
     */
    drawPushPins: function() {
        this._map.entities.push(this._pushPins);
    },

    /**
     * Called on pushpin hover
     *
     * @param {Object} meta
     * @param {Event} e
     */
    showInfoBox: function(meta, e) {
        const location = e.target.getLocation();

        const handlebars = app.template.getField('bing-map', 'infobox')(meta);

        if (this._infoBox) {
            this._infoBox.setLocation(location);
            this._infoBox.setOptions({
                htmlContent: handlebars,
                visible: true,
            });
        } else {
            this._infoBox = new Microsoft.Maps.Infobox(location, {
                htmlContent: handlebars
            });

            this._infoBox.setMap(this._map);
        }

        setTimeout(_.bind(this.registerInfoBoxStreetViewClick, this, meta, location), 500);
    },

    /**
     * Register StreetView Click event on InfoBox
     *
     * @param {Object} meta
     * @param {Object} location
     */
    registerInfoBoxStreetViewClick: function(meta, location) {
        const $infoBox = $(this._infoBox._getInfoboxElement());
        const $streetViewIcon = $infoBox.find('[data-action=street-view]');

        $streetViewIcon.off('click', _.bind(this.openStreetView, this, meta, location));
        $streetViewIcon.on('click', _.bind(this.openStreetView, this, meta, location));
    },

    /**
     * Called on infobox leave
     *
     * @param {Event} e
     */
    hideInfoBox: function(e) {
        if (this._infoBox) {
            this._infoBox.setOptions({visible: false});
        }
    },

    /**
     * Open Street View
     *
     * @param {Object} meta
     * @param {Object} location
     * @param {Event} e
     */
    openStreetView: function(meta, location, e) {
        this.hideInfoBox();

        this._map.setView({
            center: location,
            zoom: 18,
            mapTypeId: Microsoft.Maps.MapTypeId.streetside,
            streetsideOptions: {
                overviewMapMode: Microsoft.Maps.OverviewMapMode.hidden,
                showCurrentAddress: true,
                showProblemReporting: false,
                disablePanoramaNavigation: true,
                showHeadingCompass: false,
                showZoomButtons: false,
                onErrorLoading: function() {
                    app.alert.show('maps-street-points-not-available', {
                        level: 'warning',
                        messages: app.lang.get('LBL_MAPS_STREET_VIEW_POINT_NOT_AVAILABLE'),
                        autoClose: true,
                    });
                },
            }
        });

    },

    /**
     * Get address data from a point
     *
     * @param {Object} location
     */
    getAddressFromPoint(location, successCallback, errorCallback) {
        this._searchManager.reverseGeocode({
            callback: successCallback,
            errorCallback: errorCallback,
            location: location
        });
    },

    /**
     * Clear the map
     */
    clearMap: function() {
        this._map.entities.clear();
        this.resetLocations();
        this.resetPushPins();
    },

    /**
     * Center the map based on the added PushPins
     *
     * @param {Object} mapViewOptions
     */
    centerMap: function(mapViewOptions) {
        mapViewOptions = mapViewOptions ? mapViewOptions : {};

        if (_.isEmpty(this._locations)) {
            this._map.setView({
                zoom: 1
            });
            return;
        }

        const centerMap = Microsoft.Maps.LocationRect.fromLocations(this._locations);

        if (_.has(mapViewOptions, 'height')) {
            centerMap.height = mapViewOptions.height;
        }

        if (_.has(mapViewOptions, 'width')) {
            centerMap.width = mapViewOptions.width;
        }

        let mapOptions = {
            mapTypeId: mapViewOptions.mapTypeId ? mapViewOptions.mapTypeId : Microsoft.Maps.MapTypeId.road,
        };

        if (_.keys(this._locations).length > 1) {
            mapOptions.bounds = centerMap;
        } else {
            const firstLocationKey = _.chain(this._locations)
                                    .keys()
                                    .first()
                                    .value();

            mapOptions.center = this._locations[firstLocationKey];
        }

        this._map.setView(mapOptions);

        if (_.has(mapViewOptions, 'zoom')) {
            let correctZoom = Infinity;

            if (mapOptions.bounds) {
                correctZoom = this._map.getZoom();
                mapOptions.center = mapOptions.bounds.center;

                delete mapOptions.bounds;
            }

            mapOptions.zoom = Math.min(Number(mapViewOptions.zoom), correctZoom);

            this._map.setView(mapOptions);
        }
    },
});
