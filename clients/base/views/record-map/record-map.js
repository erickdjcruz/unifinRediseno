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
 * Record View Map
 *
 * @class View.Views.Base.BaseRecordMapView
 * @alias SUGAR.App.view.views.BaseRecordMapView
 * @extends View.View
 */
 ({
    extendsFrom: 'ListMapView',

    events: {
        'click [data-action=close]': 'closeDrawer',
    },

    /**
     * Property initialization, nothing to do for this view
     *
     * @param {Object} options
     */
    _initProperties: function() {
        this._super('_initProperties', arguments);

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

        if (this._showDirections) {
            this._createAndShowDirections();
        } else {
            this._createAndShowMap();
        }
    },

    /**
     * Create the map and display it
     */
    _createAndShowMap: function() {
        const records = _.extend({}, this.context.get('collection').models);

        this.createMap(records);
    },

    /**
     * Create the map and show the optimal route
     */
    _createAndShowDirections: function() {
        const records = _.extend({}, this.context.get('collection').models);

        this.createMap(records, {
            directions: {
                startPoint: {
                    module: 'Users',
                    id: app.user.id,
                },
            },
        });
    },

    /**
     * Close maps directions drawer
     */
    closeDrawer: function() {
        app.drawer.close();
    },
});
