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
 * @class View.Fields.Base.PhoneField
 * @alias SUGAR.App.view.fields.BasePhoneField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     *
     * The direction for this field should always be `ltr`.
     */
    direction: 'ltr',

    /**
     * Listen for CCP phone number clicks
     */
    events: {
        'click .ccp-outgoing': '_dialNumber',
    },

    /**
     * Is a CCP agent logged in? Defaults to false.
     */
    ccpEnabled: false,

    /**
     * @override
     * @param options
     */
    initialize: function (options) {
        var serverInfo = app.metadata.getServerInfo();

        this.dialoutEnabled = serverInfo.system_skypeout_on ? true : false;
        this.ccpEnabled = window.connect && window.connect.core.initialized;

        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        app.events.on('ccp:initiated', this._enableCCP, this);
        app.events.on('ccp:terminated', this._disableCCP, this);
    },

    /**
     * Attempt to dial phone number via CCP. Warn user if calling fails.
     * @param {Object} event - JQuery click event
     * @private
     */
    _dialNumber: function(event) {
        if (!(window.connect && connect.core.initialized)) {
            app.alert.show('ccp-not-initiated', {
                level: 'error',
                messages: app.lang.get('LBL_CCP_NOT_INITIATED')
            });
            return;
        }

        if (this._activeChatSession()) {
            app.alert.show('dialout-not-allowed', {
                level: 'warning',
                messages: app.lang.get('LBL_CCP_DIALOUT_NOT_ALLOWED')
            });
            return;
        }

        try {
            var phoneNumber = event.target.text;
            var agent = new connect.Agent();
            var arn = agent.getRoutingProfile().defaultOutboundQueue.queueARN;
            var endpoint = connect.Endpoint.byPhoneNumber(phoneNumber);
            agent.connect(endpoint, {
                queueARN: arn, success: _.bind(this._callSuccess, this), failure: this._callFailure
            });
        } catch (e) {
            // this occurs if there's an error in calling the library functions.
            // Error in the call itself is handled in the failure callback.
            app.logger.error(e);
            app.alert.show('ccp-call-error', {
                level: 'error',
                messages: app.lang.get('LBL_CCP_LIBRARY_CALLOUT_ERROR')
            });
        }
    },

    /**
     * Determine if there's an active chat session in the CCP.
     * @private
     */
    _activeChatSession: function() {
        var agent = new connect.Agent();
        var currentContacts = agent.getContacts();
        return _.reduce(currentContacts, function(memo, contact) {
            return memo || contact.getType() === connect.MediaType.CHAT;
        }, false);
    },

    /**
     * Callback for successful outgoing call
     * Set the phone number on the omnichannel.
     * @private
     */
    _callSuccess: function() {
        if (app.omniConsole) {
            app.omniConsole.context.set('lastDialedRecord', this.model);
        }
    },

    /**
     * Callback for call failure. Example includes bad phone number, failure in
     * CCP dialer, etc.
     *
     * @param {Object} err - API Error
     * @private
     */
    _callFailure: function(err) {
        app.alert.show('ccp-call-error', {
            level: 'error',
            messages: app.lang.get('LBL_CCP_DIALING_ERROR')
        });
        app.logger.error(err);
    },

    /**
     * Convert phone number to link
     * @private
     */
    _enableCCP: function() {
        this.ccpEnabled = true;
        this.render();
    },

    /**
     * Convert phone number to text
     * @private
     */
    _disableCCP: function() {
        this.ccpEnabled = false;
        this.render();
    },

    /**
     * Remove event listeners on dispose
     * @private
     */
    _dispose: function() {
        this._super('_dispose');
        app.events.off('ccp:initiated', null, this);
        app.events.off('ccp:terminated', null, this);
    },

})
