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
 * @class View.Views.Base.BaseMapsDashletView
 * @alias SUGAR.App.view.views.BaseMapsDashletView
 * @extends View.View
 */
({
    extendsFrom: 'ListMapView',
    plugins: ['Dashlet'],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties(options);
        this._registerEvents();
    },

    /**
     * @inheritdoc
     */
    _initProperties: function(options) {
        this._super('_initProperties', [options]);

        this._noAccessTemplate = app.template.get(this.name + '.noaccess');
        this._isDashlet = true;
        this._showExpandButton = false;
        this._showCloseButton = false;
        this._showMapToPdfButton = true;
        this._showMapShareButton = true;
        this._defaultZoom = 15;
        this._mapsEnabled = true;
    },

    /**
     * Listening to external events
     *
     */
    _registerEvents: function() {
        this.listenTo(app.controller.context.get('collection'), 'data:sync:complete', this.refreshClicked, this);
    },

    /**
     * @inheritdoc
     */
    initDashlet: function(viewName) {
        this._mode = viewName;

        if (!this.settings.get('maps_display_zoom')) {
            this.settings.set('maps_display_zoom', this._defaultZoom);
        }

        this.before('render', function() {
            if (!this.model || !this._hasMapAccess()) {
                return this._noAccess();
            }
        });
    },

    /**
     * Renders the no-access template, then aborts further rendering.
     *
     * @return {boolean} Always returns `false`.
     * @private
     */
    _noAccess: function() {
        this.template = this._noAccessTemplate;

        this.$el.empty();
        this.$el.html(this.template(this));

        return false;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this._createAndShowMap();
    },

    /**
     * Called when Map is loaded
     */
    onMapReady: function() {
        const mapsDisplayType = this.settings.get('maps_display_type');
        const zoomLevel = this.settings.get('maps_display_zoom');

        const mapViewOptions = {
            mapTypeId: mapsDisplayType ? mapsDisplayType : this._getDefaultMapType(),
            zoom: zoomLevel ? zoomLevel : this._defaultZoom
        };

        this._createLocation();
        this._mapController.centerMap(mapViewOptions);
    },

    /**
     * Create the map and display it
     */
    _createAndShowMap: function() {
        if (!app.user.hasMapsLicense()) {
            this._mapsEnabled = false;

            this._noAccess();

            return;
        }

        const collection = this.context.get('collection');

        if (!collection || !this._hasMapAccess() || !this.$el) {
            return;
        }

        let records = _.extend({}, this.context.get('collection').models);

        if (this.context.parent && _.isEmpty(records)) {
            records = [this.context.parent.get('model')];
        }

        if (_.isEmpty(records)) {
            this.$('[data-container=map-no-geocoded-records]').show();
            this.$('[data-widget=list-map-loading]').hide();
        } else {
            this.$('[data-container=map-no-geocoded-records]').hide();
            this.createMap(records);
        }
    },

    /**
     * Refresh the map
     */
    refreshClicked: function() {
        this._createAndShowMap();
    },

    /**
      * Check if the current module has access to the Maps
      *
      * @return {boolean}
      */
    _hasMapAccess: function() {
        if (!_.has(app.config, 'maps')) {
            return false;
        }

        if (!_.has(app.config.maps, 'enabled_modules')) {
            return false;
        }

        const enabledModulesKey = 'enabled_modules';

        const allowedModules = app.config.maps[enabledModulesKey];

        if (allowedModules.indexOf(this.module) > -1) {
            return true;
        }

        return false;
    },

    /**
     * Get default value for MapType
     *
     * @return {string}
     */
    _getDefaultMapType: function() {
        const mapTypeDropdown = app.lang.getAppListStrings('maps_display_type_list');
        const defaultValue = _.first(_.keys(mapTypeDropdown));

        return defaultValue;
    },
});
