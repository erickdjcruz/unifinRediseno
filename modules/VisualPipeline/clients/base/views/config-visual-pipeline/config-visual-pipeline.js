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
 * @class View.Views.Base.VisualPipeline.ConfigPanelView
 * @alias SUGAR.App.view.views.BaseVisualConfigPanelView
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseConfigPanelView',

    selectedModules: [],

    activeTabIndex: 0,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.customizeMetaFields();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.collection.on('add remove reset', this.render, this);
        this.collection.on('change:show_column_total', this._toggleDepedent, this);
        this.listenTo(this.context, 'pipeline:config:set-active-module', this._setupActiveModule);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');
        //event used in tile preview
        this.context.trigger('pipeline:config:tabs-initialized');
    },

    /**
     * @inheritdoc
     * When rendering fields, handle the state of the axis labels
     */
    _renderField: function(field) {
        let noTotalableFields = _.isEmpty(_.first(this.activeModule).get('tabContent').allTotalableFields);
        if (field.name === 'show_column_total_options' && noTotalableFields) {
            return;
        }
        this._super('_renderField', [field]);

        // manage display state of fieldsets with toggle
        this._toggleDepedent();
    },

    /**
     *  Adds the fields to the module into a two column layout
     */
    customizeMetaFields: function() {
        var twoColumns = [];
        var customizedFields = []; // To use as row in the UI

        _.each(this.meta.panels, function(panel) {
            _.each(panel.fields, function(field) {
                if (field.twoColumns) {
                    twoColumns.push(field);
                    if (twoColumns.length === 2) {
                        customizedFields.push(twoColumns);
                        twoColumns = [];
                    }
                } else {
                    customizedFields.push([field]);
                }
            }, this);
        }, this);

        this.meta.customizedFields = customizedFields;
    },

    /**
     * Set active module and render
     *
     * @param {string} activeModule
     */
    _setupActiveModule: function(activeModule) {
        this.activeModule = _.filter(this.collection.models, function(model) {
            model.set('selectedModule', model.get('enabled_module') === activeModule);
            return model.get('enabled_module') === activeModule;
        });
        this.render();
    },

    /**
     * Handle the conditional display of settings input field based on checkbox toggle state
     */
    _toggleDepedent: function() {
        const checkboxField = this.getField('show_column_total').$('input');
        this.getField('total_field').setDisabled(!checkboxField.prop('checked'));
    },
})
