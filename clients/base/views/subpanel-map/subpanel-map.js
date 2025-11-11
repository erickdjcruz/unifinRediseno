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
 * Maps widget Top view
 *
 * @class View.Views.Base.SubpanelMapView
 * @alias SUGAR.App.view.views.BaseSubpanelMapView
 * @extends View.Views.Base.BaseListMapView
 */
 ({
    /**
     * @inheritdoc
     */
    extendsFrom: 'ListMapView',

    /**
     * @inheritdoc
     */
    events: {
        'click [data-action=close]': 'closeDrawer',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization, nothing to do for this view
     *
     */
    _initProperties: function() {
        this._super('_initProperties');

        this._showCloseButton = false;
        this._showExpandButton = false;
        this._showMapToPdfButton = true;
        this._showMapShareButton = true;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.layout &&
            this.layout.options &&
            this.layout.options.def &&
            this.layout.options.def.showDirections) {
            this._drawMapDirections();
        } else {
            this._drawMapLocations();
        }
    },

    /**
     * Draw map
     */
    _drawMapLocations: function() {
        const massCollection = this.context.get('mass_collection');
        let selectedRecords = [];

        if (massCollection) {
            selectedRecords = _.extend({}, massCollection.models);
        }

        this.createMap(selectedRecords);
    },

    /**
     * Draw map
     */
    _drawMapDirections: function() {
        const massCollection = this.context.get('mass_collection');
        let selectedRecords = [];

        if (massCollection) {
            selectedRecords = _.extend({}, massCollection.models);
        }

        let mapOptions = {};

        if (this.layout &&
            this.layout.options &&
            this.layout.options.def &&
            this.layout.options.def.startPoint) {
            mapOptions = {
                directions: {
                    startPoint: this.layout.options.def.startPoint,
                },
            };
        }

        this.createMap(selectedRecords, mapOptions);
    },

    /**
     * Close maps directions drawer
     */
    closeDrawer: function() {
        app.drawer.close();
    },
});
