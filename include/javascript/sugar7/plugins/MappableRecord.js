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
(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('MappableRecord', ['view'], {

            /**
             * @inheritdoc
             *
             * Add the Map Geocoding Buttons
             */
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    if (!app.user.hasMapsLicense()) {
                        return;
                    }

                    if (!this._isModuleEnabled()) {
                        return;
                    }

                    /**
                     * Geocoding button meta
                     */
                    let geocodingMeta = [
                        {
                            'type': 'rowaction',
                            'event': 'button:geocoding_record:click',
                            'name': 'geocoding_record',
                            'label': 'LBL_MAP_GEOCODING_RECORD',
                            'acl_action': 'view',
                        },
                        {
                            'type': 'divider',
                        },
                        {
                            'type': 'rowaction',
                            'event': 'button:showmap:click',
                            'name': 'showmap_button',
                            'label': 'LBL_MAP_MAP',
                            'acl_action': 'view',
                        },
                        {
                            'type': 'divider',
                        },
                        {
                            'type': 'rowaction',
                            'event': 'button:directionsfromuser:click',
                            'name': 'drawdirections_button',
                            'label': 'LBL_MAP_DIRECTIONS_FROM_USER',
                            'acl_action': 'view',
                        },
                        {
                            'type': 'divider',
                        },
                    ];

                    let dropdown = this.getRecordMainDropdown();

                    if (_.isArray(dropdown)) {
                        dropdown.push(...geocodingMeta);
                        this._registerGeocodingButtonEvents();
                    }

                }, this);
            },

            /**
             * @inheritdoc
             *
             * Clean up associated event handlers.
             */
            onDetach: function(component, plugin) {
                this.context.off('button:geocoding_record:click', this.openManualGeocodingDrawer, this);
                this.context.off('button:directionsfromuser:click', this.showDirectionsFromUser, this);
                this.context.off('button:showmap:click', this.showMap, this);
            },

            /**
             * Register the events for the geocoding button, on the view context
             */
            _registerGeocodingButtonEvents: function() {
                this.context.on('button:geocoding_record:click', this.openManualGeocodingDrawer, this);
                this.context.on('button:directionsfromuser:click', this.showDirectionsFromUser, this);
                this.context.on('button:showmap:click', this.showMap, this);
            },

            /**
             * Start the geocoding process
             */
            openManualGeocodingDrawer: function() {
                app.drawer.open({
                    layout: 'maps-manual-geocoding',
                    module: 'Geocode',
                    context: app.controller.context,
                }, _.bind(function notifyListeners() {
                    this.trigger('maps-manual-geocoding-finished');
                }, this));
            },

            /**
             * Start directions starting from user
             */
            showDirectionsFromUser: function() {
                app.controller.context.showDirections = true;
                app.drawer.open({
                    layout: 'record-map',
                    context: app.controller.context,
                    meta: {
                        showDirections: true,
                    }
                });
            },

            /**
             * Start map
             */
            showMap: function() {
                app.controller.context.showDirections = false;

                app.drawer.open({
                    layout: 'record-map',
                    context: app.controller.context,
                });
            },

            /**
             * Returns the meta object representing the record main dropdown
             *
             * @return {?Array}
             */
            getRecordMainDropdown: function() {
                if (this.meta.buttons) {
                    let mainDropdown = _.filter(this.meta.buttons, function(button) {
                        return button.name === 'main_dropdown';
                    });

                    if (_.isArray(mainDropdown) && _.isEmpty(mainDropdown) === false) {
                        return _.first(mainDropdown).buttons;
                    }
                }

                return null;
            },

            /**
             * Check if the current module is allowed to use this feature
             *
             * @return {boolean}
             */
            _isModuleEnabled: function() {
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
        });
    });
})(SUGAR.App);
