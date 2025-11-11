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
 * Dashboard filter group widget
 *
 * @class View.Views.Base.HomeDashboardFilterGroupWidget
 * @alias SUGAR.App.view.views.BaseHomeDashboardFilterGroupWidget
 * @extends View.View
 */
({
    events: {
        'change [data-action="new-group-name"]': 'onGroupNameChanged',
        'click [data-action="remove-group"]': 'onRemoveGroup',
        'click [data-action="collapse-group"]': 'onCollapseGroup',
        'click .filter-widget-headerpane': 'onCollapseGroup',
        'click [data-action="remove-field"]': 'onRemoveField',
        'click [data-action="dashlet-group-widget"]': 'onGroupClick',
        'mouseenter .dashlet-group-filter-widget': 'mouseOverGroup',
        'mouseleave .dashlet-group-filter-widget': 'mouseOutGroup',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Property initialization, nothing to do for this view
     *
     */
    _initProperties: function() {
        const options = this.options;

        this._groupId = options.groupId;
        this._groupMeta = options.groupMeta || {};
        this._widgetNo = options.widgetNo;
        this._editMode = options.editMode || false;

        this._groupFields = this._groupMeta.fields || [];
        this._filterDef = this._groupMeta.filterDef || [];
        this._groupType = this._groupMeta.fieldType || '';
        this._groupLabel = this._groupMeta.label || `Filter ${this._widgetNo}`;
        this._isEmptyGroup = _.isEmpty(this._groupFields);

        const metadata = this.model.get('metadata');

        this._users = metadata.users || [];
        this._operators = metadata.runtimeFilterOperators || [];
        this._groupOperators = this._operators[this._groupType] || [];

        this._filterOperatorWidget = false;

        this._currentUserRestrictedGroup = !!this._groupMeta.currentUserRestrictedGroup;

        this._checkACLRestrictions();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'filter-groups-updated', this.groupsUpdated, this);
        this.listenTo(this.context, 'filter-operator-data-changed', this.updateItemsCount, this);
        this.listenTo(this.context, 'dashboard-filter-group-selected', this.newGroupSelected, this);
    },

    /**
     * Check ACL restrictions
     */
    _checkACLRestrictions: function() {
        if (this._currentUserRestrictedGroup) {
            this._disabledACL = true;
            return;
        }

        const metadata = this.model.get('metadata');
        const restrictedGroups =  metadata && _.has(metadata, 'currentUserRestrictedDashlets') ?
            metadata.currentUserRestrictedDashlets : [];

        const fieldNotAllowed = _.find(this._groupFields, (field) => {
            if (_.isArray(restrictedGroups) && restrictedGroups.includes(field.dashletId)) {
                return true;
            }

            const hasListAccess = app.acl.hasAccess('list', field.targetModule);

            if (!hasListAccess) {
                return true;
            }

            if (field.isRelated && !_.isEmpty(field.fieldDef.ext2)) {
                return !(
                    app.acl.hasAccess('view', field.fieldDef.ext2) &&
                    app.acl.hasAccess('list', field.fieldDef.ext2) &&
                    app.acl.hasAccess('read', field.fieldDef.ext2, {field: field.fieldName})
                );
            }  else if (field.isRelated && _.has(field, 'tableKey')) {
                const checkRelateAccess = function(relatedFieldsMeta) {
                    let access = true;

                    if (relatedFieldsMeta) {
                        const module = relatedFieldsMeta.module;
                        const relatedField = relatedFieldsMeta.fieldName;

                        if (module && relatedField) {
                            const fieldAccess = (
                                app.acl.hasAccess('view', module) &&
                                app.acl.hasAccess('list', module) &&
                                app.acl.hasAccess('read', module, {field: relatedField})
                            );

                            access = fieldAccess;
                        }
                    }

                    return access;
                };

                const directlyRelatedFieldsMeta = this._getRelatedFieldMeta(field);
                const relatedFieldsMeta = this._getRelatedFieldMetaRel(field);

                if (!directlyRelatedFieldsMeta && !relatedFieldsMeta) {
                    return false;
                }

                let fieldhasAccess = true;

                fieldhasAccess = checkRelateAccess(directlyRelatedFieldsMeta);

                if (fieldhasAccess) {
                    fieldhasAccess = checkRelateAccess(relatedFieldsMeta);
                }

                return !fieldhasAccess;
            } else {
                return !(
                    app.acl.hasAccess('view', field.targetModule) &&
                    app.acl.hasAccess('list', field.targetModule) &&
                    app.acl.hasAccess('read', field.targetModule, {field: field.fieldName})
                );
            }
        });

        if (!_.isEmpty(fieldNotAllowed)) {
            this._disabledACL = true;
        }
    },

    /**
     * Get the full table list for a specific field
     *
     * @param {Object} field - metadata of the field
     */
    _getFullTableListForField: function(field) {
        const meta = this.model.get('metadata');

        if (!meta) {
            return false;
        }

        if (!_.has(meta, 'dashlets')) {
            return false;
        }

        const dashlets = meta.dashlets;

        if (!dashlets) {
            return false;
        }

        const fieldDashlet = _.find(dashlets, item => item.id === field.dashletId);

        if (!fieldDashlet || !_.has(fieldDashlet, 'view') || !_.has(fieldDashlet.view, 'fullTableList')) {
            return false;
        }

        const fullTableList = fieldDashlet.view.fullTableList;

        if (!fullTableList) {
            return false;
        }

        return fullTableList;
    },

    /**
     * Metadata for the related fields without an Ext2 table
     *
     * @param {Object} field - metadata of the field
     */
    _getRelatedFieldMetaRel: function(field) {
        if (!_.isString(field.tableKey)) {
            return false;
        }

        const linkPath = field.tableKey.split(':');

        if (linkPath.length < 2) {
            return false;
        }

        const fullTableList = this._getFullTableListForField(field);

        if (!fullTableList) {
            return false;
        }

        const linkDef = fullTableList[field.tableKey];

        if (!linkDef) {
            return false;
        }

        if (!_.has(linkDef, 'module')) {
            return false;
        }

        const linkDefKey = 'link_def';
        const relationshipNameKey = 'relationship_name';
        const beanSideKey = 'bean_is_lhs';
        const rhsModuleKey = 'rhs_module';
        const lhsModuleKey = 'lhs_module';
        const lhsFieldKey = 'lhs_key';
        const rhsFieldKey = 'rhs_key';
        const linkNameKey = 'name';

        if (!_.has(linkDef, 'module')) {
            return false;
        }

        if (!_.has(linkDef, linkDefKey)) {
            return false;
        }

        if (!_.has(linkDef[linkDefKey],[relationshipNameKey])) {
            return false;
        }

        const linkMeta = linkDef[linkDefKey];

        if (!_.has(linkMeta, beanSideKey) || !_.has(linkMeta, linkNameKey)) {
            return false;
        }

        const relationshipName = linkMeta[relationshipNameKey];

        if (!relationshipName) {
            return false;
        }

        const relMeta = app.metadata.getRelationship(relationshipName);

        if (!relMeta || !_.has(relMeta, rhsModuleKey) || !relMeta[rhsModuleKey]) {
            return false;
        }

        const isInLhs = linkMeta[beanSideKey];

        let module = '';
        let fieldName = '';

        if (isInLhs && _.has(relMeta, lhsModuleKey)) {
            module = relMeta[lhsModuleKey];
            fieldName = relMeta[lhsFieldKey];
        } else if (_.has(relMeta, rhsModuleKey)) {
            module = relMeta[rhsModuleKey];
            fieldName = relMeta[rhsFieldKey];
        }

        if (module && fieldName) {
            return {
                'module': module,
                'fieldName': fieldName,
            };
        }

        return false;
    },

    /**
     * Metadata for the related fields without an Ext2 table
     *
     * @param {Object} field - metadata of the field
     */
    _getRelatedFieldMeta: function(field) {
        if (!_.isString(field.tableKey)) {
            return false;
        }

        const linkPath = field.tableKey.split(':');

        if (linkPath.length < 2) {
            return false;
        }

        const fullTableList = this._getFullTableListForField(field);

        if (!fullTableList) {
            return false;
        }

        const linkDef = fullTableList[field.tableKey];

        if (!linkDef) {
            return false;
        }

        if (!_.has(linkDef, 'module')) {
            return false;
        }

        const targetModule = linkDef.module;

        return {
            'module': targetModule,
            'fieldName': field.fieldName,
        };
    },

    /**
     * Update items count
     */
    updateItemsCount: function() {
        if (!this._filterOperatorWidget) {
            return;
        }

        const itemsCount = this._filterOperatorWidget.getItemsCount();

        this.$('.items-selected-nr').html(itemsCount);
    },

    /**
     * Listen when a group is updated
     *
     * @param {Object} filterGroups
     * @param {string} activeFilterGroupId
     * @param {boolean} isGroupClicked
     */
    groupsUpdated: function(filterGroups, activeFilterGroupId, isGroupClicked) {
        this._isSelected = this._groupId === activeFilterGroupId;

        if (_.isUndefined(filterGroups[activeFilterGroupId])) {
            return;
        }

        const {
            fieldType,
            fields,
            filterDef,
        } = filterGroups[this._groupId];

        this._groupType = fieldType;
        this._groupFields = fields;
        this._filterDef = filterDef;
        this._groupOperators = this._operators[fieldType] || [];
        this._isEmptyGroup = _.isEmpty(this._groupFields);

        const groupClicked = isGroupClicked ? isGroupClicked : false;

        if (this._isSelected && !groupClicked) {
            this.render();
        }

        this._toggleGroupActive(this._isSelected);

        if (this._isSelected && this._isEmptyGroup && this._editMode) {
            this._highlightInput();
        }

        if (!groupClicked) {
            this._updateFilterOperatorWidget();
        }
    },

    /**
     * Handle text changed
     *
     * @param {UIEvent} e
     */
    onGroupNameChanged: function(e) {
        this._groupLabel = e.target.value;
        this.context.trigger('dashboard-filter-group-name-changed', this._groupLabel, this._groupId);
        this.context.trigger('dashboard-filters-interaction');
    },

    /**
     * On group hover in
     *
     * @param {UIEvent} e
     */
    mouseOverGroup: function(e) {
        if (this._editMode) {
            return;
        }

        const highlight = true;

        this._groupHighlight(highlight, e);
    },

    /**
     * On group hover in
     *
     * @param {UIEvent} e
     */
    mouseOutGroup: function(e) {
        if (this._editMode) {
            return;
        }

        const highlight = false;

        this._groupHighlight(highlight, e);
    },

    /**
     *  Highlight current group and notify it's dashlets
     *
     * @param {boolean} highlight
     * @param {UIEvent} element
     */
    _groupHighlight: function(highlight, element) {
        this.$(element.currentTarget).toggleClass('hover-highlight', highlight);

        if (!this._groupMeta || !_.has(this._groupMeta, 'fields') || this._groupMeta.fields === 0) {
            return;
        }

        this._notifyGroupHighlight(highlight);
    },

    /**
     * Notify to show/hide the highlight
     *
     * @param {boolean} highlight
     */
    _notifyGroupHighlight: function(highlight) {
        const dashletIds = _.pluck(this._groupMeta.fields, 'dashletId');

        this.context.trigger('dashboard-filter-group-highlight', dashletIds, highlight);
    },

    /**
     * On group click
     *
     * @param {UIEvent} e
     */
    onGroupClick: function(e) {
        if (this._disabledACL) {
            return;
        }

        const $el = this.$('.dashlet-collapse-group');
        const collapsed = $el.is('.sicon-chevron-down');

        if (collapsed) {
            this.onCollapseGroup(e);
        }

        this.context.trigger('dashboard-filter-group-selected', this._groupId);
    },

    /**
     * A new group was selected
     *
     * @param {string} selectedGroupId
     */
    newGroupSelected: function(selectedGroupId) {
        this._isSelected = this._groupId === selectedGroupId;

        this._toggleGroupActive(this._isSelected);
    },

    isValid: function() {
        return this._filterOperatorWidget && this._filterOperatorWidget.isValid();
    },

    /**
     * Mark the group as invalid
     *
     * @param {boolean} show
     */
    toggleGroupInvalid: function(show) {
        this.$('.dashlet-group-filter-widget').toggleClass('dashlet-group-invalid', show);
    },

    /**
     * Handle click on the remove group
     *
     * @param {UIEvent} e
     */
    onCollapseGroup: function(e) {
        if (e.target && e.target.dataset && e.target.dataset.action === 'new-group-name') {
            return;
        }

        const $el = this.$('.dashlet-collapse-group');
        const collapsed = $el.is('.sicon-chevron-up');

        $el.toggleClass('sicon-chevron-down', collapsed);
        $el.toggleClass('sicon-chevron-up', !collapsed);

        this.$('.empty-group-message').toggleClass('hidden', collapsed);
        this.$('[data-container="fields-container"]').toggleClass('hidden', collapsed);
        this.$('[data-container="operators-container"]').toggleClass('hidden', collapsed);
        this.$('[data-container="collapsed-filter-group"]').toggleClass('hidden', !collapsed);
        this.$('[data-container="expanded-filter-group"]').toggleClass('hidden', collapsed);
        this.$('.dashlet-group-filter-widget').toggleClass('dashlet-group-filter-widget-collapsed', collapsed);
        this.$('.dashboard-group-edit-label').toggleClass('dashboard-group-edit-name', !collapsed);

        if (e) {
            e.stopPropagation();
        }
    },

    /**
     * Handle click on the remove group
     *
     * @param {UIEvent} e
     */
    onRemoveGroup: function(e) {
        this.context.trigger('dashboard-filter-group-removed', this._groupId);

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    },

    /**
     * Handle click on the field to be removed
     *
     * @param {UIEvent} e
     */
    onRemoveField: function(e) {
        const fieldName = e.currentTarget.getAttribute('data-field-name');
        const dashletId = e.currentTarget.getAttribute('data-dashlet-id');

        const fieldMeta = {
            dashletId,
            field: {
                name: fieldName,
            },
            highlighted: false,
        };

        this.context.trigger('dashboard-filter-widget-clicked', fieldMeta);

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    },

    /**
     * Select and highlight the input text
     */
    _highlightInput: function() {
        const inputEl = this.$('[data-action="new-group-name"]');

        inputEl.focus();
        inputEl.select();
    },

    /**
     * Update filter operator widget
     */
    _updateFilterOperatorWidget: function() {
        this._deleteFilterOperatorWidget();
        this._createFilterOperatorWidget();
    },

    /**
     * Delete filter operator widget
     */
    _deleteFilterOperatorWidget: function() {
        if (this._filterOperatorWidget) {
            this._filterOperatorWidget.dispose();
            this._filterOperatorWidget = false;
        }
    },

    /**
     * Create filter operator widget
     */
    _createFilterOperatorWidget: function() {
        const field = _.first(this._groupFields);

        if (_.isUndefined(field)) {
            return;
        }

        this._filterOperatorWidget = app.view.createView({
            type: 'filter-operator-widget',
            context: this.context,
            operators: this._groupOperators,
            users: this._users,
            filterData: this._filterDef,
            fieldType: this._groupType,
            filterId: this._groupId,
            seedFieldDef: field.fieldDef,
            seedModule: field.targetModule,
            tooltipTitle: this._groupLabel,
            manager: this,
        });

        this._filterOperatorWidget.render();

        this.$('[data-container="operators-container"]').append(this._filterOperatorWidget.$el);
        this.$('.runtime-filter-summary-text').html(this._filterOperatorWidget.getSummaryText());
        this.$('[data-tooltip="filter-summary"]').tooltip({
            delay: 200,
            container: 'body',
            placement: 'bottom',
            title: _.bind(this._filterOperatorWidget.getTooltipText, this._filterOperatorWidget),
            html: true,
            trigger: 'hover',
        });

        this.updateItemsCount();
    },

    /**
     * Handle the group style if is active/inactive
     *
     * @param {boolean} isActive
     */
    _toggleGroupActive: function(isActive) {
        isActive = isActive && this._editMode;

        this._toogleGroupSelected(isActive);
    },

    /**
     * Handle selected group
     *
     * @param {boolean} selected
     */
    _toogleGroupSelected: function(selected) {
        this.$('.dashlet-group-filter-widget').toggleClass('dashlet-group-active', selected);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._deleteFilterOperatorWidget();

        this._super('_dispose');
    },
});
