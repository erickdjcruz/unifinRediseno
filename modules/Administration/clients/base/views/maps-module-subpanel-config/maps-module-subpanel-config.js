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
 * @class View.Views.Base.AdministrationMapsModuleSubpanelConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsModuleSubpanelConfigView
 */
({
    plugins: [
        'ReorderableColumns',
    ],

    /**
     * Event listeners
     */
    events: {
        'click [data-action=reset-default]': 'resetDefault',
        'click [data-action=add-column]': 'addColumn',
        'click [data-action=remove-column]': 'removeColumn',
        'change .field_map_select': 'fieldsChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties(options);
        this._registerEvents();
    },

    /**
     * Property initialization
     *
     * @param {Object} options
     */
    _initProperties: function(options) {
        if (options.widgetModule) {
            this.widgetModule = options.widgetModule;

            this._initDefaultData();

            const widgetFields = app.metadata.getModule(this.widgetModule).fields;

            if (!_.isEmpty(widgetFields)) {
                this._moduleFields = app.utils.maps.arrayToObject(
                    _.chain(widgetFields)
                    .filter(function removeLinks(widgetField) {
                        return widgetField.type !== 'link' && widgetField.dbType !== 'id';
                    })
                    .map(function buildFields(field) {
                        let data = {};

                        data[field.name] = field.name;

                        return data;
                    }, this)
                    .value()
                );
            }
        }
    },

    /**
     * Default Properties initialization
     *
     * @param {bool} force
     */
    _initDefaultData: function(force) {
        const module = this.widgetModule;
        const _modulesData = this.context.safeRetrieveModulesData(module);

        const key = `${module}:maps-subpanel-list:visible-fields`;
        const savedSubpanelConfig = app.user.lastState.get(`${key}:admin:config`);

        if (savedSubpanelConfig) {
            _modulesData[module].subpanelConfig = savedSubpanelConfig;
        }

        if (_.isEmpty(_modulesData[module].subpanelConfig) || force) {
            _modulesData[module].subpanelConfig = [];
        }

        this.model.set('maps_modulesData', _modulesData);

        this._fields = {
            all: _modulesData[module].subpanelConfig
        };

        if (force) {
            this.render();
        }
    },

    /**
     * Register context event handlers
     *
     */
    _registerEvents: function() {
        this.listenTo(this, 'list:reorder:columns', this.subpanelColumnsOrderChanged, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render', arguments);

        this._disposeSelect2Elements();
        this._buildSelect2s();
        this._buildDraggable();
    },

    /**
     * Build select2 ui elements
     */
    _buildSelect2s: function() {
        this.$('.field_map_select').select2({
            closeOnSelect: true,
            containerCssClass: 'select2-choices-pills-close',
            width: '100%',
        });

        this.$('.select2-choices').removeClass('maps-input-label').addClass('maps-input-label');

        _.each(this._fields.all, function setDefaultValues(column) {
            this.$('[data-fieldname="' + column.name + '-select"]').select2('val', column.fieldName);
        }, this);
    },

    /**
     * Build draggable ui elements
     */
    _buildDraggable: function() {
        this.$('.ui-draggable').draggable({
            cancel: null
        });

        this.$('.ui-draggable input').click(function() {
            $(this).focus();
        });
    },

    /**
     * Reorder fields array
     *
     * @param {Object} fields
     * @param {Array} newOrder
     */
    subpanelColumnsOrderChanged: function(fields, newOrder) {
        // update position
        _.each(newOrder, function reorderColumns(columnId, position) {
            _.each(this._fields.all, function changeOrder(column) {
                if (column.name === columnId) {
                    column.position = position;
                }
            }, this);
        }, this);

        //sort with the new positions
        this._fields.all.sort(function sortByPos(fColumn, sColumn) {
            return fColumn.position - sColumn.position;
        });

        const module = this.widgetModule;
        const _modulesData = this.context.safeRetrieveModulesData(module);

        _modulesData[module].subpanelConfig = this._fields.all;

        this._resetModuleSubpanelOrder(module);

        this.model.set('maps_modulesData', _modulesData);
        this.model.trigger('change', this.model);
    },

    /**
     * Resets subpanel columns order
     *
     * @param {string} module
     */
    _resetModuleSubpanelOrder: function(module) {
        const key = `${module}:maps-subpanel-list:visible-fields`;

        app.user.lastState.remove(key);
        app.user.lastState.remove(`${key}:admin:config`);
    },

    /**
     * Restore layout config data to default
     *
     * @param {jQuery} e
     */
    resetDefault: function(e) {
        this._initDefaultData(true);
    },

    /**
     * Adds a new column to the subpanel layout
     *
     * @param {jQuery} e
     */
    addColumn: function(e) {
        const module = this.widgetModule;
        const _modulesData = this.context.safeRetrieveModulesData(module);

        _modulesData[module].subpanelConfig.push({
            name: app.utils.generateUUID(),
            label: app.lang.getModString('LBL_ID', module),
            fieldName: 'id',
            position: _modulesData[module].subpanelConfig.length
        });

        this.model.set('maps_modulesData', _modulesData);
        this._fields.all = _modulesData[module].subpanelConfig;

        this._resetModuleSubpanelOrder(module);

        this.render();
    },

    /**
     * Remove one of the subpanel column
     *
     * @param {jQuery} e
     */
    removeColumn: function(e) {
        const module = this.widgetModule;
        const columnId = e.currentTarget.dataset.fieldname;
        const _modulesData = this.context.safeRetrieveModulesData(module);
        let columnIndex = -1;

        _.each(_modulesData[module].subpanelConfig, function changeLabel(column, index) {
            if (column.name === columnId) {
                columnIndex = index;
            }
        }, this);

        if (columnIndex > -1) {
            _modulesData[module].subpanelConfig.splice(columnIndex, 1);
        }

        this.model.set('maps_modulesData', _modulesData);
        this._fields.all = _modulesData[module].subpanelConfig;

        this._resetModuleSubpanelOrder(module);

        this.render();
    },

    /**
     * Manages column field changes
     *
     * @param {jQuery} e
     */
    fieldsChanged: function(e) {
        const module = this.widgetModule;
        const columnId = e.currentTarget.dataset.type;
        const fieldName = e.val;
        const _modulesData = this.context.safeRetrieveModulesData(module);
        const fieldMeta = app.metadata.getModule(module).fields[fieldName];
        const fieldLabelKey = fieldMeta.vname ? fieldMeta.vname : fieldMeta.label;

        let fieldLabel = app.lang.getModString(fieldLabelKey, module);
        fieldLabel = fieldLabel ? fieldLabel : app.lang.get(fieldLabelKey);

        _.each(_modulesData[module].subpanelConfig, function changeLabel(column) {
            if (column.name === columnId) {
                column.fieldName = fieldName;
                column.label = fieldLabel;
            }
        }, this);

        this.model.set('maps_modulesData', _modulesData);
        this._fields.all = _modulesData[module].subpanelConfig;

        this._resetModuleSubpanelOrder(module);

        this.render();
    },

    /**
     * Dispose a select2 element
     */
    _disposeSelect2: function(name) {
        this.$('[data-fieldname=' + name + ']').select2('destroy');
    },

    /**
     * Dispose all select2 elements
     */
    _disposeSelect2Elements: function() {
        _.each(this._fields.all, function setDefaultValues(column) {
            this._disposeSelect2(column.name);
        }, this);

        this._disposeSelect2('field_map_select');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSelect2Elements();

        this._super('_dispose');
    },
});
