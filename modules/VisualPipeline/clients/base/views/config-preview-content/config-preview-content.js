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
 * @class View.Views.Base.VisualPipeline.ConfigPreviewContentView
 * @alias SUGAR.App.view.views.BaseVisualConfigPreviewContentView
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        // events based on which we have to re-render the preview
        this.model.on('change', this.render, this);
        this.context.on('pipeline:config:tabs-initialized', this.setupTabChange, this);
        this.collection.on('change', this.render, this);
    },

    /**
     * Sets a listener on the module settings tab in the main content for
     * render the view when a tab is changed
     */
    setupTabChange: function() {
        var content = this.closestComponent('side-pane').layout;
        let tabControls = content.$('#tabs select.module-selection');
        _.each(tabControls, function(el) {
            $(el).on('change', _.bind(this.render, this));
        }, this);
    },

    /**
     * Removes listeners on the module settings tab in the main content
     * on dispose
     */
    removeTabChangeEvents: function() {
        var content = this.closestComponent('side-pane').layout;
        var tabControls = content.$('#tabs li.tab');
        _.each(tabControls, function(el) {
            $(el).off('click');
        }, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        //get the currently active tab
        var content = this.closestComponent('side-pane').layout;
        let currentTab = content.$('#tabs select.module-selection').val();

        //get the model shown in the current tab
        var currentModel = _.find(this.collection.models, function(model) {
            if (model.get('enabled_module') === currentTab) {
                return model;
            }
        }, this);

        //we will use this object in the preview
        this.previewModel = {};
        this.currentModel = currentModel;

        //if we have a currently selected model extract the information we want to show in the preview
        if (!_.isUndefined(currentModel)) {
            this.previewModel.moduleName = currentModel.get('enabled_module');
            this.previewModel.tile_header = this.getFieldLabel(currentModel.get('tile_header'));

            this.previewModel.tile_body_fields = [];
            _.each(currentModel.get('tile_body_fields'), function(fieldName) {
                this.previewModel.tile_body_fields.push(this.getFieldLabel(fieldName));
            }, this);

            this._super('render');
        }
    },

    /**
     * Returns the label value of a field based on the currently selected module
     * @return {string} The label of a field
     */
    getFieldLabel: function(fieldName) {
        var fields = app.metadata.getModule(this.previewModel.moduleName, 'fields');

        var label = '';
        _.each(fields, function(field) {
            if (_.isObject(field) && field.name === fieldName) {
                label = field.vname || field.label;

                return label;
            }
        });

        return label;
    },

    /**
     * Remove the tab events
     * @inheritdoc
     */
    _dispose: function() {
        this.removeTabChangeEvents();
        this._super('_dispose');
    }
});
