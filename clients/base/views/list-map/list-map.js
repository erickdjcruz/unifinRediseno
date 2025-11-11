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
 * @class View.Views.Base.BaseListMapView
 * @alias SUGAR.App.view.views.BaseListMapView
 * @extends View.View
 */
({
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
        this._mapController = null;
        this._locations = [];
        this._mapOptions = {};
        this._showCloseButton = true;
        this._showExpandButton = true;
        this._showMapToPdfButton = true;
        this._showMapShareButton = true;
        this._showDirections = app.controller.context.showDirections;
        this._isDashlet = false;
    },

    /**
     * Create the map
     *
     * @param {Data.Bean[]} records
     * @param {Object} options
     */
    createMap: function(records, options = {}) {
        this.onMapExpanded(false);

        var fieldContainer = this.$('div[data-container="main-map-container"]');
        fieldContainer.show();

        this._mapOptions = options;

        if (this._mapOptions.directions) {
            this._fetchRecordsForDirections(records);
        } else {
            this._fetchTargetRecords(this.module, records, [], {});
        }
    },

    /**
     * Fetch either by mapping field or related record
     *
     * @param {string} module
     * @param {Array} records
     * @param {Array} additionalRecords
     * @param {Object} additionalMappings
     */
    _fetchTargetRecords: function(module, records, additionalRecords, additionalMappings) {
        const mapModuleData = app.config.maps.modulesData[module];
        const selectedRecordsCount = _.keys(records).length;
        const recordsId = _.pluck(records, 'id').concat(additionalRecords);
        const firstRecord = _.first(records);
        const targetModule = firstRecord ? firstRecord.get('_module') : module;
        const targetModuleData = app.config.maps.modulesData[targetModule];

        if (mapModuleData && mapModuleData.mappingType === 'relateRecord') {
            this._fetchRelatedRecordsLocationData(
                mapModuleData.mappingRecord,
                selectedRecordsCount,
                additionalRecords,
                additionalMappings,
                recordsId
            );
        } else if (targetModuleData && targetModuleData.mappingType === 'relateRecord') {
            this._fetchRelatedRecordsLocationData(
                targetModuleData.mappingRecord,
                selectedRecordsCount,
                additionalRecords,
                additionalMappings,
                recordsId
            );
        } else {
            const mapType = this._mapOptions.directions ? '_buildMapWithDirections' : '_buildMap';

            this._fetchRecordsLocationData(_.bind(this[mapType], this, {}, selectedRecordsCount), recordsId);
        }
    },

    /**
     * Fetch starting point and target records
     *
     * @param {Array} records
     */
    _fetchRecordsForDirections: function(records) {
        const startPoint = this._mapOptions.directions.startPoint;
        const startPointModule = startPoint.module;
        const startPointId = startPoint.id;

        const mapModuleData = app.config.maps.modulesData[startPointModule];

        if (mapModuleData && mapModuleData.mappingType === 'relateRecord' && !_.isEmpty(mapModuleData.mappingRecord)) {
            const mappingRecordKey = _.chain(mapModuleData.mappingRecord)
                                        .keys()
                                        .first()
                                        .value();

            const startPointModel = app.data.createBean(startPointModule, {id: startPointId});

            startPointModel.fetch({
                fields: [mappingRecordKey, 'name'],
                success: _.bind(function mapsModelFetched(fetchedModel) {
                    let mappings = {};
                    let recordsIds = [];

                    let recordId = fetchedModel.get(mappingRecordKey);
                    const modelId = fetchedModel.get('id');

                    if (_.isEmpty(recordId.records)) {
                        app.alert.show('maps-invalid-starting-point', {
                            level: 'warning',
                            messages: 'LBL_MAPS_MISSING_GEOCODING_START_POINT',
                        });
                        this._fetchTargetRecords(this.module, records, [], []);
                        return;
                    }

                    recordId = _.first(recordId.records).id;

                    mappings[modelId] = {
                        recordId: recordId,
                        table: {
                            'parent_name': fetchedModel.get('name')
                        }
                    };

                    recordsIds.push(recordId);

                    this._mapOptions.directions.startPoint = {
                        id: recordId,
                        module: fetchedModel._module
                    };

                    this._fetchTargetRecords(this.module, records, recordsIds, mappings);
                }, this),
                error: function() {
                    this._fetchTargetRecords(this.module, records, [], {});
                },
            });
        } else {
            this._fetchTargetRecords(this.module, records, [startPointId], {});
        }
    },

    /**
     * Get related records location
     *
     * @param {Object} mappingRecord
     * @param {number} selectedRecordsCount
     * @param {Array} additionalRecords
     * @param {Object} additionalMappings
     * @param {Array} recordsIds
     */
    _fetchRelatedRecordsLocationData: function(
        mappingRecord,
        selectedRecordsCount,
        additionalRecords,
        additionalMappings,
        recordsIds
    ) {
        if (_.isEmpty(mappingRecord)) {
            const mapType = this._mapOptions.directions ? '_buildMapWithDirections' : '_buildMap';
            this._fetchRecordsLocationData(_.bind(this[mapType], this, {}, selectedRecordsCount), []);
            return;
        }

        const context = app.controller.context;
        const massCollection = context.get('mass_collection');
        const targetCollection = massCollection ? massCollection : this.collection;
        const mapsCollection = targetCollection.clone();
        let modelsIds = _.pluck(mapsCollection.models, 'id');

        if (recordsIds) {
            modelsIds = recordsIds;
        }

        mapsCollection.filterDef = [{'id': {'$in': modelsIds}}];

        const mappingRecordKey = _.chain(mappingRecord)
                                    .keys()
                                    .first()
                                    .value();

        // certain target collections have custom module, clone will not pass that along
        if (!mapsCollection.module && !_.isEmpty(this.collection.models)) {
            mapsCollection.module = this.collection.models[0].module;
        }

        mapsCollection.fetch({
            limit: targetCollection.length,
            fields: [mappingRecordKey, 'name'],
            success: _.bind(function mapsCollectionFetched(updatedCollection) {
                let mappings = additionalMappings;
                let recordsIds = additionalRecords;

                _.each(updatedCollection.models, function getRelatedKey(model) {
                    const modelId = model.get('id');
                    let recordId = model.get(mappingRecordKey);

                    if (recordId) {
                        if (_.isEmpty(recordId.records)) {
                            return;
                        }

                        recordId = _.first(recordId.records).id;
                    } else {
                        recordId = modelId;
                    }


                    mappings[modelId] = {
                        recordId: recordId,
                        table: {
                            'parent_name': model.get('name')
                        }
                    };

                    recordsIds.push(recordId);
                });

                const mapType = this._mapOptions.directions ? '_buildMapWithDirections' : '_buildMap';
                this._fetchRecordsLocationData(_.bind(this[mapType], this, mappings, selectedRecordsCount), recordsIds);
            }, this),
            error: _.bind(function mapsCollectionFetched() {
                const mapType = this._mapOptions.directions ? '_buildMapWithDirections' : '_buildMap';
                this._fetchRecordsLocationData(_.bind(this[mapType], this, {}, selectedRecordsCount), []);
            }, this),
        });
    },

    /**
     * Get current records location
     *
     * @param {Function} successCallback
     * @param {Array} recordsIds
     */
    _fetchRecordsLocationData: function(successCallback, recordsIds) {
        const geocodeCollection = app.data.createBeanCollection('Geocode');

        if (_.isEmpty(recordsIds)) {
            successCallback(geocodeCollection);
            return;
        }

        geocodeCollection.filterDef = [
            {
                'parent_id': {
                    '$in': recordsIds
                }
            },
            {
                'geocoded': 1
            },
            {
                'deleted': 0
            }
        ];

        geocodeCollection.fetch({
            limit: recordsIds.length,
            success: successCallback
        });
    },

    /**
     * Create the field map
     *
     * @param {Object} mappings
     * @param {number} selectedRecordsCount
     * @param {Data.BeanCollection[]} collection
     */
    _createFieldMap: function(mappings, selectedRecordsCount, collection) {
        this._disposeMap();

        var fieldContainer = this.$('div[data-container="main-map-container"]');

        this._locations = {};

        _.each(collection.models, function createLocations(model) {
            let locationData = model.toJSON();

            const related = _.filter(mappings, function getRelatedRecords(mapping, relatedId) {
                mapping.relatedId = relatedId;
                return mapping.recordId === model.get('parent_id');
            });

            if (_.isEmpty(related)) {
                const modelId = model.get('id');

                this._locations[modelId] = locationData;
                return;
            }

            // if we have multiple locations in the exact same spot we spread them in a circle
            let counter = 1;
            let stepDegrees = 20;
            let radius = 0.0002;

            _.each(related, function goThroughRelated(data) {
                let relatedLocationData = app.utils.deepCopy(locationData);

                _.each(data.table, function mapValues(fieldValue, fieldName) {
                    relatedLocationData[fieldName] = fieldValue;
                });

                relatedLocationData.latitude = locationData.latitude + radius * Math.cos(counter * stepDegrees);
                relatedLocationData.longitude = locationData.longitude + radius * Math.sin(counter * stepDegrees);

                counter++;

                this._locations[data.relatedId] = relatedLocationData;
            }, this);
        }, this);

        if (_.keys(this._locations).length === 0 && !this._isDashlet) {
            app.alert.show('maps-invalid-records', {
                level: 'info',
                messages: 'LBL_MAPS_MISSING_GEOCODING_RECORD',
            });
        } else if (selectedRecordsCount > this._locations.length && !this._isDashlet) {
            app.alert.show('maps-invalid-records', {
                level: 'info',
                messages: 'LBL_MAPS_MISSING_GEOCODING_RECORDS',
            });
        }

        // we simply create the formula builder field
        this._mapController = app.view.createField({
            def: {
                type: 'bing-map',
                name: 'BingMap'
            },
            view: this,
            viewName: 'main-map-container',
            showCloseButton: this._showCloseButton,
            showExpandButton: this._showExpandButton,
            showMapToPdfButton: this._showMapToPdfButton,
            showMapShareButton: this._showMapShareButton,
        });

        this._mapController.render();
        this._mapController.createMap();

        this.$('[data-widget=list-map-loading]').hide();

        fieldContainer.append(this._mapController.$el);

        if (this.name === 'list-map') {
            this.$('.map-loading-screen-overlay').height(this.$el.height());
        }

        this.listenTo(this._mapController, 'map:close', this.onMapClose, this);
        this.listenTo(this._mapController, 'map:expand', this.onMapExpanded, this);
        this.listenTo(this._mapController, 'map:save:pdf', this.onMapSavePdf, this);
        this.listenTo(this._mapController, 'map:share:email', this.onMapEmailed, this);
    },

    /**
     * Create the map and show directions
     *
     * @param {Object} mappings
     * @param {number} selectedRecordsCount
     * @param {Data.BeanCollection[]} collection
     */
    _buildMapWithDirections: function(mappings, selectedRecordsCount, collection) {
        this._createFieldMap(mappings, selectedRecordsCount, collection);

        this.listenTo(this._mapController, 'map:directions:load:complete', this.onMapDirectionsReady, this);
    },

    /**
     * Create the map
     *
     * @param {Object} mappings
     * @param {number} selectedRecordsCount
     * @param {Data.BeanCollection[]} collection
     */
    _buildMap: function(mappings, selectedRecordsCount, collection) {
        this._createFieldMap(mappings, selectedRecordsCount, collection);

        this.listenTo(this._mapController, 'map:load:complete', this.onMapReady, this);
    },

    /**
     * Change list view height depending on map height
     *
     * @param {bool} fullscreen
     */
    adjustListViewHeight: function(fullscreen) {
        if (!this.layout) {
            return;
        }

        const listView = this.layout.getComponent('filterpanel');

        if (!listView) {
            return;
        }

        if (fullscreen) {
            listView.$el.height('100%');
        } else {
            const parentHeight = listView.$el.parent().height();
            const mapHeight = this.$el.height();
            const diff = parentHeight - mapHeight;
            const correctHeight = diff * 100 / parentHeight + '%';

            // list view height + map height must always be 100%
            listView.$el.height(correctHeight);
            this.$('.map-loading-screen-overlay').height(mapHeight);
        }
    },

    /**
     * Hide map view
     */
    onMapClose: function() {
        const fieldContainer = this.$('div[data-container="main-map-container"]');
        fieldContainer.hide();

        const mapContainer = this.$('.map-holder-list-view').parent();
        mapContainer.removeClass('map-holder-list-view-big').removeClass('map-holder-list-view-small');

        this.adjustListViewHeight(true);
    },

    /**
     * Expand/Collapse map view
     */
    onMapExpanded: function(expand) {
        const classToBeRemoved = expand ? 'map-holder-list-view-small' : 'map-holder-list-view-big';
        const classToBeAdded = expand ? 'map-holder-list-view-big' : 'map-holder-list-view-small';

        const fieldContainer = this.$('.map-holder-list-view').parent();
        fieldContainer.removeClass(classToBeRemoved).addClass(classToBeAdded);

        this.adjustListViewHeight();
    },

    /**
     * Save Map as PDF
     */
    onMapSavePdf: function() {
        if (_.size(this._locations) === 0) {
            app.alert.show('maps-invalid-starting-point', {
                level: 'warning',
                messages: 'LBL_MAPS_ONE_GEOCDED_RECORD_NEEDED',
            });

            return;
        }

        let locations = this._locations;

        const map = this._mapController._map;

        const fromDirections = _.has(this._mapOptions, 'directions');
        const mapType = map.getMapTypeId();
        const mapZoom = map.getZoom();
        const mapBounds = map.getBounds();
        const mapCenter = map.getCenter();
        const mapExpanded = this._isMapExpanded();
        const provider = 'bing';
        const mapMeta = {
            mapType,
            mapZoom,
            mapBounds,
            fromDirections,
            mapExpanded,
            mapCenter,
        };

        if (fromDirections) {
            const startPoint = _.findWhere(this._locations, {
                'parent_id': this._mapOptions.directions.startPoint.id
            });

            if (!startPoint) {
                app.alert.show('maps-invalid-starting-point', {
                    level: 'warning',
                    messages: 'LBL_MAPS_MISSING_GEOCODING_START_POINT',
                });

                return;
            }

            //put the starting point first
            locations = _.chain(locations)
                        .without(startPoint)
                        .unshift(startPoint)
                        .value();

            const itineraryKey = '_itinerary';

            mapMeta.itinerary = this._mapController[itineraryKey];
        }

        if (_.isEmpty(mapMeta.itinerary) && fromDirections) {
            app.alert.show('maps-invalid-itinerary', {
                level: 'warning',
                messages: 'LBL_MAPS_NO_VALID_ITINERARY',
            });

            return;
        }

        const recordsMeta = _.values(locations);

        app.alert.show('maps-generating', {
            level: 'info',
            messages: 'LBL_MAPS_GENERATING',
        });

        const generateMapPath = 'maps/generateMap';
        const requestType = 'create';

        const calbacks = {
            success: _.bind(function(document) {
                const currentTime = new Date().toJSON();
                const name = `Map-${currentTime}.pdf`;

                this.downloadFileLocally(name, document);

                app.alert.dismiss('maps-generating');
            }, this),
            error: _.bind(function() {
                app.alert.dismiss('maps-generating');
            }, this),
        };

        const requestMeta = {
            mapMeta,
            recordsMeta,
            provider
        };
        const apiUrl = app.api.buildURL(generateMapPath, requestType, requestMeta, {});

        app.api.call(requestType, apiUrl, requestMeta, null, calbacks);
    },

    /**
     * Share Map on email
     */
    onMapEmailed: function() {
        if (_.size(this._locations) === 0) {
            app.alert.show('maps-invalid-starting-point', {
                level: 'warning',
                messages: 'LBL_MAPS_ONE_GEOCDED_RECORD_NEEDED',
            });

            return;
        }

        if (_.size(this._locations) > 10) {
            app.alert.show('maps-max-points-exceeded', {
                level: 'warning',
                messages: 'LBL_MAPS_MAX_GEOCDED_RECORD_EXCEDED',
            });

            return;
        }

        const completeMapUrl = this._buildShareMapURL(this._locations, this._mapOptions.directions);
        const pointNameKey = 'parent_name';

        let mapPoints = [];

        _.each(this._locations, function getShareURL(locationData) {
            const locationMapUrl = this._buildShareMapURL([locationData]);

            mapPoints.push({
                url: locationMapUrl,
                name: locationData[pointNameKey],
            });

        }, this);

        const emailBody = app.template.getView('list-map', 'share')({
            completeMapUrl,
            mapPoints
        });

        this._sendMapByEmail(emailBody);
    },

    /**
     * Opens a drawer containing Bing Maps Data
     *
     * @param {Array} body
     */
    _sendMapByEmail: function(body) {
        const emailModel = app.data.createBean('Emails');

        emailModel.set({
            name: app.lang.get('LBL_MAPS_MAP_ON_BING_WEB'),
            description_html: body,
        });

        app.drawer.open({
            layout: 'compose-email',
            context: {
                create: 'true',
                module: 'Emails',
                model: emailModel,
            },
        });
    },

    /**
     * Generate bing map url
     *
     * @param {Array} mapLocations
     * @param {Object} directions
     *
     * @return string
     */
    _buildShareMapURL: function(mapLocations, directions) {
        const baseURL = 'https://www.bing.com/maps?';
        const delimiter = '~';
        const coordsDelimiter = '_';
        const pointNameKey = 'parent_name';
        const parentIdKey = 'parent_id';

        const map = this._mapController._map;
        const center = map.getCenter();
        const zoom = map.getZoom();
        const style = map.getMapTypeId();

        const centerPoint = 'cp=' + center.latitude + delimiter + center.longitude;
        const zoomLvl = '&lvl=' + zoom;
        const mapStyle = '&style=' + style;
        const locType = directions ? '&rtp=' : '&sp=';
        const pointType = directions ? 'pos.' : 'point.';

        let locations = '';
        let startPointId = '';

        if (directions) {
            startPointId = directions.startPoint.id;

            const pointData = _.findWhere(mapLocations, {
                parent_id: startPointId,
            });

            if (pointData) {
                const point = pointData.latitude + coordsDelimiter + pointData.longitude;

                locations = pointType + point + coordsDelimiter + pointData[pointNameKey];
            }
        }

        _.each(mapLocations, function getLocationData(locData) {
            if (locData[parentIdKey] === startPointId) {
                return;
            }

            const prefix = _.isEmpty(locations) ? '' : delimiter;
            const coords = locData.latitude + coordsDelimiter + locData.longitude;

            locations += prefix + pointType + coords + coordsDelimiter + locData[pointNameKey];
        }, this);

        const shareMapURL = baseURL + centerPoint + zoomLvl + mapStyle + locType + encodeURIComponent(locations);

        return shareMapURL;
    },

    /**
     * Check if the map is expanded
     *
     * @return bool
     */
    _isMapExpanded: function() {
        //from subpanel it's always expanded
        if (this.layout.name === 'subpanel-map') {
            return true;
        }

        if (_.has(this._mapController, '_expanded')) {
            return this._mapController._expanded;
        }

        return false;
    },

    /**
     * Downloads a file on the file system
     *
     * @param {string} filename
     * @param {string} content
     */
    downloadFileLocally: function(filename, content) {
        const dataURIToBlob = function(dataURI) {
            let binStr = atob(dataURI);
            let len = binStr.length;
            let arr = new Uint8Array(len);

            for (let i = 0; i < len; i++) {
                arr[i] = binStr.charCodeAt(i);
            }

            return new Blob([arr], {
                type: 'application/octet-stream'
            });
        };

        const blob = dataURIToBlob(content);
        const url = URL.createObjectURL(blob);

        let element = document.createElement('a');
        element.setAttribute('href', url);
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    },

    /**
     * Called when Map is loaded
     */
    onMapReady: function() {
        this._createLocation();
        this._mapController.centerMap();
    },

    /**
     * Called when Directions Map is loaded
     */
    onMapDirectionsReady: function() {
        this._mapController.clearMap();
        this._mapController.createLocations(this._locations);
        this._mapController.centerMap();

        const startPoint = _.findWhere(this._locations, {
            'parent_id': this._mapOptions.directions.startPoint.id
        });

        if (!startPoint) {
            app.alert.show('maps-invalid-starting-point', {
                level: 'warning',
                messages: 'LBL_MAPS_MISSING_GEOCODING_START_POINT',
            });

            this._mapController.clearMap();
            this._mapController.centerMap();

            return;
        }

        const locations = _.chain(this._locations)
                            .without(startPoint)
                            .map(function getAddress(location) {
                                return {
                                    address: location.parent_name,
                                    coords: {
                                        latitude: location.latitude,
                                        longitude: location.longitude
                                    }
                                };
                            }).value();

        this._mapController.showDirections(
            locations,
            {
                address: startPoint.parent_name,
                coords: {
                    latitude: startPoint.latitude,
                    longitude: startPoint.longitude
                }
            }
        );
    },

    /**
     * Create map location
     */
    _createLocation: function() {
        this._mapController.clearMap();
        this._mapController.createLocations(this._locations);
        this._mapController.createPushPins();
        this._mapController.drawPushPins();
    },

    /**
     * Dispose map element
     */
    _disposeMap: function() {
        if (this._mapController) {
            this._mapController.dispose();
            this._mapController = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeMap();
        this._super('_dispose');
    },
});
