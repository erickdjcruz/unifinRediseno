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
 * Show momentum_ratio as a bar with different colors depending on the value
 *
 * @class View.Fields.Base.CjMomentumBarField
 * @alias SUGAR.App.view.fields.BaseCjMomentumBarField
 * @extends View.Fields.Base.BaseField
 */
({
    plugins: ['Tooltip'],

    /**
     * Constant variable on the basis of which bar color will change to green
     */
    THREE_FOURTH_RATIO: 0.75,

    /**
     * Constant variable on the basis of which bar color will change to yellow
     */
    HALF_RATIO: 0.5,

    /**
     * Constant variable on the basis of which bar color will change to light orange
     */
    ONE_FOURTH_RATIO: 0.25,

    /**
     * Change the color or Bar according to the momentum ratio
     *
     * @inheritdoc
     */
    _render: function() {
        let ratio = this.model.get('momentum_ratio');
        this.barColor = 'cj-bar-red';

        if (ratio >= this.THREE_FOURTH_RATIO) {
            this.barColor = 'cj-bar-green';
        } else if (ratio >= this.HALF_RATIO) {
            this.barColor = 'cj-bar-yellow';
        } else if (ratio >= this.ONE_FOURTH_RATIO) {
            this.barColor = 'cj-bar-orange';
        }

        this._super('_render');
    },

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
    }
})
