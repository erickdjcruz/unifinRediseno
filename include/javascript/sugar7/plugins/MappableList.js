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
(function register(app) {
    app.events.on('app:init', function init() {
        /**
         * Action Map plugin adds the ability to a Sidecar component
         * to be a host for (and be aware of) MappableList button
         */
        app.plugins.register('MappableList', ['view'], {
            listMapView: null,

            /**
             * @inheritdoc
             */
            onAttach: function(component, plugin) {
                this.on('init', function registerActionrRunner() {
                    this._injectMapButtons();

                    return true;
                });
            },

            /**
             * Unbind events on dispose.
             */
            onDetach: function() {
                if (_.has(this, 'layout') && this.layout && _.isFunction(this.layout.off)) {
                    this.layout.off('list:massmapdraw:fire', null, this);
                    this.layout.off('list:massdirectionsfromuserdraw:fire', null, this);
                    this.layout.off('list:massdirectionsfromrecorddraw:fire', null, this);
                }
            },

            /**
             * Listen for just the map event from the list view
             */
            delegateListFireEvents: function() {
                if (_.has(this, 'layout') && this.layout && _.isFunction(this.layout.on)) {
                    this.layout.on('list:massmapdraw:fire', _.bind(this.handleDrawMap, this));
                    this.layout.on(
                        'list:massdirectionsfromuserdraw:fire',
                        _.bind(this.handleDrawDirectionsFromUser,this)
                    );
                    this.layout.on(
                        'list:massdirectionsfromrecorddraw:fire',
                        _.bind(this.handleDrawDirectionsFromRecord, this)
                    );
                }
            },

            /**
             *
             * @param {UIEvent} e
             */
            handleDrawMap: function(e) {
                //for now we are using the mass colection since
                //it contains the same records
                const selectedRecords = _.extend({}, this.context.get('mass_collection').models);

                this.listMapView.createMap(selectedRecords);
            },

            /**
             * Trigger map creation and show optimal route
             * @param {Object} startPoint
             */
            handleDrawDirections: function(startPoint) {
                const selectedRecords = _.extend({}, this.context.get('mass_collection').models);

                this.listMapView.createMap(selectedRecords, {
                    directions: {
                        startPoint: startPoint,
                    },
                });
            },

            /**
             * Draw directions from user
             * @param {UIEvent} e
             */
            handleDrawDirectionsFromUser: function(e) {
                this.handleDrawDirections({
                    module: 'Users',
                    id: app.user.id
                });
            },

            /**
             * Draw directions from record
             * @param {UIEvent} e
             */
            handleDrawDirectionsFromRecord: function(record) {
                this.handleDrawDirections({
                    module: record.module,
                    id: record.get('id')
                });
            },

            /**
             * Inject map button on module ListView
             */
            _injectMapButtons: function() {
                if (!app.user.hasMapsLicense()) {
                    return false;
                }

                if (!_.has(app.config, 'maps')) {
                    return false;
                }

                if (!_.has(app.config.maps, 'enabled_modules')) {
                    return false;
                }

                const enabledModulesKey = 'enabled_modules';
                const allowedModules = app.config.maps[enabledModulesKey];

                const directionButton = {
                    name: 'drawdirections_button',
                    label: 'LBL_MAP_DIRECTIONS_FROM_USER',
                    events: {
                        click: 'list:massdirectionsfromuserdraw:fire',
                    },
                    type: 'button',
                };
                const mapButton = {
                    name: 'drawmap_button',
                    label: 'LBL_MAP_MAP',
                    events: {
                        click: 'list:massmapdraw:fire',
                    },
                    type: 'button',
                };

                this.listMapView = this._getListMapComponent(this.layout);

                if (!this.listMapView || !this.meta || !_.contains(allowedModules, this.module)) {
                    return;
                }

                this.meta.selection.actions.push(mapButton);
                this.meta.selection.actions.push(directionButton);

                this.delegateListFireEvents();

                if (this.type === 'recordlist') {
                    let insertAtIdx = this.meta.rowactions.actions.findIndex(function find(btn) {
                        return btn.name === 'delete_button' || btn.name === 'unlink_button';
                    });

                    if (insertAtIdx === -1) {
                        insertAtIdx = this.meta.rowactions.actions.length;
                    }

                    const listDirectionButton = _.extend(
                        app.utils.deepCopy(directionButton),
                        {
                            label: 'LBL_MAP_DIRECTIONS_FROM_RECORD',
                            events: {
                                click: 'list:massdirectionsfromrecorddraw:fire',
                            },
                        }
                    );

                    this.meta.rowactions.actions.splice(insertAtIdx, 0, {type: 'divider'});
                    this.meta.rowactions.actions.splice(insertAtIdx + 1, 0, listDirectionButton);
                    this.meta.rowactions.actions.splice(insertAtIdx + 2, 0, {type: 'divider'});
                }
            },

            /**
             * Get the list-map component from layout
             *
             * @param {View.Layout} layout
             *
             * @return {View.View|View.Layout|bool} list-map view component
             */
            _getListMapComponent: function(layout) {
                if (layout) {
                    return layout.getComponent('list-map') || this._getListMapComponent(layout.layout);
                }

                return false;
            }
        });
    });
})(SUGAR.App);
