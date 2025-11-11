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
 * @class View.Views.Base.DashletFilterModeView
 * @alias SUGAR.App.view.views.BaseDashletFilterModeView
 * @extends View.View
 */
({
    className: 'w-full h-full',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this._widgets = [];

        const validFilters = this._getValidFilters(this.model.get('filtersDef'));

        this._runtimeFilters = this._getRuntimeFilters(validFilters, []);
        this._noFilters = _.isEmpty(this._runtimeFilters);
        this._dashletSpecificData = this.options.dashletSpecificData || {};
        this._manager = this.options.manager;

        this._noRights = false;
        if (!_.isUndefined(this._dashletSpecificData.currentUserRestrictedDashlet)) {
            this._noRights = this._dashletSpecificData.currentUserRestrictedDashlet;
        }
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'filter-groups-updated', this.manageGroupWidgets, this);
        this.listenTo(this.context, 'show-dashlet-loading', this.showLoadingScreen, this);
    },

    /**
     * Returns all the runtime filters
     *
     * @param {Object} filters
     * @param {Array} runtimeFilters
     *
     * @return {Array}
     */
    _getRuntimeFilters: function(filters, runtimeFilters) {
        _.each(filters, function goThroughFilters(filter) {
            if (_.isEmpty(filter.operator)) {
                if (filter.runtime === 1) {
                    runtimeFilters.push(filter);
                }
            } else {
                const validFilters = this._getValidFilters(filter);

                runtimeFilters = this._getRuntimeFilters(validFilters, runtimeFilters);
            }
        }, this);

        return runtimeFilters;
    },

    /**
     * Returns only filters, without operators
     * @param {Object} filter
     *
     * @return {Array}
     */
    _getValidFilters: function(filter) {
        return _.chain(filter)
            .values()
            .filter(function ignoreOperator(filterData) {
                return _.isObject(filterData);
            })
            .value();
    },

    /**
     * Create widgets and display them
     */
    show: function() {
        if (!_.isUndefined(this.layout)) {
            // hide all toolbar actions except for collapse
            this.layout.getComponent('dashlet-toolbar').$('.btn-group').hide();
        }

        this.render();
        this._createWidgets();
    },

    /**
     * Hide widgets and destroy them
     */
    hide: function() {
        if (!_.isUndefined(this.layout)) {
            // show all toolbar actions except for collapse
            this.layout.getComponent('dashlet-toolbar').$('.btn-group').show();
        }

        this.dispose();
    },

    /**
     * Handle the state of each widget in the dashlet
     *
     * @param {Object} filterGroups
     * @param {string} targetGroupId
     */
    manageGroupWidgets: function(filterGroups, targetGroupId) {
        this.resetWidgets();

        if (!filterGroups || _.size(filterGroups) < 1) {
            return;
        }

        this.highlightWidgets(filterGroups, targetGroupId);
        this.addCheckmarksToWidgets(filterGroups, targetGroupId);
        this.disableWidgets(filterGroups, targetGroupId);
        this.massageWidgetsPermissions();
    },

    /**
     * Make all widgets available
     */
    resetWidgets: function() {
        _.each(this._widgets, function resetWidget(widget) {
            widget.toggleAvailable(true);
        }, this);
    },

    /**
    * Disable widgets if the user has no access to it
    */
    massageWidgetsPermissions: function() {
        if (!this._widgets) {
            return;
        }

        _.each(this._widgets, function massageWidget(widget) {
            if (widget._hasAccess === false) {
                widget._disableWidget();
            }
        });
    },

    /**
     * Highlight all the elements inside this dashlet that
     * correspond to the target group
     *
     * @param {Object} filterGroups
     * @param {string} targetGroupId
     */
    highlightWidgets: function(filterGroups, targetGroupId) {
        const filterGroup = filterGroups[targetGroupId];

        const targetField = _.find(filterGroup.fields, function findField(field) {
            return field.dashletId === this.layout.options.dashletMetaId;
        }, this);

        if (_.isUndefined(targetField)) {
            return;
        }

        this.controlWidget(targetField, 'toggleHighlight');
    },

    /**
     * Add checkmarks to elements from the other groups
     *
     * @param {Object} filterGroups
     * @param {string} targetGroupId
     */
    addCheckmarksToWidgets: function(filterGroups, targetGroupId) {
        _.each(filterGroups, function goThroughGroups(group, groupId) {
            if (groupId === targetGroupId) {
                return;
            }

            _.each(group.fields, function goThroughFields(field) {
                this.controlWidget(field, 'toggleChecked');
            }, this);
        }, this);
    },

    /**
     * Disable widgets that do not match the type of the chose filter group
     *
     * @param {Object} filterGroups
     * @param {string} targetGroupId
     */
    disableWidgets: function(filterGroups, targetGroupId) {
        const filterGroup = filterGroups[targetGroupId];
        const targetField = _.first(filterGroup.fields);

        if (_.isUndefined(targetField) || _.isUndefined(targetField.fieldDef)) {
            return;
        }

        _.each(this._widgets, function disableWidget(widget) {
            widget.manageWidgetState({
                field: {
                    type: filterGroup.fieldType,
                    options: targetField.fieldDef.options,
                    function: targetField.fieldDef.function,
                    function_bean: targetField.fieldDef.function_bean,
                    module: targetField.fieldDef.module || targetField.targetModule,
                },
                targetModule: targetField.targetModule,
            });
        }, this);
    },

    /**
     * Does different actions on the widget (toggleHighlight, toggleAvailable etc.)
     *
     * @param {Object} targetField
     * @param {string} action
     */
    controlWidget: function(targetField, action) {
        const tableKeyName = 'table_key';

        if (_.isUndefined(this.layout)) {
            return;
        }

        const targetWidget = _.find(this._widgets, function findWidget(widget) {
            const filterData = widget.getFilterData();
            return filterData.name === targetField.fieldName &&
                    filterData[tableKeyName] === targetField.tableKey &&
                    this.layout.options.dashletMetaId === targetField.dashletId;
        }, this);

        if (_.isUndefined(targetWidget)) {
            return;
        }

        targetWidget._hasAccess ? targetWidget[action](true) : targetWidget[action](false);
    },

    /**
     * Create the widgets
     */
    _createWidgets: function() {
        this._disposeWidgets();

        _.each(this._runtimeFilters, function createWidget(filter) {
            const widget = app.view.createView({
                context: this.context,
                type: 'dashlet-filter-widget',
                module: this.module,
                model: this.model,
                layout: this.layout,
                filterData: filter,
                fullTableList: this.model.get('fullTableList'),
                dashletSpecificData: this._dashletSpecificData,
            });

            widget.render();
            this.$('[data-container="filter-widgets-container"]').append(widget.$el);

            this._widgets.push(widget);
        }, this);
    },

    /**
     * Toggle loading screen
     */
    showLoadingScreen: function() {
        this._manager.isDashletLoading = true;

        this.$('.data-skeleton-loader').toggleClass('hidden', false);
        this.$('.dashboard-filter-mode-container').toggleClass('hidden', true);
    },

    /**
     * Dispose the widgets
     */
    _disposeWidgets: function() {
        _.each(this._widgets, function disposeWidget(widget) {
            widget.dispose();
        }, this);

        this._widgets = [];
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeWidgets();

        if (this._manager) {
            this._manager.isDashletLoading = false;
        }

        this._super('_dispose');
    },
});
