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
 * @class View.Views.Portal.ActivityTimelineBaseView
 * @alias SUGAR.App.view.views.PortalActivityTimelineBaseView
 * @extends View.Views.Base.ActivityTimelineBaseView
 */
({
    extendsFrom: 'ActivityTimelineBaseView',

    /**
     * Do we need to hide the activity panel
     */
    hideActivity: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        var baseId = this.baseRecord.get('id');

        if (baseId) {
            // fetch the complete record using id
            var baseBean = app.data.createBeanCollection(this.baseModule);
            baseBean.fetch({
                params: {filter: [{'id': baseId}]},
                success: _.bind(function(data) {
                    this.baseRecord = data.models ? data.models[0] : this.baseRecord;
                }, this),
                complete: _.bind(function(data) {
                    this._render();
                }, this)
            });
        }

        this.events = _.extend({}, this.events, {
            'click [data-action=addNote]': 'openNoteDrawer'
        });

        this.filter.module = 'all_modules';
    },

    /**
     * Return array of moludules
     *
     * @param baseModule
     * @return {any[]|*[]}
     */
    getEnabledModules: function(baseModule) {
        const meta = this.getModulesMeta(baseModule);
        if (!meta.activity_modules) {
            return [];
        }

        return _.map(meta.activity_modules, 'module');
    },

    /**
     * @inheritdoc
     */
    _getBaseModel: function(options) {
        var model;
        var currContext = options.context;
        var baseModule = currContext.get('module');
        var contextModel = currContext.get('model');

        if (contextModel && contextModel.module === baseModule) {
            model = contextModel;
        }
        return model || {};
    },

    /**
     * Open a drawer for writing a note.
     *
     * @param {Event} event
     */
    openNoteDrawer: function(event) {
        var model = this.createLinkModel(this.model, 'notes');

        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: 'Notes',
                model: model
            }
        }, _.bind(function(context, model) {
            if (model) {
                this.reloadData();
            }
        }, this));
    },
})
