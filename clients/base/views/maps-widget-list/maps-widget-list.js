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
 * @class View.Views.Base.MapsWidgetListView
 * @alias SUGAR.App.view.views.BaseMapsWidgetListView
 * @extends View.Views.Base.SubpanelListView
 */
({
    extendsFrom: 'SubpanelListView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Before init properties
     */
    _beforeInit: function(options) {
        options.module = app.controller.context.get('module');
    },

    /**
     * Init custom properties
     */
    _initProperties: function() {
        if (this._hasMetaPanels()) {
            this._defaultMetaFields = _.chain(this.meta.panels)
                                    .pluck('fields')
                                    .flatten()
                                    .value();
        }

        this._updateCollectionFilter();
    },

    /**
     * @inheritdoc
     */
    _initOrderBy: function() {
        const orderBy = {
            field: 'maps_distance',
            direction: 'asc'
        };

        return orderBy;
    },

    /**
     * Register custom events
     */
    _registerEvents: function() {
        this.listenTo(this.model, 'change:modules', this._updateCollectionFilter, this);
        this.listenTo(this.model, 'change:filterBy', this._updateCollectionFilter, this);
        this.listenTo(this.model, 'change:unitType', this._updateCollectionFilter, this);
        this.listenTo(this.model, 'change:radius', this._updateCollectionFilter, this);
        this.listenTo(
            this.layout,
            'maps:subpanel:filter:collection:fetched',
            this._updateCollectionFilter,
            this
        );
        this.listenTo(
            this.context,
            'subpanel:massdirectionsfromrecorddraw:fire',
            this.handleDrawDirectionsFromRecord,
            this
        );
        this.listenTo(
            this.context,
            'subpanel:massdirectionsfromuserdraw:fire',
            this.handleDrawDirectionsFromUser,
            this
        );
        this.listenTo(
            this.context,
            'subpanel:massmapdraw:fire',
            this.handleDrawMap,
            this
        );
        this.listenTo(
            this,
            'list:reorder:columns',
            this.reorderMetaFields,
            this
        );
    },

    /**
     * @inheritdoc
     */
    _render: function(options) {
        this._super('_render', [options]);

        this.$('.sortable-row-header-arrows').hide();
        this.$('[data-fieldname="maps_distance"]').find('.sortable-row-header-arrows').show();
    },

    /**
     * Updates collection fields from fields meta
     *
     * @param {Array} newFieldsMeta
     */
    _updateCollectionFields: function(newFieldsMeta) {
        let fields = [];

        if (this._hasMetaPanels()) {
            fields = _.chain(newFieldsMeta)
                    .pluck('name')
                    .filter(function removeDistance(fieldName) {
                        return fieldName !== 'distance';
                    })
                    .value();
        }

        const moduleMeta = app.metadata.getModule(this.model.get('modules'));
        let requiredFields = [];

        if (moduleMeta) {
            requiredFields = _.chain(newFieldsMeta)
                            .filter(function getRelated(field) {
                                return field.type === 'relate';
                            })
                            .map(function getRequirements(field) {
                                return moduleMeta.fields[field.name].id_name;
                            }, this)
                            .value();
        }

        fields.push('my_favorite');
        fields = fields.concat(requiredFields);

        this.context.set('fields', fields);

        this.collection.setOption('fields', fields);
        this.collection.setOption('limit', this.limit);
    },

    /**
     * Checks wether panels are initialized
     *
     * @return {boolean}
     */
    _hasMetaPanels: function() {
        if (!this.meta) {
            return false;
        }

        if (!this.meta.panels) {
            return false;
        }

        return true;
    },

    /**
     * Build and apply collection filter
     */
    _updateCollectionFilter: function(model) {
        const module = this.model.get('modules');
        const filterBy = this.model.get('filterBy');
        const unitType = this.model.get('unitType');
        const radius = this.model.get('radius');
        const contextModel = app.controller.context.get('model');
        const recordLayout = this.closestComponent('record');
        const recordId = recordLayout && recordLayout.model ? recordLayout.model.get('id') : contextModel.get('id');
        const recordModule = recordLayout && recordLayout.module ? recordLayout.module : contextModel.module;
        const mapsModule = `${module}/maps`;

        if (!module) {
            const nextOffsetKey = 'next_offset';

            this.collection.reset();
            this.collection.dataFetched = true;
            this.collection[nextOffsetKey] = -1;

            if (model) {
                this.layout.getComponent('list-bottom').render();
                this._tryChangeMetaFields(true);
            }

            return;
        }

        // trigger render only when module has been changed
        const shouldRerender = model && _.has(model, 'changed') && _.has(model.changed, 'modules');

        this._tryChangeMetaFields(shouldRerender);

        let filtersDef = [
            {
                '$distance': {
                    '$in_radius_from_record': {
                        unitType,
                        radius,
                        recordId,
                        recordModule,
                        requiredFields: this.collection.getOption('fields'),
                    }
                }
            }
        ];

        const moduleFiltersDef = this.layout.getComponent('maps-widget-dropdowns').getAvailableFilters();

        if (moduleFiltersDef && filterBy) {
            const filterModel = _.find(moduleFiltersDef.models, function find(filterModel) {
                const filterLabel = app.lang.get(filterModel.get('name'), null, {module});

                return filterLabel === filterBy;
            });

            if (filterModel) {
                const filterDef = filterModel.get('filter_definition');

                filtersDef = filtersDef.concat(filterDef);
            }
        }

        this.collection.module = mapsModule;

        const newModuleCollection = app.data.createBeanCollection(module);

        this.collection.model = newModuleCollection.model;
        this.collection.filterDef = filtersDef;
        this.collection.fetch({
            success: _.bind(function success() {
                if (this.disposed || !this.context || !this.collection) {
                    return;
                }

                const isPanelCollapsed = this.context.get('collapsed');
                const isCollectionEmpty = this.collection.length === 0;

                if (isPanelCollapsed && isCollectionEmpty) {
                    this.$el.parent().find('.block-footer').toggleClass('hide', true);
                }
            }, this)
        });
    },

    /**
     * Changes panels' fields and rerenders view on need
     *
     * @param {boolean} rerender
     */
    _tryChangeMetaFields: function(rerender) {
        if (!this._hasMetaPanels()) {
            return;
        }

        const module = this.model.get('modules');
        const currentMapsModuleSettings = app.config.maps.modulesData;

        let newFieldsMeta = [];

        _.each(this.meta.panels, function addFieldToPanel(panel) {
            panel.fields = app.utils.deepCopy(this._defaultMetaFields);

            let subpanelConfig = [];

            if (_.has(currentMapsModuleSettings[module], 'subpanelConfig')) {
                subpanelConfig = currentMapsModuleSettings[module].subpanelConfig;
            }

            _.each(subpanelConfig, function addFieldToMeta(field) {
                if (_.pluck(panel.fields, 'name').includes(field.fieldName)) {
                    return;
                }

                const moduleFields = app.metadata.getModule(module).fields;
                const fieldMeta = moduleFields[field.fieldName];
                const mapType = app.metadata.fieldTypeMap;
                const widgetType = mapType[fieldMeta.type] ? mapType[fieldMeta.type] : fieldMeta.type;

                panel.fields.push({
                    name: fieldMeta.name,
                    type: widgetType,
                    label: field.label
                });
            }, this);

            newFieldsMeta = panel.fields;
        }, this);

        this.module = module ? module : this.module;

        this._allListViewsFieldListKey = app.user.lastState.buildKey('field-list', 'list-views', this.module);
        this._thisListViewFieldListKey = app.user.lastState.key('visible-fields', this);

        this._fields = this.parseFields();

        const mapsDistanceField = _.find(this._fields.visible, function getDistanceField(field) {
            return field.name === 'maps_distance';
        });

        if (mapsDistanceField) {
            mapsDistanceField.expectedWidth = 'small';
        }

        this._updateCollectionFields(newFieldsMeta);
        this.orderBy = this._initOrderBy();
        this.collection.orderBy = this.orderBy;

        if (rerender) {
            if (this.orderBy) {
                this.orderBy.field = 'maps_distance';
                this.orderBy.direction = 'asc';
            }

            this.render();
        }
    },

    /**
     * Updates admin meta config with the new columns order
     *
     * @param {Array} fields
     * @param {Array} newOrder
     */
    reorderMetaFields: function(fields, newOrder) {
        const module = this.model.get('modules');
        const currentMapsModuleSettings = app.config.maps.modulesData;

        if (!_.has(currentMapsModuleSettings[module], 'subpanelConfig')) {
            return;
        }

        let subpanelConfig = currentMapsModuleSettings[module].subpanelConfig;
        let newSubpanelOrder = [];
        let startPos = 0;

        _.each(newOrder, function reorder(fieldName) {
            let fieldData = _.filter(subpanelConfig, function getField(field) {
                return field.fieldName === fieldName;
            })[0];

            if (fieldData) {
                fieldData.position = startPos;
                startPos++;

                newSubpanelOrder.push(fieldData);
            }
        });

        app.user.lastState.set(this._thisListViewFieldListKey + ':admin:config', newSubpanelOrder);
    },

    /**
     * @inheritdoc
     */
    setOrderBy: function(event) {

        if (event.currentTarget.dataset.fieldname !== 'maps_distance') {
            return;
        }

        this._super('setOrderBy', [event]);
    },

    /**
     * Trigger map creation and show optimal route
     *
     * @param {Object} startPoint
     */
    handleDrawMap: function(startPoint) {
        app.drawer.open({
            layout: 'subpanel-map',
            context: this.context,
            showDirections: false,
        });
    },

    /**
     * Trigger map creation and show optimal route
     *
     * @param {Object} startPoint
     */
    _handleDrawDirections: function(startPoint) {
        app.drawer.open({
            layout: 'subpanel-map',
            context: this.context,
            showDirections: true,
            startPoint
        });
    },

    /**
     * Draw directions from user
     *
     * @param {UIEvent} e
     */
    handleDrawDirectionsFromUser: function(e) {
        this._handleDrawDirections({
            module: 'Users',
            id: app.user.id
        });
    },

    /**
     * Draw directions from record
     *
     * @param {UIEvent} e
     */
    handleDrawDirectionsFromRecord: function(e) {
        const model = app.controller.context.get('model');
        const id = model.get('id');
        const module = model.module;

        this._handleDrawDirections({
            id,
            module,
        });
    },
});
