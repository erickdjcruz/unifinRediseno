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
 * @class View.Views.Base.PipelineHeaderpaneView
 * @alias SUGAR.App.view.views.BasePipelineHeaderpaneView
 * @extends View.Views.Base.PipelineHeaderpaneView
 */
({
    events: {
        'click a[name=pipelineBtn]': 'changePipeline',
    },

    /**
     * Initializes various pipelineType fields and the table_header
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.context.on('button:pipeline_create_button:click', this.createNewRecord, this);
        this.pipelineTypes = [];
        this.table_header = app.metadata.getModule('VisualPipeline','config').table_header[this.module];
        _.each(this.meta.fields, function(field) {
            this.pipelineTypes.push(field);
        }, this);
        this.pipelineStateKey = app.user.lastState.buildKey('last-tab', 'pipeline', this.module);

        // default to the config-set table header
        var pipelineType = this.table_header;
        if (this.module === 'Opportunities') {
            // unless we're in Opps then try to set the pipelineType by the last state first
            pipelineType = app.user.lastState.get(this.pipelineStateKey) || 'date_closed';
        }

        this.context.get('model').set('pipeline_type', pipelineType);
    },

    /**
     * Gets triggered when switching pipeline tabs
     * Changes the css classes to reflect the selected tab
     * @param event
     */
    changePipeline: function(event) {
        var $currentTarget = this.$(event.currentTarget);
        if ($currentTarget.hasClass('active')) {
            return;
        }

        this.$('.tab.active').removeClass('active');
        $('.close-drawer button').trigger('click');
        $currentTarget.addClass('active');
        var pipelineType = $currentTarget.data('pipeline');

        // set the new last state
        app.user.lastState.set(this.pipelineStateKey, pipelineType);

        this.context.get('model').set('pipeline_type', pipelineType);

        // apply filter
        let filterPanel = this.layout.getComponent('pipeline-filterpanel');
        if (filterPanel) {
            filterPanel.trigger('filter:apply');
        }
    },

    /**
     * Opens the create drawer for the user to create a new record when the create button on headerpane is clicked
     */
    createNewRecord: function() {
        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: this.module,
            }
        }, _.bind(function(context, model) {
            if (model && model.dataFetched) {
                this.context.trigger('pipeline:recordlist:model:created', model);
            }
        }, this));
    }
})
