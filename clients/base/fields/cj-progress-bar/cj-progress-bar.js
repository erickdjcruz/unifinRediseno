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
 * Show progress as a bar filled with color depending on the value
 *
 * @class View.Fields.Base.CjProgressBarField
 * @alias SUGAR.App.view.fields.BaseCjProgressBarField
 * @extends View.Fields.Base.BaseField
 */
({
    plugins: ['Tooltip'],

    /**
     * @inheritdoc
     *
     * @param {number} value
     * @return {number}
     */
    unformat: function(value) {
        let progress = parseFloat(value);
        progress /= 100;
        return progress;
    },

    /**
     * @inheritdoc
     *
     * @param {number} value
     * @return {number}
     */
    format: function(value) {
        let progress = parseFloat(value);
        progress *= 100;
        return Math.round(progress);
    },

    /**
     * @private
     */
    _loadTemplate: function() {
        // Always use the detail template
        this.options.viewName = 'detail';
        this._super('_loadTemplate');
    },

    /**
     * Change the color or Bar according to the state
     *
     * @inheritdoc
     */
    _render: function() {
        let state = this.model.get('state');
        this.barColor = '';

        if (state === 'cancelled') {
            this.barColor = 'cj-bar-red';
        } else if (state === 'completed') {
            this.barColor = 'cj-bar-green';
        }

        this._super('_render');
    },
})
