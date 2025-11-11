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
 *
 * This view displays the selected records at the top of a selection list. It
 * also allows to unselect them.
 *
 * @class View.Views.Base.DashboardsSelectionListContextView
 * @alias SUGAR.App.view.views.DashboardsSelectionListContextView
 * @extends View.View.BaseSelectionListContext
 */
({
    extendsFrom: 'SelectionListContext',

    /**
     * Adds a pill in the template.
     * This overrides the base fucntion in order to translate the dashboard name
     *
     * @param {Data.Bean|Object|Array} models The model, set of model attributes
     * or array of those corresponding to the pills to add.
     */
    addPill: function(models) {
        models = _.isArray(models) ? models : [models];

        if (_.isEmpty(models)) {
            return;
        }

        var pillsAttrs = [];
        var pillsIds = _.pluck(this.pills, 'id');

        _.each(models, function(model) {
            var modelName = app.lang.get(model.get('name'), model.get('dashboard_module'));

            if (modelName && !_.contains(pillsIds, model.id)) {
                pillsAttrs.push({id: model.id, name: modelName});
            }
        });

        this.pills.push.apply(this.pills, pillsAttrs);

        this._debounceRender();
    },
})
