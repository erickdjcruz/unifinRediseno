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
 * @class View.Views.Base.Cases.RequestClosedCasesDashlet
 * @alias SUGAR.App.view.views.BaseCasesRequestClosedCasesDashlet
 * @extends @extends View.Views.Base.ListView
 */
({
    plugins: ['Dashlet'],

    extendsFrom: 'ListView',

    /**
     * Fields displayed in dashlet
     *
     * @property {Array}
     */
    displayedFields: [
        'case_number',
        'name',
        'priority',
        'status',
        'date_modified',
    ],

    /**
     * Cases bean collection.
     *
     * @property {Data.BeanCollection}
     */
    collection: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initCollection();
    },

    /**
     * Initialize feature collection.
     */
    _initCollection: function() {
        var today = app.date().formatServer(true);
        var self = this;
        this.collection = app.data.createBeanCollection(this.module);
        this.collection.setOption({
            fields: this.displayedFields,
            filter: {
                'request_close': {
                    '$equals': 1
                },
                'status': {
                    '$not_in': ['Closed']
                },
                '$owner': ''
            },
        });
        this.collection.displayedFields = this._initDisplayedFields();

        // set meta last state id so sorting order is maintained
        this.meta.last_state = {id: 'request-closed-cases-dashlet'};
        this.orderByLastStateKey = app.user.lastState.key('order-by', this);
        this.orderBy = this._initOrderBy();
        if (this.collection) {
            this.collection.orderBy = this.orderBy;
        }

        return this;
    },

    /**
     * Returns the displayed field objects
     *
     * @return {Array} the field objects
     * @private
     */
    _initDisplayedFields: function() {
        var displayedFields = [];

        _.each(this.displayedFields, function(field) {
            if (!this.model.fields) {
                return;
            }
            var toPush = this.model.fields[field];
            toPush.link = (field === 'name') ? true : false;
            displayedFields.push(toPush);
        }, this);

        return displayedFields;
    },

    /**
     * @inheritdoc
     *
     * Once collection has been changed, the view should be refreshed.
     */
    bindDataChange: function() {
        if (this.collection) {
            this.collection.on('add remove reset', function() {
                if (this.disposed) {
                    return;
                }
                this.render();
            }, this);
        }
    },

    /**
     * @inheritdoc
     */
    _setOrderBy: function(options) {
        if (this.orderByLastStateKey) {
            app.user.lastState.set(this.orderByLastStateKey, this.orderBy);
        }
        this.loadData(options);
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        this.collection.fetch(options);
    },

})
