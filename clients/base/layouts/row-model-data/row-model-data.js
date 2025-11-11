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
 * @class View.Layouts.Base.RowModelDataLayout
 * @alias SUGAR.App.view.layouts.RowModelDataLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.context.set('layout', options.context.get('layout') || 'multi-line');
        this.context.set('rowModel', options.context.get('model'));
        this.context = this.context.getChildContext({layout: this.context.get('layout')});
    },

    /**
     * Extends the base loadData to make sure that we have a fully fetched bean
     * for the rowModel
     * @param options
     */
    loadData: function(options) {
        if (this.options && this.options.context && this.options.context.get('modelId')) {
            var rowModelBean = app.data.createBean(this.options.context.get('module'), {
                id: this.options.context.get('modelId')
            });

            app.alert.show('load_row_data_model', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
                autoClose: false
            });

            rowModelBean.fetch({
                success: _.bind(function() {
                    if (this.disposed) {
                        return;
                    }
                    this.context.parent.set('rowModel', rowModelBean);
                    _.each(app.sideDrawer._tabs, function(tab) {
                        if (tab.context.modelId === rowModelBean.get('id')) {
                            rowModelBean.set(
                                'tag',
                                this._format(rowModelBean.has('tag') ? rowModelBean.get('tag') : null)
                            );
                            tab.context.model = rowModelBean;
                        }
                    }, this);
                    this._super('loadData', [options]);
                }, this),
                complete: function() {
                    app.alert.dismiss('load_row_data_model');
                },
                viewed: true,
                view: 'record',
            });
        } else {
            this._super('loadData', [options]);
        }
    },

    /**
     * @param {Array} value
     */
    _format: function(value) {
        return _.map(value, function(tag) {
            return _.extend(tag, {encodedValue: encodeURIComponent(tag.name)});
        });
    },

    /**
     * Change row model.
     * @param {Object} model The new row model
     * @return {boolean} true if model changed, otherwise false
     */
    setRowModel: function(model) {
        var dashboard = this.getComponent('row-model-data').getComponent('dashboard');
        if (dashboard && dashboard.model.mode !== 'edit') {
            this.context.parent.set('rowModel', model);
            dashboard.getComponent('dashlet-main').setMetadata();
            return true;
        }
        return false;
    },

    /**
     * Retrieves the current row model
     *
     * @return {Bean|null} the current row model if it exists; null otherwise
     */
    getRowModel: function() {
        var focusedRecord = this.context && this.context.parent && this.context.parent.get('rowModel');
        return focusedRecord || null;
    }
})
