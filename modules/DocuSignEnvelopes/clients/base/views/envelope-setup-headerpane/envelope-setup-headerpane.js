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
 * @class View.Views.Base.DocuSignEnvelopes.EnvelopeSetupHeaderpaneView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesEnvelopeSetupHeaderpaneView
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._registerEvents();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.layout, 'setup:closedrawer:fire', this.closeDrawer, this);
        this.listenTo(this.layout, 'setup:back:fire', this.navigateBack, this);
        this.listenTo(this.layout, 'setup:send:fire', this.send, this);
    },

    /**
     * Close drawer
     */
    closeDrawer: function() {
        this.$el.off();
        app.drawer.close({
            closeEvent: true
        });
    },

    /**
     * Navigate to previous drawer
     */
    navigateBack: function() {
        this.$el.off();

        this._sendingPayload = this.context.get('payload');

        let closeOptions = {
            closeEvent: true,
        };

        if (app.DocuSign.utils.shouldShowRecipients() || !_.isUndefined(this._sendingPayload.templateSelected)) {
            closeOptions.dsProcessWillContinue = true;
        }

        app.drawer.close(closeOptions);

        if (app.DocuSign.utils.shouldShowRecipients()) {
            if (this._sendingPayload.composite) {
                _.debounce(_.bind(this._openCompositeRecipientsDrawer, this), 1000)();
            } else {
                _.debounce(_.bind(this._openRecipientsDrawer, this), 1000)();
            }
        } else if (!_.isUndefined(this._sendingPayload.templateSelected)) {
            if (this._sendingPayload.composite) {
                _.debounce(_.bind(this._openCompositeTemplatesListDrawer, this), 1000)();
            } else {
                _.debounce(_.bind(this._openTemplatesListDrawer, this), 1000)();
            }
        }
    },

    /**
     * Continue sending the envelope
     */
    send: function() {
        const validationResult = this._validateEnvelopeSetup();
        if (_.isString(validationResult)) {
            app.alert.show('error-envelope-setup', {
                level: 'error',
                messages: validationResult,
                autoClose: true,
            });

            return;
        }

        this.$el.off();

        app.drawer.close({
            envelopeName: this.context.get('_envelopeName'),
        });
    },

    /**
     * Open recipients drawer
     */
    _openRecipientsDrawer: function() {
        app.events.trigger('docusign:send:initiate', this._sendingPayload);
    },

    /**
     * Open composite recipients drawer
     */
    _openCompositeRecipientsDrawer: function() {
        app.events.trigger('docusign:compositeSend:initiate', this._sendingPayload);
    },

    /**
     * Open templates list drawer
     */
    _openTemplatesListDrawer: function() {
        app.events.trigger('docusign:send:initiate', this._sendingPayload, 'selectTemplate');
    },

    /**
     * Open composite templates list drawer
     */
    _openCompositeTemplatesListDrawer: function() {
        app.events.trigger('docusign:compositeSend:initiate', this._sendingPayload, 'selectTemplate');
    },

    /**
     * Validate envelope setup
     *
     * @return {mixed}
     */
    _validateEnvelopeSetup: function() {
        const envelopeName = this.context.get('_envelopeName');
        if (_.isEmpty(envelopeName)) {
            return app.lang.get('LBL_ENVELOPE_NAME_EMPTY', 'DocuSignEnvelopes');
        }

        return true;
    },
});
