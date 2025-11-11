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
 * @class View.Layouts.Base.MultiLineSortingLayout
 * @alias SUGAR.App.view.layouts.BaseMultiLineSortingLayout
 * @extends View.Layout
 */
({
    className: 'sorting-panel flex',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.context, 'app:view:sorting-dropdown:changeDropdownValue', this._sortSortableDataComponent);
        this.listenTo(this.context, 'app:view:sorting-dropdown:clickArrow', this._sortSortableDataComponent);
        this.listenTo(this.context, 'metric:empty', this._clearSortingDropdown);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    },

    /**
     * Sort by new settings.
     * @private
     */
    _sortSortableDataComponent: function() {
        const primarySortingDropdown = this._getPrimarySortComponent();
        const secondarySortingDropdown = this._getSecondarySortComponent();
        const multiLineList = this._getMultiLineList();
        let orderByPrimary = primarySortingDropdown.currentField;
        let orderBySecondary = secondarySortingDropdown.currentField;
        if (multiLineList && multiLineList.metric) {
            this.cacheOrderBy(multiLineList.metric, {
                order_by_primary: orderByPrimary,
                order_by_primary_direction: primarySortingDropdown.currentDirection,
                order_by_secondary: orderBySecondary || '',
                order_by_secondary_direction: secondarySortingDropdown.currentDirection || 'asc',
            });
        }
        let orderBy = orderByPrimary + ':' + primarySortingDropdown.currentDirection;
        if (orderBySecondary) {
            orderBy += ',' + orderBySecondary + ':' + secondarySortingDropdown.currentDirection;
        }
        let ctxCollection = this.context.get('collection');
        let params = ctxCollection.getOption('params') || {};
        params.order_by = orderBy;
        ctxCollection.setOption('params', params);
        this.context.resetLoadFlag({recursive: false});
        this.context.set('skipFetch', false);
        this.context.loadData();
    },

    /**
     * Setup sorting dropdowns.
     * @private
     */
    setSortingDropdownData: function() {
        const primarySortingDropdown = this._getPrimarySortComponent();
        const secondarySortingDropdown = this._getSecondarySortComponent();
        const multiLineList = this._getMultiLineList();

        if (multiLineList && multiLineList.metric) {
            const sortFields = this._getSortFields(multiLineList.module, multiLineList.metaFields);
            let primaryOrderByDefault = multiLineList.metric.order_by_primary.trim() || '';
            primarySortingDropdown.setDefaultField(primaryOrderByDefault);
            let primaryOrderByDirectionDefault = multiLineList.metric.order_by_primary_direction ?
                multiLineList.metric.order_by_primary_direction : 'asc';
            let secondaryOrderByDefault = multiLineList.metric.order_by_secondary.trim() || '';
            secondarySortingDropdown.setDefaultField(secondaryOrderByDefault);
            let secondaryOrderByDirectionDefault = multiLineList.metric.order_by_secondary_direction ?
                multiLineList.metric.order_by_secondary_direction : 'asc';
            const cachedOrderBy = this.getCachedOrderBy(multiLineList.metric);
            primaryOrderBy = cachedOrderBy ? cachedOrderBy.order_by_primary : primaryOrderByDefault;
            primaryOrderByDirection = cachedOrderBy ? cachedOrderBy.order_by_primary_direction :
                primaryOrderByDirectionDefault;
            secondaryOrderBy = cachedOrderBy ? cachedOrderBy.order_by_secondary :
                secondaryOrderByDefault;
            secondaryOrderByDirection = cachedOrderBy ? cachedOrderBy.order_by_secondary_direction :
                secondaryOrderByDirectionDefault;
            primarySortingDropdown.setDropdownFields(sortFields);
            primarySortingDropdown.setState(primaryOrderBy, primaryOrderByDirection);
            secondarySortingDropdown.setDropdownFields(sortFields);
            secondarySortingDropdown.setState(secondaryOrderBy, secondaryOrderByDirection);
            this.render();
        }
    },

    /**
     * Clear Primary and Secondary sorting dropdowns
     *
     * @private
     */
    _clearSortingDropdown: function() {
        const clearField = (field) => {
            field.setDefaultField('');
            field.setDropdownFields([]);
            field.setState('', 'asc');
        };

        const primarySortingDropdown = this._getPrimarySortComponent();
        const secondarySortingDropdown = this._getSecondarySortComponent();

        clearField(primarySortingDropdown);
        clearField(secondarySortingDropdown);
        this.render();
    },

    /**
     * Get the primary sort component.
     * @return {View.View}
     * @private
     */
    _getPrimarySortComponent: function() {
        return this._components[0];
    },

    /**
     * Get the secondary sort component.
     * @return {View.View}
     * @private
     */
    _getSecondarySortComponent: function() {
        return this._components[1];
    },

    /**
     * Get the multi-line list component.
     * @return {View.View}
     * @private
     */
    _getMultiLineList: function() {
        return this.layout && this.layout.layout &&
            this.layout.layout.getComponent('multi-line-list') || null;
    },

    /**
     * Get sort fields for a multi-line list view.
     * @param {string} module
     * @param {Object} metaFields
     * @return {Array}
     * @private
     */
    _getSortFields: function(module, metaFields) {
        let sortFields = [];
        let multiLineFields = this._getMultiLineFields(module, metaFields);
        const nonSortableTypes = ['id', 'relate', 'widget', 'assigned_user_name'];
        _.each(multiLineFields, function(field) {
            if (_.isObject(field) && app.acl.hasAccess('read', module, null, field.name)) {
                // Set the sort field information if the field is sortable
                const label = app.lang.get(field.label || field.vname, module);
                const isSortable = !_.isEmpty(label) && field.sortable !== false &&
                    field.sortable !== 'false' && nonSortableTypes.indexOf(field.type) === -1;
                if (isSortable) {
                    sortFields.push({name: field.name, label: label});
                }
            }
        });
        return sortFields;
    },

    /**
     * Get a unique list of the underlying fields contained in a multi-line list
     * @param module
     * @return {Array} a list of field definitions from the multi-line list metadata
     * @private
     */
    _getMultiLineFields: function(module, metaFields) {
        const moduleFields = app.metadata.getModule(module, 'fields');
        let subfields = [];
        let relatedFields = [];
        _.each(metaFields, function(fieldDefs) {
            subfields = subfields.concat(fieldDefs.subfields);
            _.each(fieldDefs.subfields, function(subfield) {
                if (subfield.related_fields) {
                    let related = _.map(subfield.related_fields, function(relatedField) {
                        return moduleFields[relatedField];
                    });
                    relatedFields = relatedFields.concat(related);
                }
            });
        }, this);

        // To filter out special fields as they should not be available for sorting or filtering
        subfields = _.filter(subfields, function(field) {
            return _.isEmpty(field.widget_name);
        });

        // Return the combined list of subfields and related fields. Ensure that
        // the correct field type is associated with the field (important for
        // filtering)
        var fields = _.compact(_.uniq(subfields.concat(relatedFields), false, function(field) {
            return field.name;
        }));
        return _.map(fields, function(field) {
            if (moduleFields[field.name]) {
                field.type = moduleFields[field.name].type;
            }
            return field;
        });
    },

    /**
     * Get cached order by values for a metric tab.
     * @param {Object} metric
     */
    getCachedOrderBy: function(metric) {
        if (metric && metric.id) {
            return app.user.lastState.get(app.user.lastState.key('sorting:' + metric.id, this.layout));
        }
        return null;
    },

    /**
     * Cache order by values for a metric tab.
     * @param {Object} metric
     * @param {Object|null}
     */
    cacheOrderBy: function(metric, orderBy) {
        if (metric && metric.id) {
            return app.user.lastState.set(app.user.lastState.key('sorting:' + metric.id, this.layout), orderBy);
        }
        return null;
    }
})
