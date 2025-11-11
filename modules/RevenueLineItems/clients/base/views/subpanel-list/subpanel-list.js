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
 * Custom Subpanel Layout for Revenue Line Items.
 *
 * @class View.Views.Base.RevenueLineItems.SubpanelListView
 * @alias SUGAR.App.view.views.BaseRevenueLineItemsSubpanelListView
 * @extends View.Views.Base.SubpanelListView
 */
({
    extendsFrom: 'SubpanelListView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        //Extend the prototype's events object to setup additional events for this controller
        this.events = _.extend({}, this.events, {
            'click [name=inline-cancel]': 'cancelClicked'
        });
    },

    /**
     * We have to overwrite this method completely, since there is currently no way to completely disable
     * a field from being displayed
     *
     * @returns {{default: Array, available: Array, visible: Array, options: Array}}
     */
    parseFields : function() {
        var catalog = this._super('parseFields'),
            config = app.metadata.getModule('Forecasts', 'config'),
            isForecastSetup = (config && config.is_setup);

        // if forecast is not setup, we need to make sure that we hide the commit_stage field
        _.each(catalog, function (group, i) {
            var filterMethod = _.isArray(group) ? 'filter' : 'pick';
            if (isForecastSetup) {
                catalog[i] = _[filterMethod](group, function(fieldMeta) {
                    if (fieldMeta.name.indexOf('_case') != -1) {
                        var field = 'show_worksheet_' + fieldMeta.name.replace('_case', '');
                        return (config[field] == 1);
                    }

                    return true;
                });
            } else {
                catalog[i] = _[filterMethod](group, function(fieldMeta) {
                    return (fieldMeta.name != 'commit_stage');
                });
            }
        });

        return catalog;
    },

    /**
     * @inheritdoc
     *
     * Tracks the last row where the view was changed to non-edit
     */
    toggleRow: function(modelId, isEdit) {
        this._super('toggleRow', [modelId, isEdit]);
        if (!isEdit) {
            this.lastToggledModel = this.collection.get(modelId);
        }
    },

    /**
     * Adds a reverting of model attributes when cancelling an edit view of
     * a row. This fixes issues with service fields not properly clearing when
     * cancelling the edit
     */
    cancelClicked: function() {
        if (this.lastToggledModel) {
            this.lastToggledModel.revertAttributes();
        }
        this.resize();
    }
})
