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
 * @class View.Fields.Base.SentimentField
 * @alias SUGAR.App.view.fields.BaseSentimentField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    /**
     * sentiment label
     */
    sentimentLabel: '',

    /**
     * sentiment icon
     */
    sentimentIcon: '',

    /**
     * @inheritdoc
     * @private
     */
    _loadTemplate: function() {
        this._super('_loadTemplate');
        // use detail view template
        this.template = app.template.getField('sentiment', 'detail', this.model.module);
    },

    /**
     * Map sentiment score to Positive, Neutral and Negative
     */
    mapSentiment: function() {
        var val = this.model.get(this.name);
        if (val !== null && val !== undefined && !isNaN(val)) {
            var path = 'styleguide/assets/img/sentiment/';
            if (val > 1.3) {
                this.sentimentIcon = path + 'Positive.svg';
                this.sentimentLabel = 'LBL_SENTIMENT_POSITIVE';
            } else if (val < -1.3) {
                this.sentimentIcon = path + 'Negative.svg';
                this.sentimentLabel = 'LBL_SENTIMENT_NEGATIVE';
            } else {
                this.sentimentIcon = path + 'Neutral.svg';
                this.sentimentLabel = 'LBL_SENTIMENT_NEUTRAL';
            }
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.mapSentiment();
        this._super('_render');
    }
})
