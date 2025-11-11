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
 * @class View.Views.Portal.PortalChatButtonView
 * @alias SUGAR.App.view.views.PortalChatButtonView
 * @extends View.View
 */
({
    className: 'portal-chat-button',

    BUTTON_STATE_OPEN: 'open',
    BUTTON_STATE_CLOSED: 'closed',

    events: {
        'click [data-action=portalchatbutton]': 'openChat'
    },

    /**
     * List of browsers supported by AWS Chat.
     * @property {Array}
     */
    supportedBrowsers: [
        'Chrome',
        'Firefox'
    ],

    /**
     * It will open a new chat popup.
     */
    openChat: function() {
        // check if the browser is supported
        if (!this.isSupportedBrowser(navigator.userAgent)) {
            return;
        }

        // if the chat component does not exist create it
        if (!this.portalChat) {
            this.createChatComponent();
            this.portalChat.initializeChat();
            return;
        }

        // Toggle chat session visibility
        if (this.portalChat.isExpanded) {
            this.portalChat.hideChat();
            return;
        }

        this.portalChat.showChat();

        if (!this.portalChat.chatSession) {
            this.portalChat.delayedInstantiateChat();
        }
    },

    /**
     * It will toggle the icon on the chat button.
     */
    toggleButtonState: function(state) {
        let btnPrimary = this.$el.find('.btn-primary');
        let chatConnected = false;

        if (this.portalChat && this.portalChat.chatSession) {
            chatConnected = (this.portalChat.chatSession.contactStatus === 'connected');
        }

        btnPrimary.toggleClass('chat-collapsed', state === this.BUTTON_STATE_CLOSED);
        btnPrimary.toggleClass('chat-connected', chatConnected);
    },

    /**
     * Checks if the current user has appropriate rights for viewing the button.
     */
    setAvailableFlag: function() {
        var isServePortal = app.config.isServe === true;
        var hasPortalAWSEnabled = app.config.awsConnectEnablePortalChat;
        var isPortalUser = app.user.get('type') === 'support_portal';
        this.isAvailable = isPortalUser && isServePortal && hasPortalAWSEnabled;
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.setAvailableFlag();
        this._super('_renderHtml');
    },

    /**
     * Checks if the current browser supports chat. If not, a message will be shown.
     *
     * @param {string} userAgent The user agents name.
     * @return {boolean} True if it is supported.
     */
    isSupportedBrowser: function(userAgent) {
        var isSupported = !!_.find(this.supportedBrowsers, function(browserName) {
            return userAgent.indexOf(browserName) !== -1 &&
                // exclude Microsoft Edge ('Edg' for newer versions)
                userAgent.indexOf('Edg') === -1;
        });
        if (!isSupported) {
            app.alert.show('portalchat-unsupported-browser', {
                level: 'error',
                messages: app.lang.get('LBL_PORTALCHAT_UNSUPPORTED_BROWSER')
            });
        }
        return isSupported;
    },

    /**
     * It will create the chat component.
     */
    createChatComponent: function() {
        var chatInterface = app.view.createView({
            type: 'chat-interface'
        });
        chatInterface.render();
        $('#sidecar').append(chatInterface.$el);
        app.events.on('app:login', function() {
            chatInterface.dispose();
        }, this);
        chatInterface.controlButton = this;
        this.portalChat = chatInterface;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        app.events.off('app:login', null, this);
    }
});
