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
 * @class View.Views.Base.MixedSelectionListContextView
 * @alias SUGAR.App.view.views.BaseMixedSelectionListContextView
 * @extends View.View.Base.SelectionListContextView
 */
({
    extendsFrom: 'SelectionListContextView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this.mixedCollection = this.context.get('mixed_collection');

        if (!(this.mixedCollection instanceof app.data.beanCollection)) {
            this.mixedCollection = app.data.createMixedBeanCollection();
            this.context.set('mixed_collection', this.mixedCollection);
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.massCollection = this.context.get('mass_collection');
        if (!this.massCollection) {
            return;
        }

        if (this.pills.length === 0 && this.mixedCollection.models.length > 0) {
            this.pills = _.map(this.mixedCollection.models, function(model) {
                return {
                    id: model.get('id'), name: model.get('name')
                };
            });
        }

        if (this.pills.length > this.maxPillsDisplayed) {
            this.displayedPills = this.pills.slice(0, this.maxPillsDisplayed);
            this.tooManySelectedRecords = true;
            this.msgMaxPillsDisplayed = app.lang.get('TPL_MAX_PILLS_DISPLAYED', this.module, {
                maxPillsDisplayed: this.maxPillsDisplayed
            });
        } else {
            this.tooManySelectedRecords = false;
            this.displayedPills = this.pills;
        }

        var recordsLeft = this.mixedCollection.length - this.displayedPills.length;
        if (recordsLeft) {
            this.moreRecords = true;
            var label = this.displayedPills.length ? 'TPL_MORE_RECORDS' : 'TPL_RECORDS_SELECTED';
            this.msgMoreRecords = app.lang.get(label, this.module, {
                recordsLeft: recordsLeft
            });
        } else {
            this.moreRecords = false;
        }

        app.view.View.prototype._render.call(this);

        this.stopListening(this.massCollection);
        this.stopListening(this.mixedCollection);

        this.listenTo(this.massCollection, 'change add sync reset',  this.syncMixedCollection);
        this.listenTo(this.massCollection, 'remove',  this.syncMixedCollectionRemove);
        this.listenTo(this.massCollection, 'massupdate:estimate',  this.syncMixedCollectionAddAll);

        this.listenTo(this.massCollection, 'add', this.addPill);
        this.listenTo(this.massCollection, 'remove', this.removePill);
        this.listenTo(this.massCollection, 'reset', this.resetPills);

        this.makeSureAllPillsHaveNames();
    },

    /**
     * Sync mixed collection
     *
     * @param {Object} model
     * @param {Object} collection
     * @param {Object} options
     */
    syncMixedCollection: function(model, collection, options) {
        if (_.isUndefined(options)) {
            options = arguments[1];
            this.mixedCollection.remove(options.previousModels);
        }
        if (options.add) {
            this.mixedCollection.add(model);
        }
        if (options.remove) {
            this.mixedCollection.remove(model);
        }

        this.makeSureAllPillsHaveNames();
    },

    /**
     * Sync mixed collection - action Remove
     *
     * @param {Object} model
     */
    syncMixedCollectionRemove: function(model) {
        this.mixedCollection.remove(model);

        this.makeSureAllPillsHaveNames();
    },

    /**
     * Sync mixed collection - action Add all
     */
    syncMixedCollectionAddAll: function() {
        this.listenToOnce(this.massCollection, 'reset', _.bind(function() {
            if (this.massCollection.entire) {
                _.each(this.massCollection.models, function(model) {
                    this.mixedCollection.add(model);
                }, this);
            }

            this.render();
        }, this));
    },

    /**
     * When using Select All, the collection is updated and is only left with ids.
     * We have to make sure that we always have record names for pills shown
     */
    makeSureAllPillsHaveNames: function() {
        const pillsWithouthNames = _.filter(this.displayedPills, function(pill) {
            if (_.isUndefined(pill.name) || _.isEmpty(pill.name)) {
                return true;
            }
            return false;
        });

        if (pillsWithouthNames.length > 0) {
            let bulkFetchRequests = [];
            _.each(pillsWithouthNames, function(pill) {
                const record = this.mixedCollection.get(pill.id);
                const module = record.get('_module');
                const url = app.api.buildURL(module, 'read', {
                    id: pill.id
                }, {
                    fields: ['id', 'name']
                });

                bulkFetchRequests.push({
                    url: url.substr(4),
                    method: 'GET',
                });
            }, this);

            app.api.call('create', app.api.buildURL(null, 'bulk'), {
                requests: bulkFetchRequests
            },
            {
                success: _.bind(function(responses) {
                    _.each(responses, function(response) {
                        if (response.contents) {
                            _.each(this.pills, (pill) => {
                                if (pill.id === response.contents.id) {
                                    pill.name = response.contents.name;
                                }
                            });
                            _.each(this.displayedPills, (pill) => {
                                if (pill.id === response.contents.id) {
                                    pill.name = response.contents.name;
                                }
                            });
                        }
                    }, this);
                    this.render();
                }, this)
            });
        }
    }
})
