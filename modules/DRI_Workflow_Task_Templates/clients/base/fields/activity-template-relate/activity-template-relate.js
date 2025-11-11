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
 * @class View.Fields.Base.DRIWorkflowTaskTemplates.ActivityTemplateRelateField
 * @alias SUGAR.App.view.fields.BaseDRIWorkflowTaskTemplatesActivityTemplateRelateField
 * @extends View.Fields.Base.RelateField
 */
({
    /**
     * ActivityTemplateRelate FieldTemplate (base)
     */
    extendsFrom: 'RelateField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'relate';
    },

    /**
     * @override
     */
    openSelectDrawer: function() {
        let layout = 'selection-list';
        let context = {
            module: this.getSearchModule(),
            fields: this.getSearchFields(),
            filterOptions: this.getFilterOptions(),
        };
        if (!!this.def.isMultiSelect) {
            layout = 'multi-selection-list';
            _.extend(context, {
                preselectedModelIds: _.clone(this.model.get(this.def.id_name)),
                maxSelectedRecords: this._maxSelectedRecords,
                isMultiSelect: true,
            });
        }
        context = _.extend({}, context, this.getCustomContext(context));
        app.drawer.open(
            {
                layout: layout,
                context: context,
            },
            _.bind(this.setValue, this)
        );
    },

    /**
     * Overriden this function to inject filters in inline-search collection
     *
     * @private
     */
    _createSearchCollection: function() {
        let searchModule = this.getSearchModule();
        if (searchModule && app.metadata.getModule(searchModule)) {
            this.searchCollection = this.customEndpointCollection(searchModule);
        } else {
            this.searchCollection = null;
        }
    },

    /**
     * Function to create a custom context with custom model and collection
     *
     * @param {context} context
     * @return {context}
     */
    getCustomContext: function(context) {
        let module = context.module || this.getSearchModule();
        context.model = app.data.createBean(module);
        context.collection = this.customEndpointCollection(module);
        return context;
    },

    /**
     * Function to create a custom collection with custom endpoint
     *
     * @param {string} module
     * @return {Data.BeanCollection}
     */
    customEndpointCollection: function(module) {
        let collection = app.data.createBeanCollection(module);
        let options = this.registerEndpoint({});
        collection.setOption(options);
        return collection;
    },

    /**
     * Function to register endpoint for a collection and inject filters before sending ajax call
     *
     * @param {Object} options
     * @return {Object|Data.BeanCollection}
     */
    registerEndpoint: function(options) {
        let self = this;
        options = _.extend({}, options, {
            endpoint: function(method, model, options, callbacks) {
                let filters = self.getAPIFilters(options.params.filter) || false;
                if (filters) {
                    options.params.filter = filters;
                }
                return self.getAPIData(method, model, options, callbacks);
            }
        });
        return options;
    },

    /**
     * Provide records by calling records api
     *
     * @param {string} method
     * @param {Bean} model
     * @param {Object} options
     * @param {Object} callbacks - Object containing callback functions i.e. success, error, etc.
     * @return {Data.BeanCollection}
     */
    getAPIData: function(method, model, options, callbacks) {
        let fields = model.attributes;
        if (method == 'update' || method == 'create') {
            fields = app.data.getEditableFields(model, options.fields);
        }
        return app.api.records(
            method,
            model.module,
            fields,
            options.params,
            callbacks,
            options.apiOptions
        );
    },

    /**
     * Provide filters for api by adding custom filetrs to original
     *
     * @param {Array} originalFilter
     * @return {Array}
     */
    getAPIFilters: function(originalFilter) {
        originalFilter = originalFilter || [];
        originalFilter = originalFilter.concat(this._getCustomFilter());
        return originalFilter;
    },

    /**
     * Provide custom filter on the bases of id and dri_workflow_template_id
     *
     * @return {Array|undefined}
     */
    _getCustomFilter: function() {
        let journeyId = this.model.get('dri_workflow_template_id');
        let id = this.model.get('id');
        if (!_.isEmpty(id) && !_.isEmpty(journeyId)) {
            return [
                {
                    $and: [
                        {
                            dri_workflow_template_id: {
                                $equals: journeyId,
                            },
                            id: {
                                $not_equals: id,
                            },
                        },
                    ],
                },
            ];
        } else if (_.isEmpty(id)) {
            return [
                {
                    $and: [
                        {
                            dri_workflow_template_id: {
                                $equals: journeyId,
                            },
                        },
                    ],
                },
            ];
        }
    },
});
