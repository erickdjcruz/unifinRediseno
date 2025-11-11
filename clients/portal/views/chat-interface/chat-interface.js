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
 * @class View.Views.Portal.ChatInterfaceView
 * @alias SUGAR.App.view.views.PortalChatInterfaceView
 * @extends View.View
 */
({
    className: 'portal-chat-interface',

    /**
     * Indicates if the resource files necessary for the chat to run have been loaded or not.
     */
    libraryLoaded: false,

    /**
     * The active chat instance. Null if there is not active chat instance.
     */
    chatSession: null,

    /**
     * Template for Chat Header
     */
    chatHeaderTemplate: null,

    /**
     * Template for Chat Footer
     */
    chatFooterTemplate: null,

    /**
     * Chat open animation time in Milliseconds. This animation timing is defined
     * in the portal-chat-interface .less styles.
     */
    chatAnimationTiming: 600,

    /**
     * Default options for chat styles. Used as a fallback in case an admin
     * config is blank.
     */
    styles: {
        awsHeaderBackgroundColor: '#265a8d',
        awsHeaderTitleColor: '#ffffff',
        awsHeaderSubtitleColor: '#ffffff',
        awsHeaderImageUrl: '../themes/default/images/company_logo_inverted.png',
        awsFooterTitleColor: '#9aa5ad',
        awsEndChatButtonTextColor: '#000000',
        awsEndChatButtonWidth: '140',
        awsEndChatButtonHeight: '40',
        awsEndChatButtonFill: '#0679c8',
        awsMessageCustomerBubbleColor: '#dae8f7',
        awsMessageAgentBubbleColor: '#d5ece0',
        awsMessageTextColor: '#000000'
    },

    /**
     * It shows if the chat is visible to the user or not.
     */
    isExpanded: false,

    loadingClass: '.cpp-loader-container',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        // Event listener for manual refreshes
        $(window).on('beforeunload', _.bind(this._warnOnRefresh, this));
    },

    initialize: function(options) {
        this._super('initialize', [options]);
        this.chatHeaderTemplate = app.template.get('chat-interface.chat-header');
        this.chatFooterTemplate = app.template.get('chat-interface.chat-footer');
        this.styles = this.generateChatStyles();
    },

    /**
     * Shows an error message about a failed call attempt and logs the message.
     *
     * @param {Object} error Details of the exception thrown by an unsuccessful code.
     */
    logException: function(error) {
        app.alert.show(error.name, {
            level: 'error',
            messages: 'LBL_PORTALCHAT_CAN_NOT_LOAD_LIB'
        });
        app.logger.error('Loading connect-streams library failed: ' + error);
    },

    /**
     * Will try to load an Amazon Connect library.
     *
     * @param {string} libName The name if the amazon connect library to be loaded.
     * @param {Function} callBack A callback to be executed on load.
     */
    loadLibrary: function(libName, callBack) {
        var urlBase = 'include/javascript/amazon-connect/amazon-connect-';
        var url = app.utils.buildUrl(urlBase + libName);
        try {
            $.getScript(url, function() {
                callBack();
            });
        } catch (error) {
            this.logException(error);
        }
    },

    /**
     * Loads the resources the chat is dependent of.
     */
    loadDependencies: function() {
        var loadInterface = _.partial(this.loadLibrary, 'chat-interface.js', _.bind(this.createChatWidget, this));
        var loadChat = _.partial(this.loadLibrary, 'chat.js', loadInterface);
        this.loadLibrary('streams.js', loadChat);
    },

    /**
     * It will set the necessary configuration and it will initialize the chat placeholder.
     */
    createChatWidget: function() {
        this.libraryLoaded = true;
        this.showChat();

        var globalConfig = {
            region: this.region
        };
        connect.ChatSession.setGlobalConfig(globalConfig);

        connect.ChatInterface.init({
            containerId: 'portal-chat-widget',
            headerConfig: {
                isHTML: true,
                render: _.bind(this._getChatHeader, this)
            },
            footerConfig: {
                isHTML: true,
                render: _.bind(this._getChatFooter, this)
            }
        });

        this.delayedInstantiateChat();
    },

    /**
     * It will initialize the chat if possible. If initialization is not possible due to missing setup,
     * it will run the setup steps first.
     */
    initializeChat: function() {
        if (this.isConfigured()) {
            if (!this.libraryLoaded) {
                this.loadDependencies();
            } else {
                this.createChatWidget();
            }
        }
    },

    /**
     * It will create a new instance of the chat. Each new instance will make a call to an agent,
     * so it is recommended to not instantiate the chat multiple times.
     */
    instantiateChat: function() {
        connect.ChatInterface.initiateChat({
            region: this.region,
            instanceId: this.instanceId,
            contactFlowId: this.contactFlowId,
            apiGatewayEndpoint: this.gatewayUrl,
            name: app.user.get('full_name'),
            username: app.user.get('portal_name'),
            contactAttributes: JSON.stringify({
                sugarContactName: app.user.get('full_name'),
                sugarContactId: app.user.get('id'),
                sugarContactEmail: app.user.get('primary_email_address'),
                sugarAccountId: app.user.get('primary_account_id')
            })
        }, _.bind(this.successHandler, this), this.failureHandler);

        // During 2nd and subsequent chats, there is a brief window between when
        // initiateChat returns and when the actual chat window is displayed.
        // During this time, amazon displays their loading spinner so we hide ours.
        this.$el.find(this.loadingClass).hide();
    },

    /**
     * This util delays instantiating the chat long enough for our drawer to open
     * so that the opening animation isn't blocked by `connect.ChatInterface.initiateChat`
     * awaiting an API return.
     */
    delayedInstantiateChat: function() {
        _.delay(
            _.bind(this.instantiateChat, this), this.chatAnimationTiming
        );
    },

    /**
     * Handler for when the chat could not be initialised. Shows the corresponding message.
     */
    failureHandler: function() {
        app.alert.show('portal-chat-timeout', {
            level: 'warning',
            messages: 'ERROR_OMNICHANNEL_TIMEOUT'
        });
    },

    /**
     * Replaced an uppercase system message with a more friendly version (System Message).
     *
     * @param {Object} item A chat message.
     * @return {Object} The message with the sender name changed.
     */
    itemDecorator: function(item) {
        if (['SYSTEM_MESSAGE', 'BOT'].indexOf(item.displayName) !== -1) {
            item.displayName = 'System Message';
        }
        return item;
    },

    /**
     * Handler for when the chat has been initialised. It will replace the standard system message to a more
     * user friendly variant (camelcase). It will set up a handler for when the chat ends, the wrapper would
     * be hidden.
     *
     * @param {Object} chatSession The chat session object.
     */
    successHandler: function(chatSession) {
        this.chatSession = chatSession;
        chatSession.incomingItemDecorator = this.itemDecorator;
        chatSession.onChatDisconnected(_.bind(function() {
            this.clearChat();
            this.chatSession = null;
        }, this));
        $('#end-portal-chat').click(_.bind(this._endChat, this));
    },

    /**
     * Util to hide chat and clean up components
     */
    clearChat: function() {
        this.hideChat();
        // The amazon provided chat widget does not remove all rendered elements
        // on endChat, so we do it here.
        this.$el.find('.portal-chat-footer').remove();
    },

    /**
     * End chat session
     *
     * @private
     */
    _endChat: function() {
        app.alert.show('stop_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_PORTAL_CHAT_CONFIRMATION_CLOSE'),
            onConfirm: _.bind(function() {
                this.clearChat();
                this.chatSession.endChat();
            }, this)
        });
    },

    /**
     * Checks if the chat settings are configured and allow opening a chat session. If not, a message will be shown.
     *
     * @return {boolean} True if the settings are configured.
     */
    isConfigured: function() {
        this.region = app.config.awsConnectRegion;
        this.gatewayUrl = app.config.awsConnectApiGatewayUrl;
        this.instanceId = app.config.awsConnectInstanceId;
        this.contactFlowId = app.config.awsConnectContactFlowId;

        var isConfigured = !_.isEmpty(this.region) && !_.isEmpty(this.gatewayUrl) &&
            !_.isEmpty(this.instanceId) && !_.isEmpty(this.contactFlowId);
        if (!isConfigured) {
            app.alert.show('portalchat-not-configured', {
                level: 'warning',
                messages: 'ERROR_OMNICHANNEL_NOT_CONFIGURED'
            });
        }
        return isConfigured;
    },

    /**
     * It toggles the control button and displays the chat.
     */
    showChat: function() {
        this.isExpanded = true;

        if (this.chatSession && this.chatSession.contactStatus !== 'connected') {
            this.$el.find(this.loadingClass).css('display', 'flex');
        }

        this.$el.show().addClass('expanded');
        this.controlButton.toggleButtonState(this.controlButton.BUTTON_STATE_OPEN);
    },

    /**
     * It toggles the control button and hides the chat.
     */
    hideChat: function() {
        this.isExpanded = false;
        this.$el.removeClass('expanded');
        this.controlButton.toggleButtonState(this.controlButton.BUTTON_STATE_CLOSED);
    },

    /**
     * Get the HTML to render as the chat header.
     *
     * @return {string} Compiled HBS template with contents for the chat header
     * @private
     */
    _getChatHeader: function() {
        var headerOptions = this._getHeaderOptions();
        return this.chatHeaderTemplate(headerOptions);
    },

    /**
     * Get options for compiling the chat header
     *
     * @return {Object} {
     *                      logoUrl: file url for chat logo,
     *                      title: chat title from admin config,
     *                      subtitle: chat subtitle from admin config
     *                  }
     * @private
     */
    _getHeaderOptions: function() {
        var logoUrl = app.config.awsHeaderImageUrl || this.styles.awsHeaderImageUrl;
        return {
            logoUrl: logoUrl,
            title: app.config.awsHeaderTitle,
            subtitle: app.config.awsHeaderSubtitle
        };
    },

    /**
     * Get the HTML to render as the chat footer.
     *
     * @return {string} Compiled HBS template with contents for the chat footer
     * @private
     */
    _getChatFooter: function() {
        var footerOptions = this._getFooterOptions();
        return this.chatFooterTemplate(footerOptions);
    },

    /**
     * Get options for the HB template
     *
     * @return {Object} {
     *                      title: footer chat title from admin config
     *                  }
     * @private
     */
    _getFooterOptions: function() {
        return {
            title: app.config.awsFooterTitle,
        };
    },

    /**
     * Replace default styles with styles provided by admin. If admin leaves a
     * value empty or it cannot be retrieved, use default styles.
     *
     * @return {Object}
     */
    generateChatStyles: function() {
        return _.mapObject(this.styles, function(value, key) {
            return app.config[key] || value;
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(window).off('beforeunload', this._warnOnRefresh(), this);
        this._super('_dispose');
    },

    /**
     * Display warning message of potential data loss when user attempts to manually trigger a refresh
     *
     * @return {string|null}
     * @private
     */
    _warnOnRefresh: function() {
        // Only display browser popup if we have an active session
        if (!_.isNull(this.chatSession)) {
            return app.lang.get('LBL_PORTAL_CHAT_WARN_ACTIVE_CCP_UNSAVED_CHANGES');
        }
    }
});
