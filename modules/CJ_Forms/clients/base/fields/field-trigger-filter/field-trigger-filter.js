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
 * @class View.Fields.Base.CJForms.FieldTriggerFilterField
 * @alias SUGAR.App.view.fields.BaseCJFormsFieldTriggerFilterField
 * @extends View.Fields.Base.FilterField
 */
 ({
    extendsFrom: 'FilterField',
    previousModuleName: '',
    initialFilterDefinition: [],
    initialFilterTemplate: [],

    /**
     * @inheritdoc
     *
     */
    _loadTemplate: function() {
        this.type = 'field-trigger-filter';
        this._super('_loadTemplate');
        this.type = 'filter';
    },

    /**
     * @inheritdoc
     *
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (this.openBuildFilterView) {
            this.model.removeValidationTask(this.name + '_name_exists');
            this.model.addValidationTask(this.name + '_not_empty', _.bind(this._doValidateField, this));
        }
    },

    /**
     * Validates that filter field
     * is not empty
     *
     * @protected
     */
    _doValidateField: function(fields, errors, callback) {
        let mainTriggerType = this.model.get('main_trigger_type');
        if (_.isEmpty(mainTriggerType) || _.isEqual(mainTriggerType, 'smart_guide_to_sugar_action')) {
            callback(null, fields, errors);
            return;
        }

        let modelValue = this.format(this.model.get(this.name));
        if (
            _.isEmpty(modelValue) ||
            _.isEmpty(modelValue.filterId) ||
            _.isEmpty(modelValue.filterId.filter_definition)
        ) {
            errors[this.name] = errors[this.name] || {};
            errors[this.name].required = true;
        }
        callback(null, fields, errors);
    },

    /**
     * @inheritdoc
     *
     */
    _render: function() {
        if (this.shouldSetFilterData()) {
            this.setFiltersDataFromModel();
        }

        this._super('_render');

        if (this.$el.hasClass('error')) {
            this.handleFieldErrorDecoration();
        }

        if (_.isEqual(this.action, 'detail') || _.isEqual(this.action, 'preview')) {
            this.disposeComponents();
            this.renderComponents();
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.model) {
            this.listenTo(this.model, 'sync', this._render, this);
        }
    },

    /**
     * Handles field decoration in case of error
     */
    handleFieldErrorDecoration: function() {
        let $requiredIcon = $(this.exclamationMarkTemplate([app.error.getErrorString('required', true)]));
        let $filterBody = this.$el.find('.filter-body').first().find('.row-fluid > [data-filter="field"]');

        $filterBody.addClass('cj-filter-validation-error');
        $filterBody.append($requiredIcon);
    },

    /**
     * set the filter Data from the
     * model on rendering
     *
     */
    setFiltersDataFromModel: function() {
        if (this.model && this.model.has(this.name)) {
            try {
                let filterData = JSON.parse(this.model.get(this.name));
                if (filterData) {
                    this.initialFilterDefinition =
                        filterData.filterDef ||
                        filterData.filterId.currentFilterId.filter_definition ||
                        filterData.filterId.filter_id.filter_definition;
                    this.initialFilterTemplate =
                        filterData.filterDef ||
                        filterData.filterId.currentFilterId.filter_template ||
                        filterData.filterId.filter_id.filter_template;
                }
            } catch (e) {
                this.initialFilterDefinition = [];
                this.initialFilterTemplate = [];
            }
        }
    },

    /**
     * @inheritdoc
     *
     */
    initProperties: function() {
        this._super('initProperties');
        this.openBuildFilterView = this.def.openBuildFilterView;
    },

    /**
     * @inheritdoc
     *
     */
    onModuleChange: function(model, module) {
        if (this.filterCollection instanceof app.BeanCollection) {
            this.previousModuleName = this.filterCollection.moduleName;
        }
        this._super('onModuleChange', [model, module]);
    },

    /**
     * @inheritdoc
     *
     */
    loadComponents: function() {
        if (!this.canLoadComponents()) {
            if (_.isEmpty(this.model.get('module_trigger'))) {
                this.view.$el.find('div[data-name=field_trigger]').closest('.panel_body').addClass('hide');
            }
            return;
        }

        this.filterLoadInProgress = true;
        this.initFilterCollection();

        let options = {
            success: _.bind(function loadSuccess() {
                this.loadSuccessHelper();
            }, this),
            error: _.bind(function loadError() {
                this.filterLoadInProgress = false;

                if (this.disposed) {
                    return;
                }

                this.buildComponents();
                this.renderComponents();
            }, this)
        };

        this.filterCollection.load(options);
    },

    /**
     * Validate whether filter data should be set or not
     *
     * @return {boolean}
     */
    shouldSetFilterData: function() {
        return this.openBuildFilterView &&
            (!_.isUndefined(this.model._syncedAttributes) &&
                this.model.get('module_trigger') == this.model._syncedAttributes.module_trigger) &&
            (this.view.inlineEditMode ||
                _.isEqual(this.view.action, 'edit') ||
                ['edit', 'detail', 'preview'].includes(this.action));
    },

    /**
     * Success function for load
     * components
     *
     */
    loadSuccessHelper: function() {
        this.filterLoadInProgress = false;

        if (this.disposed) {
            return;
        }

        let filterModel = this.getFilterCollectionModelById();

        if (filterModel instanceof app.data.beanModel) {
            this.filterCollection.collection.defaultFilterFromMeta = this.filterId;
        }

        this.buildComponents();
        this.renderComponents();

        this.view.$el.find('div[data-name=field_trigger]').closest('.panel_body').removeClass('hide');

        if (this.shouldSetFilterData()) {
            this.filter.trigger('filter:create:close', this.filter.model);
            if (this.initialFilterDefinition) {
                this.filter.model.set('filter_definition', this.initialFilterDefinition);
            }
            if (this.initialFilterTemplate) {
                this.filter.model.set('filter_template', this.initialFilterTemplate);
            }
            this.filter.trigger('filter:create:open', this.filter.model);
        } else if (this.filterpanel instanceof app.view.Layout &&
            this.filter instanceof app.view.Layout  &&
            this.openBuildFilterView && !['preview'].includes(this.action)) {

            if (this.previousModuleName !== this.filterCollection.moduleName) {
                this.filter.trigger('filter:create:close', this.filter.model);
                this.filter.model.set('filter_definition', []);
                this.filter.model.set('filter_template', []);
            }
            this.filter.trigger('filter:create:open', this.filter.model);
        }
    },

    /**
     * @inheritdoc
     *
     */
    format: function(value) {
        value = this._super('format', [value]);
        if (this.openBuildFilterView) {
            try {
                value = this.model.has(this.name) ? JSON.parse(this.model.get(this.name)) : null;
            } catch (error) {
                value = null;
            }
        }

        return value;
    },

    /**
     * @inheritdoc
     *
     */
    getFilterCollectionModelById: function() {
        if (this.openBuildFilterView) {
            let filterModel = null;
            try {
                if (!_.isEmpty(this.filter) && !_.isEmpty(this.filter.model)) {
                    filterModel = this.filter.model;
                } else {
                    filterModel = this.filterModel;
                }
            } catch (e) {
                filterModel = null;
            }
            return filterModel;
        } else {
            return this._super('getFilterCollectionModelById');
        }
    },

    /**
     * @inheritdoc
     *
     */
    buildComponents: function() {
        this._super('buildComponents');

        if (this.filter instanceof app.view.Layout && this.filter.model instanceof app.data.beanModel) {
            this.listenTo(this.filter.model, 'change:filter_template', _.bind(this.onFilterChange, this));
        }
    },

    /**
     * @inheritdoc
     *
     */
    getFilterMeta: function() {
        let metadata = this._super('getFilterMeta');
        if (this.openBuildFilterView) {
            metadata = {
                'model': this.initFilterModel(),
                'collection': this.initFilterCollection(),
                'context': this.initFilterContext(),
                'module': this.getFilterModule() || this.filterDefaultModule,
                'layout': this,
                'action': this.action || this.view.action,
                'name': 'cj-filterpanel',
                'meta': {
                    'components': [
                        {
                            'layout': {
                                'name': 'filter',
                                'layout': 'filter',
                            }
                        },
                        {
                            'view': 'cj-field-trigger-filter-rows',
                        },
                    ],
                    'filterOptions': {
                        'show_actions': true,
                        'currentFilterId': this.filterId
                    }
                }
            };
        }

        return metadata;
    }
});
