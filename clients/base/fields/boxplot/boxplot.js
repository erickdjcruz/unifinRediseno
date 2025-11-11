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
 * @class View.Fields.Base.BoxplotField
 * @alias SUGAR.App.view.fields.BaseBoxplotField
 * @extends View.Fields.Base.BaseField
 */
({
    overallWorstCase: 0.0,

    overallBestCase: 0.0,

    overallCaseDifference: 0.0,

    worstCase: 0.0,

    bestCase: 0.0,

    likely: 0.0,

    likelyRound: 0,

    boxStart: 0.0,

    boxEnd: 0.0,

    boxWidth: 0.0,

    likelyPercent: 0.0,

    caretPos: 0.0,

    amountPos: 0.0,

    likelyMarginFromWorstCase: 0.0,

    initialize: function(options) {
        this._super('initialize', [options]);
        this._caseComparator();
    },

    bindDataChange: function() {
        this.collection.on('change', function() {
            this.collection.overallBestCase = null;
            this.collection.overallWorstCase = null;
            this._caseComparator();
        }, this);
    },

    /**
     * @inheritdoc
     *
     * calculates the width of the box-plot and the worst_case and the best_case end whiskers,
     * by calculating the worst_case and best_case value for a collection.
     * It also calculates the median line for the likely value position to be placed in the box plot.
     * @return {Object} this
     */
    _render: function() {
        this._boxPlotCalculator(this.overallBestCase, this.overallWorstCase);
        return this._super('_render');
    },

    /**
     * _caseComparator() accepts modelArray for the collection
     * and gets the overall worst_case value.
     * @param {Array} it accepts array of models of the collection
     * @return {Object} value minimum worst_case, maximum best_case
     * @private
     */
    _caseComparator: function() {
        if (this.collection && (!this.collection.overallBestCase || !this.collection.overallWorstCase)) {
            var min = Number.MAX_VALUE;
            var max = 0;
            var userDecPrecision = +app.user.getPreference('decimal_precision');
            var modelArray = this.collection.models;
            modelArray.forEach(function(model) {
                var worstCase = model.get('worst_case');
                var bestCase = model.get('best_case');
                if (max < parseFloat(bestCase)) {
                    max = parseFloat(bestCase);
                }
                if (min > parseFloat(worstCase)) {
                    min = parseFloat(worstCase);
                }
            });
            this.overallBestCase = max.toFixed(userDecPrecision);
            this.overallWorstCase = min.toFixed(userDecPrecision);
            this.collection.overallWorstCase = this.overallWorstCase;
            this.collection.overallBestCase = this.overallBestCase;
        } else {
            this.overallBestCase = this.collection.overallBestCase;
            this.overallWorstCase = this.collection.overallWorstCase;
        }
    },

    /**
     * _boxPlotCalculator() accepts best_Case and worst_case values relative to the entire collection
     * and calculates the box plot accordingly for the positive amounts.
     * @param {number} bestCase, worstCase
     * @private
     */
    _boxPlotCalculator: function(bestCase, worstCase) {
        var userDecPrecision = +app.user.getPreference('decimal_precision');

        this.overallCaseDifference = parseFloat((bestCase - worstCase).toFixed(userDecPrecision));

        this.worstCase = parseFloat(this.model.get('worst_case')).toFixed(userDecPrecision);
        this.likely =  parseFloat(this.model.get('amount')).toFixed(userDecPrecision);
        this.likelyRound = Math.round(this.likely);
        var likelyAmount = app.currency.formatAmountLocale(this.likely);
        this.likelyRound = likelyAmount.substring(0, likelyAmount.length - (userDecPrecision + 1));
        this.bestCase = parseFloat(this.model.get('best_case')).toFixed(userDecPrecision);

        this.likelyMarginFromWorstCase = this.likely - worstCase;

        this.likelyPercent = this.overallCaseDifference !== 0 ?
          parseFloat((this.likelyMarginFromWorstCase / this.overallCaseDifference).toFixed(userDecPrecision)) * 100 : 0;
        this.likelyPercent = this.likelyPercent === 100 ? this.likelyPercent - 1 : this.likelyPercent;
        this.caretPos =  this.likelyPercent - 2.7;
        this.amountPos = this.likelyPercent - (this.likelyRound.length + 3);
        this.amountPos = (this.amountPos <= 0) ? 0 : this.amountPos;

        this.boxStart = this.overallCaseDifference !== 0 ?
            ((this.worstCase - worstCase) / this.overallCaseDifference).toFixed(userDecPrecision) * 100 : 0;
        this.boxEnd = ((this.bestCase - worstCase) / this.overallCaseDifference).toFixed(userDecPrecision) * 100;
        this.boxEnd = this.overallCaseDifference !== 0 ?
            ((this.bestCase - worstCase) / this.overallCaseDifference).toFixed(userDecPrecision) * 100 : 0;
        this.boxWidth = parseFloat((this.boxEnd - this.boxStart).toFixed(userDecPrecision));
        this.boxWidth = (this.boxStart + this.boxWidth) === 100 ? parseFloat(this.boxWidth - 1) : this.boxWidth;
        this.boxWidth = isNaN(this.boxWidth) ? 99 : this.boxWidth;

        this.likely = app.currency.formatAmountLocale(this.likely);
        this.bestCase = app.currency.formatAmountLocale(this.bestCase);
        this.worstCase = app.currency.formatAmountLocale(this.worstCase);
    },
})
