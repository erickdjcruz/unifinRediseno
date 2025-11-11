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
 * @class View.Fields.Base.DRIWorkflows.ConfigureRecordViewDisplayEnumField
 * @alias SUGAR.App.view.fields.DRIWorkflowsConfigureRecordViewDisplayEnumField
 * @extends View.Fields.Base.EnumField
 */
({
    /**
     * ConfigureRecordviewDisplayEnum FieldTemplate (base)
     */
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';
        this.def.dropdown_class = 'cj-configure-recordview-display-content-field';
    },

    /**
     * @inheritdoc
     */
    _sortResults: function(results, container, query) {
        let sortedResults = this._super('_sortResults', [results, container, query]);
        return this.disableOptions(sortedResults);
    },

    /**
     *
     * @param {Array} results
     * @return {Array}
     */
    disableOptions: function(results) {
        if (!this._hasTabEnabledForView()) {
            _.each(results, function(li, idx) {
                let key = li.id || '';
                if (key.toString().includes('tab')) {
                    results[idx].disabled = true;
                }
            }, this);
        }
        return results;
    },

    /**
     * Check if atleast one tab exists in view
     *
     * @return {boolean|undefined}
     * @private
     */
    _hasTabEnabledForView: function(view = 'record') {
        if (_.isEmpty(this.def.baseModule)) {
            return;
        }
        let moduleRecordView = app.metadata.getView(this.def.baseModule, view);
        let enabledTab = false;
        if (this._hasMetaPanels(moduleRecordView)) {
            _.each(moduleRecordView.panels, function(panel, index) {
                if (!_.isUndefined(panel.newTab) && app.utils.isTruthy(panel.newTab)) {
                    enabledTab = true;
                }
            });
        }
        return enabledTab;
    },

    /**
     * check if panels meta is defined and not empty
     *
     * @param {Object}
     * @return {bolean}
     * @private
     */
    _hasMetaPanels: function(moduleRecordView) {
        return (!_.isEmpty(moduleRecordView) && !_.isEmpty(moduleRecordView.panels));
    },
});
