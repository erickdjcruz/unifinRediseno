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
 * @class View.Layouts.Base.MetricsRecordLayout
 * @alias SUGAR.App.view.layouts.BaseMetricsRecordLayout
 * @extends View.Layouts.Base.ConfigDrawerLayout
 */
({
    extendsFrom: 'BaseConfigDrawerLayout',

    plugins: ['ErrorDecoration'],

    events: {
        'click a[name="cancel_button"]': 'editCancelled',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.createMode = false;

        if (this.context.get('action') === 'edit') {
            this.action = 'edit';
            // if the model is not fully loaded, then load model again
            // else set model
            if (_.isUndefined(this.model.get('metric_module'))) {
                this.loadData();
            } else {
                this.setModel(this.model);
            }
        } else {
            if (this.model.get('id')) {
                this.action = 'detail';
                this.loadData();
            } else {
                this.createMode = true;
                this.action = 'edit';
                this.setModel(this.model);
            }
        }

        this.context.set({create: this.createMode, action: this.action});
        this.context.on('edit:clicked', this.editClicked, this);
        this.context.on('edit:cancelled', this.editCancelled, this);
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        if (!this.model.get('id')) {
            return;
        }
        app.alert.show('fetching_metric', {
            level: 'process',
            title: app.lang.get('LBL_LOADING'),
            autoClose: false
        });
        this.model.fetch({
            success: _.bind(function() {
                if (this.disposed) {
                    return;
                }
                this.setModel(this.model);
                this.render();
            }, this),
            complete: function() {
                app.alert.dismiss('fetching_metric');
            }
        });
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!this.createMode && !this.model.dataFetched) {
            return;
        }
        return this._super('_render');
    },

    /**
     * @inheritdoc
     */
    setContextModel: function(options) {
        return;
    },

    /**
     * Checks Metrics ACLs to see if the User is a system admin
     * or if the user has a developer role for the Metrics module
     *
     * @inheritdoc
     */
    _checkModuleAccess: function() {
        var acls = app.user.getAcls().Metrics;
        var isSysAdmin = (app.user.get('type') == 'admin');
        var isDev = (!_.has(acls, 'developer'));

        return (isSysAdmin || isDev || this.action !== 'edit');
    },

    _checkConfigMetadata: function() {
        return true;
    },

    /**
     * Handles 'edit' button click
     */
    editClicked: function() {
        this.action = 'edit';
        this.context.set('action', this.action);
        this.render();
    },

    /**
     * Handles 'cancel' button click
     */
    editCancelled: function() {
        this.action = 'detail';
        this.context.set('action', this.action);
        this.model.revertAttributes({
            hideDbvWarning: true
        });
        this.render();
    },

    /**
     * @inheritdoc
     */
    _checkConfigMetadata: function() {
        return true;
    },

    /**
     * Takes a stored order_by value and splits it into field name and direction
     *
     * @param value
     * @return {Array} an array containing the order_by field and direction data
     * @private
     */
    _parseOrderByComponents: function(value) {
        if (_.isString(value)) {
            return value.split(':');
        }
        return [];
    },

    /**
     * Sets default values.
     * @param {Object} bean
     */
    setModelDefaults: function(bean) {
        var config = app.metadata.getView(bean.get('metric_module'), 'multi-line-list') || {};
        var defaults = config.defaults || {};
        var defaultAttributes = {};

        _.each(defaults, function(value, key) {
            if (key === 'order_by_primary' || key === 'order_by_secondary') {
                var orderByComponents = this._parseOrderByComponents(value);
                defaultAttributes[key] = orderByComponents[0] || '';
                defaultAttributes[key + '_direction'] = orderByComponents[1] || 'desc';
            } else {
                defaultAttributes[key] = value;
            }
        }, this);
        bean.set('defaults', defaultAttributes);
    },

    /**
     * Sets up the model
     */
    setModel: function(bean) {
        if (!this.checkAccess()) {
            this.blockModule();
            return;
        }

        this.setModelDefaults(bean);
        this.setTabContent(bean);
        this.setFilterableFields(bean);
        this.addValidationTasks(bean);

        bean.on('change:columns', function() {
            this.setTabContent(bean, true);
            this.setSortValues(bean);
        }, this);
    },

    /**
     * Sets the filterable fields
     * @param bean
     */
    setFilterableFields: function(bean) {
        var module = bean.get('metric_module');
        var filterableFields = app.data.getBeanClass('Filters').prototype.getFilterableFields(module);
        bean.set('filterableFields', filterableFields);
    },

    /**
     * Sets tab content for the module on the bean
     *
     * @param {Object} bean to model
     * @param {boolean} update Flag to show if it's the updating of bean
     */
    setTabContent: function(bean, update) {
        update = update || false;

        var tabContent = {};
        var module = bean.get('metric_module');
        var multiLineFields = update ?
            this.getColumns(bean) :
            this._getMultiLineFields(module, bean);

        // Set the information about the tab's fields, including which fields
        // can be used for sorting
        var fields = {};
        var sortFields = {};
        var nonSortableTypes = ['id', 'widget'];
        _.each(multiLineFields, function(field) {
            if (_.isObject(field) && app.acl.hasAccess('read', module, null, field.name)) {
                // Set the field information
                fields[field.name] = field;

                // Set the sort field information if the field is sortable
                var label = app.lang.get(field.label || field.vname, module);
                var isSortable = !_.isEmpty(label) && field.sortable !== false &&
                    field.sortable !== 'false' && nonSortableTypes.indexOf(field.type) === -1;
                if (isSortable) {
                    sortFields[field.name] = label;
                }
            }
        });
        tabContent.fields = fields;
        tabContent.sortFields = sortFields;

        bean.set('tabContent', tabContent);
        bean.trigger('change:tabContent');
    },

    /**
     * Sets values of the sortable fields
     *
     * @param {Object} bean
     */
    setSortValues: function(bean) {
        const sortValue1 = bean.get('order_by_primary');
        const sortValue2 = bean.get('order_by_secondary');
        const columns = this.getColumns(bean);

        if (sortValue2 && !columns[sortValue2]) {
            bean.set('order_by_secondary', '');
        }

        if (sortValue1 && !columns[sortValue1]) {
            if (sortValue2) {
                bean.set('order_by_primary', sortValue2);
                bean.set('order_by_secondary', '');
            } else {
                bean.set('order_by_primary', '');
            }
        }
    },

    /**
     * Return values of the sortable fields using selected columns and metadata
     *
     * @param {Object} bean
     * @return {Object} a list fields by selected columns
     */
    getColumns: function(bean) {
        const module = bean.get('metric_module');
        var columns = bean.get('columns');
        var moduleFields = app.metadata.getModule(module, 'fields');

        _.each(columns, function(field, key) {
            // add related_fields from widgets, they should be sortable
            if (!_.isEmpty(field.console) && !_.isEmpty(field.console.related_fields)) {
                var relatedFields = field.console.related_fields;
                _.each(relatedFields, function(field) {
                    if (_.isEmpty(columns[field]) && !_.isEmpty(moduleFields[field])) {
                        columns[field] = moduleFields[field];
                    }
                });
            }
        });

        return columns;
    },

    /**
     * Gets a unique list of the underlying fields contained in a multi-line list
     * @param module
     * @param {Object} bean
     * @return {Array} a list of field definitions from the multi-line list metadata
     * @private
     */
    _getMultiLineFields: function(module, bean) {
        // Get the unique lists of subfields and related_fields from the multi-line
        // list metadata of the module
        var beanViewDefs = bean.attributes.viewdefs;
        var multiLineMeta = beanViewDefs && beanViewDefs.base ?
            beanViewDefs.base.view['multi-line-list'] :
            app.metadata.getView(module, 'multi-line-list');
        var moduleFields = app.metadata.getModule(module, 'fields');
        var subfields = [];
        var relatedFields = [];
        _.each(multiLineMeta.panels, function(panel) {
            var panelFields = panel.fields;
            _.each(panelFields, function(fieldDefs) {
                subfields = subfields.concat(fieldDefs.subfields);
                _.each(fieldDefs.subfields, function(subfield) {
                    if (subfield.related_fields) {
                        var related = _.map(subfield.related_fields, function(relatedField) {
                            return moduleFields[relatedField];
                        });
                        relatedFields = relatedFields.concat(related);
                    }
                });
            }, this);
        }, this);

        // To filter out special fields as they should not be available for sorting or filtering.
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
     * Adds validation tasks to the fields in the layout for the enabled modules
     */
    addValidationTasks: function(bean) {
        if (bean !== undefined) {
            bean.addValidationTask('check_name', _.bind(this._validateName, bean));
            bean.addValidationTask('check_order_by_primary', _.bind(this._validatePrimaryOrderBy, bean));
        }
    },

    /**
     * Validates name
     *
     * @protected
     */
    _validateName: function(fields, errors, callback) {
        if (_.isEmpty(this.get('name'))) {
            errors.name = errors.name || {};
            errors.name.required = true;
        }

        callback(null, fields, errors);
    },

    /**
     * Validates table header values for the enabled module
     *
     * @protected
     */
    _validatePrimaryOrderBy: function(fields, errors, callback) {
        if (_.isEmpty(this.get('order_by_primary'))) {
            errors.order_by_primary = errors.order_by_primary || {};
            errors.order_by_primary.required = true;
        }

        callback(null, fields, errors);
    }
})
