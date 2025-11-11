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
 * @class View.Views.Base.DashletFilterWidgetView
 * @alias SUGAR.App.view.views.BaseDashletFilterWidgetView
 * @extends View.View
 */
({
    events: {
        'click [data-action="dashlet-filter-widget"]': 'filterWidgetClicked',
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
     * Init properties
     *
     * @param {Object} options
     */
    _initProperties: function(options) {
        this._highlighted = false;
        this._checked = false;
        this._available = true;

        this._filterData = options.filterData;
        this._fullTableList = options.fullTableList;
        this._dashletSpecificData = options.dashletSpecificData || {};

        this._targetModule = this._getTargetModule();
        this._targetField = this._getTargetField();
        this._isRelate = this._targetField ? this._isRelateField(this._targetField.type) : false;
        this._targetFieldModule = this._getTargetModuleLabel();
        this._hasAccess = true;
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'dashboard-filter-widget-clicked', this.manageWidgetState, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._checkACLRestrictions();

        this._super('_render');

        if (!this._hasAccess) {
            this._disableWidget();
        }

        this.$('[data-tooltip="filter-summary"]').tooltip({
            delay: 100,
            container: 'body',
            placement: 'bottom',
            title: _.bind(this._getTooltipText, this),
            html: true,
            trigger: 'hover',
        });
    },

    /**
     * Metadata for the related fields without an Ext2 table
     *
     * @param {string} tableKey
     */
    _getRelatedFieldMetaRel: function(tableKey) {
        if (!_.isString(tableKey)) {
            return false;
        }

        const linkPath = tableKey.split(':');

        if (linkPath.length < 2) {
            return false;
        }

        const linkDef = this._fullTableList[tableKey];

        if (!linkDef) {
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
     * Check ACL restrictions
     */
    _checkACLRestrictions: function() {
        const fieldDef = this._targetField;
        const isRelate = this._isRelate;
        const tableKeyName = 'table_key';
        const tableKey = this._filterData ? this._filterData[tableKeyName] : false;

        this._hasAccess = app.acl.hasAccess('list', this._targetModule);

        if (!this._hasAccess) {
            return;
        }

        if (isRelate && !_.isEmpty(fieldDef.ext2)) {
            this._hasAccess = (
                app.acl.hasAccess('view', fieldDef.ext2) &&
                app.acl.hasAccess('list', fieldDef.ext2) &&
                app.acl.hasAccess('read', fieldDef.ext2, {field: fieldDef.name})
            );

            return;
        }  else if (isRelate && tableKey) {
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

            if (!_.isString(tableKey)) {
                this._hasAccess = true;
                return;
            }

            const directlyRelatedFieldsMeta = this._getRelatedFieldMetaModule(tableKey, fieldDef);
            const relatedFieldsMeta = this._getRelatedFieldMetaRel(tableKey);

            if (!directlyRelatedFieldsMeta && !relatedFieldsMeta) {
                this._hasAccess = true;
                return;
            }

            let fieldHasAccess = true;

            fieldHasAccess = checkRelateAccess(directlyRelatedFieldsMeta);

            if (fieldHasAccess) {
                fieldHasAccess = checkRelateAccess(relatedFieldsMeta);
            }

            this._hasAccess = fieldHasAccess;
        } else {
            this._hasAccess = (
                app.acl.hasAccess('view', this._targetModule) &&
                app.acl.hasAccess('list', this._targetModule) &&
                app.acl.hasAccess('read', this._targetModule, {field: fieldDef.name})
            );
        }
    },

    /**
     * Metadata for the related fields without an Ext2 table
     *
     * @param {string} tableKey
     * @param {Object} field
     */
    _getRelatedFieldMetaModule: function(tableKey, field) {
        if (!_.isString(tableKey)) {
            return false;
        }

        const linkPath = tableKey.split(':');

        if (linkPath.length < 2) {
            return false;
        }

        const linkDef = this._fullTableList[tableKey];

        if (!linkDef) {
            return false;
        }

        if (!_.has(linkDef, 'module')) {
            return false;
        }

        const targetModule = linkDef.module;

        return {
            'module': targetModule,
            'fieldName': field.name,
        }
    },

    /**
     * Return filter data
     *
     * @return {Array}
     */
    getFilterData: function() {
        return this._filterData;
    },

    /**
     * Enable or disable active elements
     *
     * @param {Object} data
     */
    manageWidgetState: function(data) {
        if (this._checked || this._highlighted || !this._targetField) {
            return;
        }

        if (!this._hasAccess) {
            this._disableWidget();

            return;
        }

        const fieldType = data.field.type;
        const invalidField = this._targetField.type !== fieldType;

        if (invalidField) {
            this.toggleDisabled(true);
        } else {
            this.toggleAvailable(true);
        }

        // handle special date cases
        const dateTypes = ['datetime', 'date'];

        if (_.includes(dateTypes, this._targetField.type) && _.includes(dateTypes, fieldType)) {
            this.toggleAvailable(true);
        }

        // handle relate cases (relate, name etc.)
        if (this._isRelateField(this._targetField.type) && this._isRelateField(fieldType)) {
            const selectedFieldModule = data.field.module || data.targetModule;

            const interogatedFieldModule = this._targetField.module || this._targetModule;

            if (selectedFieldModule === interogatedFieldModule) {
                this.toggleAvailable(true);
            } else {
                this.toggleDisabled(true);
            }
        }

        // handle enum and multi enum cases
        // only activate those enums that share the same options or same function bean and function
        const enumTypes = ['enum', 'multienum'];
        const fnBean = 'function_bean';

        if (_.includes(enumTypes, this._targetField.type) &&
            _.includes(enumTypes, fieldType)) {
            if ((this._targetField.options && this._targetField.options === data.field.options) ||
                (this._targetField.function && this._targetField.function === data.field.function &&
                this._targetField[fnBean] && this._targetField[fnBean] === data.field[fnBean])) {
                this.toggleAvailable(true);
            } else {
                this.toggleDisabled(true);
            }
        }
    },

    /**
     * Notify listeners that a field has been clicked
     */
    filterWidgetClicked: function() {
        this.context.trigger('dashboard-filter-widget-clicked', {
            dashletId: this.layout.options.dashletMetaId,
            field: this._targetField,
            filter: this._filterData,
            highlighted: !this._highlighted,
            targetModule: this._targetModule,
            dashletLabel: this.model.get('label'),
            dashletSpecificData: this._dashletSpecificData,
            isRelated: this._isRelate,
            targetFieldModule: this._targetFieldModule,
        });
    },

    /**
     * Add/Remove the highlight feedback
     *
     * @param {boolean} highlight
     */
    toggleHighlight: function(highlight) {
        this._highlighted = highlight;
        this.$('.reports-runtime-widget').toggleClass('dashboard-filter-field-selected', this._highlighted);

        if (this._highlighted) {
            this.toggleDisabled(false);
            this.toggleChecked(false);
            this.toggleAvailable(false);
        }
    },

    /**
     * Add/Remove the checked feedback
     *
     * @param {boolean} check
     */
    toggleChecked: function(check) {
        this._checked = check;

        this.$('.reports-runtime-widget').toggleClass('dashboard-filter-field-checked', this._checked);
        this.$('[data-container="dashboard-filter-checked-icon"]').toggleClass('hide', !this._checked);

        if (this._checked) {
            this.toggleDisabled(false);
            this.toggleHighlight(false);
            this.toggleAvailable(false);
        }
    },

    /**
     * Add/Remove the available feedback
     *
     * @param {boolean} available
     */
    toggleAvailable: function(available) {
        this._available = available;

        this.$('.reports-runtime-widget').toggleClass('dashboard-filter-field-available', available);

        if (this._available) {
            this.toggleDisabled(false);
            this.toggleHighlight(false);
            this.toggleChecked(false);
        }
    },

    /**
     * Add/Remove the disabled feedback
     *
     * @param {boolean} disable
     */
    toggleDisabled: function(disable) {
        const target = this.$('[data-action="dashlet-filter-widget"]');

        this.$('.reports-runtime-widget').toggleClass('dashboard-filter-field-disabled', disable);
    },

    /**
     * Returns the filter's target module
     *
     * @return {Mixed}
     */
    _getTargetModule: function() {
        const tableKeyName = 'table_key';

        const tableKey = this._filterData ? this._filterData[tableKeyName] : false;
        const tables = this._fullTableList ? this._fullTableList : false;
        const targetModule = tables ? tables[tableKey].module : false;

        return targetModule;
    },

    /**
     * Disable widget
     *
     * @private
     */
    _disableWidget: function() {
        this.toggleAvailable(false);
        this.toggleHighlight(false);
        this.toggleChecked(false);
        this.toggleDisabled(true);
    },

    /**
     * Returns the filter's target field
     *
     * @return {Mixed}
     */
    _getTargetField: function() {
        if (!this._targetModule) {
            return false;
        }

        return app.utils.deepCopy(
            app.metadata.getField({
                module: this._targetModule,
                name: this._filterData.name,
            })
        );
    },

    /**
     * Returns the filter's target field
     *
     * @param {string} fieldType
     *
     * @return {boolean}
     */
    _isRelateField: function(fieldType) {
        const relateTypes = ['username', 'name', 'relate'];

        return _.includes(relateTypes, fieldType);
    },

    /**
     * Returns the filter's target module label
     *
     * @return {Mixed}
     */
    _getTargetModuleLabel: function() {
        const tableKey = this._filterData ? this._filterData.table_key : '';
        const tables = this._fullTableList || {};

        if (!tables[tableKey]) {
            return '';
        }

        const targetModule = tables ? app.lang.getModuleName(tables[tableKey].label, {
            plural: true,
        }) : false;

        return targetModule;
    },

    /**
     * Build tooltip description text
     *
     * @return {string}
     */
    _getTooltipText: function() {
        const tables = this._fullTableList || {};
        const targetTable = tables[this._filterData.table_key];

        if (!targetTable) {
            return '';
        }

        const tableHierarchy = targetTable.name ? targetTable.name : app.lang.getModuleName(targetTable.value, {
            plural: true,
        });
        const fieldLabel = app.lang.get(this._targetField.vname, this._targetModule);

        // tooltip text has to be html so we can meet the UX mocks
        const title = '<div class="runtime-filter-summary-tooltip">' +
                    tableHierarchy.replace(/\s\s+/g, ' ') +
                    ' ><b> ' + fieldLabel +
                    '</b><br></div>';

        return title;
    },

    /**
     * Returns the filter's target module label
     *
     * @return {Mixed}
     */
    _getHierarchyModuleLabel: function() {
        const tables = this._fullTableList || {};
        const filterData = this._filterData || {};
        const targetTable = tables[filterData.table_key];

        if (_.has('name', targetTable)) {
            const tableHierarchy = targetTable.name ? targetTable.name : app.lang.getModuleName(targetTable.value, {
                plural: true,
            });

            return tableHierarchy.replace(/\s\s+/g, ' ');
        } else {
            return '';
        }
    },
});
