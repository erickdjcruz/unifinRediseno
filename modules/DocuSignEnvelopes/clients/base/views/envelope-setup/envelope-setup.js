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
 * @class View.Views.Base.DocuSignEnvelopes.EnvelopeSetupView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesEnvelopeSetupView
 * @extends View.Views.Base.View
 */
({
    events: {
        'change input[name=envelopeName]': 'envelopeNameChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        const payload = this.context.get('payload');
        if (!_.isUndefined(payload) && !_.isUndefined(payload.template)) {
            this._envelopeName = payload.template.name;
        } else {
            this._envelopeName = '';
        }

        this.context.set('_envelopeName', this._envelopeName);
    },

    /**
     * Envelope name changed
     */
    envelopeNameChanged: function() {
        this._envelopeName = this.$el.find('input[name=envelopeName]').val();

        this.context.set('_envelopeName', this._envelopeName);
    }
});
