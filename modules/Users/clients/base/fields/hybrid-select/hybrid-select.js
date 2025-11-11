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
 * @class View.Fields.Base.Users.HybridSelectField
 * @alias SUGAR.App.view.fields.BaseUsersHybridSelectField
 * @extends View.Fields.Base.Field
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'change select': 'updateSelect',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.selectModule = options.def.select_module;

        this.context.on('change:assigned_user', this.changeAssignedFilter, this);

        /**
         * select items
         */
        this.items = [{id: '', text: '', element: new Option()}];

        /**
         * selected items
         */
        this.massCollection = app.data.createBeanCollection(this.selectModule);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render', arguments);
        this.setSelect(this.items);
    },

    /**
     * setup the select as select2
     */
    setSelect: function() {
        const select = this.$('.hybrid select');
        const placeholder = this.options.def.placeholder;

        select.select2({
            allowClear: true,
            containerCssClass: 'select2-choices-pills-close',
            placeholder: app.lang.getModString(placeholder, this.module),
        });

        // set empty options
        this.$('select').val(null).trigger('change');
        //add options
        for (const item of this.items) {
            this.$('select').append(item.element);
        }
        this.$('select').trigger('change');

        select.on('select2-opening', _.bind(this.openDrawer, this));
    },

    /**
     * Change event for assigned filter
     *
     * @param {Event} evt
     */
    changeAssignedFilter: function(evt) {
        let filterPopulate = {
            assigned_user_id: this.context.get('assigned_user'),
        };
        let initialFilterLabel = 'LBL_FILTER_UTILS_SELECT';

        // if the module is Filters, we should filter by 'created by'
        if (this.selectModule === 'Filters') {
            filterPopulate = {
                created_by: this.context.get('assigned_user'),
            };
            initialFilterLabel = 'LBL_FILTER_UTILS_CREATED';
        }

        this.filterOptions = new app.utils.FilterOptions()
        .config({
            initial_filter: 'utils-select',
            initial_filter_label: initialFilterLabel,
            filter_populate: filterPopulate,
        }).format();
    },

    /**
     * Open multi select drawer
     *
     * @param {Event} evt
     */
    openDrawer: function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        /**
         * Remove focus from input because we don't want accidental typing events triggered
         */
        this.$('.select2-search input, :focus,input').prop('focus', false).blur();

        if (this.context.get('assigned_user') && _.isUndefined(this.filterOptions)) {
            this.changeAssignedFilter();
        }

        let context = {
            module: this.selectModule,
            isMultiSelect: true,
            mass_collection: this.massCollection,
        };

        if (this.filterOptions) {
            _.extend(context, {filterOptions: this.filterOptions});
        }

        // open the selection drawer
        app.drawer.open({
            context: context,
            layout: 'multi-selection-list',
        }, _.bind(function(data) {
            // give focus back to the input
            this.$('.select2-search input, :focus,input').prop('focus', true).blur();

            if (_.isArray(data) && !_.isEmpty(data)) {
                const hasName = _.first(data).name;

                if (hasName) {
                    this.setFieldValue(data);
                } else {
                    // make sure we have sth to display into the field
                    // so we need to retrieve records names
                    this.enhanceData(data, this.setFieldValue);
                }
            }
        }, this));
    },

    /**
     * Call api in order to retrieve the name field for each selected record
     *
     * @param {Array} data
     * @param {Function} callback
     */
    enhanceData: function(data, callback) {
        app.alert.show('utils-loading', {
            level: 'process',
            title: app.lang.getModString('LBL_LOADING_ITEMS', this.module),
        });

        let fields = ['id', 'name',];

        if (this.selectModule === 'Dashboards') {
            fields.push('dashboard_module');
        }

        const ids = _.map(data, function mapFilter(item) {
            return item.id;
        });
        const filter = [{id: {$in: ids}}];

        var url = app.api.buildURL(this.selectModule, 'filter');

        app.api.call('create', url, {
            fields: fields,
            filter: filter,
            max_num: -1,
        }, {
            success: _.bind(callback, this),
            error: function(error) {
                app.alert.show('data-error', {
                    level: 'error',
                    messages: app.lange.getModString('LBL_DATA_NOT_RETRIEVED', this.module),
                });
            },
            complete: function() {
                app.alert.dismiss('utils-loading');
            }
        });
    },

    /**
     * Set data in order to be displayed in the field input
     *
     * @param {Array} data
     */
    setFieldValue: function(data) {
        data = data.records || data;

        // set the selected items
        this.massCollection = new app.data.createBeanCollection(this.selectModule);
        for (const item of data) {
            this.massCollection.push(new app.data.createBean(this.selectModule, item));
        }

        // this will be used at select2 init
        this.items = _.map(data, _.bind(function(item) {
            let itemName = item.name;
            if (this.massCollection.module === 'Dashboards') {
                itemName = app.lang.get(item.name, item.dashboard_module);
            }

            return {
                id: item.id,
                text: itemName,
                element: new Option(itemName, item.id, true, true)
            };
        }, this));
        this.render();
    },

    /**
     * whenever we change data on select, update the massCollection also
     *
     * @param {Event} evt
     */
    updateSelect: function(evt) {
        const removed = evt.removed;
        if (!removed) {
            return;
        }

        _.each(this.massCollection.models, _.bind(function massCollectionRemove(item) {
            if (item.id === removed.id) {
                this.massCollection.remove(item);
            }
        }, this));
    },

    /**
     * Retrieves the selected data and returns only their ids
     */
    getSelected: function() {
        return _.map(this.massCollection.models, function mapItems(item) {
            return item.get('id');
        });
    },
});
