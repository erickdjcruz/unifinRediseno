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
 * The ccp container of the Omnichannel console.
 *
 * @class View.Views.Base.OmnichannelCcpView
 * @alias SUGAR.App.view.views.BaseOmnichannelCcpView
 * @extends View.View
 */
({
    className: 'omni-ccp',

    /**
     * A map of contact type to module
     */
    contactTypeModule: {
        voice: 'Calls',
        chat: 'Messages',
    },

    /**
     * Default maximum number of results to be returned by search query
     */
    maxQueryResultsDefault: 5,

    /**
     * The list of source types
     */
    sourceType: {
        voice: 'Phone',
        chat: 'Chat',
    },

    /**
     * The active contact
     */
    activeContact: null,

    /**
     * Call/chat records
     */
    connectionRecords: {},

    /**
     * The list of connected contacts
     */
    connectedContacts: {},

    /**
     * Chat controllers, keyed by contact ID
     */
    chatControllers: {},

    /**
     * Transcripts of chat messages, keyed by contact ID
     */
    chatTranscripts: {},

    /**
     * Is the ccp loaded?
     */
    ccpLoaded: false,

    /**
     * Have we loaded the CCP library?
     */
    libraryLoaded: false,

    /**
     * Is agent logged in?
     */
    agentLoggedIn: false,

    /**
     * Default CCP settings. Will be overridden by admin settings in the future
     */
    defaultCCPOptions: {
        loginPopupAutoClose: true,
        softphone: {
            allowFramedSoftphone: true
        }
    },

    /**
     * Prefix for AWS connect instance URLs
     */
    urlPrefix: 'https://',

    /**
     * Suffix for AWS connect instance URLs
     */
    urlSuffix: '.awsapps.com/connect/ccp-v2/',

    /**
     * A list of fields that might be updated through API from other sources (eg. lambda functions).
     */
    multiSourceFields: ['call_recording_url', 'contact_id'],

    /**
     * A list of modules that will be searched for matching records when a
     * contact starts
     */
    searchModules: ['Contacts', 'Accounts', 'Leads', 'Cases'],

    /**
     * Stores contextual information about AWS contacts by AWS contact ID
     */
    recordMatchContexts: {},

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        // Load the CCP when console drawer opens
        this.layout.on('omniconsole:open', function() {
            this.loadCCP();
            this.resize();
        }, this);

        // Event listener for when the mode of the console changes
        this.layout.on('omniconsole:mode:set', this.resize, this);

        // Event listener for manual refreshes
        $(window).on('beforeunload', _.bind(this._warnOnRefresh, this));

        // resize the CCP on window resize
        var debouncedResize = _.bind(_.debounce(this.resize, 100), this);
        $(window).on('resize.' + this.cid, debouncedResize);
    },

    /**
     * Change the height of CCP when the console (and detail panel) toggles.
     */
    resize: function() {
        if (!this.disposed) {
            this.$el.css('top', this._determineTop());
        }
    },

    /**
     * Calculate the top of CPP.
     * @return {number}
     * @private
     */
    _determineTop: function() {
        // Start by getting the top of the detail panel
        var detailPanel = this.layout.getComponent('omnichannel-detail');

        return parseInt(detailPanel.$el.css('top'), 10) + parseInt(detailPanel.$el.css('height'), 10) + 1;
    },

    /**
     * Load the CCP library if needed, then initialize the CCP. Show an alert
     * message if loading the CCP fails. We expect it to fail in IE and Safari,
     * as the CCP itself is not compatible with those browsers.
     */
    loadCCP: function() {
        if (!this._loadAdminConfig()) {
            this._showNonConfiguredWarning();
            return;
        }
        if (this.libraryLoaded) {
            this.initializeCCP();
            return;
        }
        try {
            var self = this;
            // Load the connect-streams library and initialize the CCP
            $.getScript('include/javascript/amazon-connect/amazon-connect-streams.js', function() {
                // Load chat library here, must be loaded after connect-streams
                $.getScript('include/javascript/amazon-connect/amazon-connect-chat.js', function() {
                    self.libraryLoaded = true;
                    self.initializeCCP();
                    self.initializeChat();
                });
            });

        } catch (error) {
            app.alert.show(error.name, {
                level: 'error',
                messages: 'ERROR_OMNICHANNEL_LOAD_FAILED'
            });
            App.logger.error('Loading connect-streams library failed: ' + error);
        }
    },

    /**
     * Initialize library with options defined above, and load event listeners
     * for different CCP objects.
     */
    initializeCCP: function() {
        if (!this.ccpLoaded) {
            connect.core.initCCP(_.first(this.$('#containerDiv')), this.defaultCCPOptions);
            this.loadAgentEventListeners();
            this.loadContactEventListeners();
            this.loadGeneralEventListeners();
            this.ccpLoaded = true;
        } else if (!this.agentLoggedIn) {
            if (connect.core.loginWindow == null || connect.core.loginWindow.closed) {
                connect.core.loginWindow = window.open(this.defaultCCPOptions.ccpUrl, connect.MasterTopics.LOGIN_POPUP);
            } else {
                connect.core.loginWindow.focus();
            }
        }
    },

    /**
     * Provide initial chat config for use with amazon-connect-chatjs library
     */
    initializeChat: function() {
        var globalConfig = {
            region: this.defaultCCPOptions.region
        };
        connect.ChatSession.setGlobalConfig(globalConfig);
    },

    /**
     * Tear down the CCP instance when an agent logs out. We have to terminate
     * the instance via the Amazon library, and completely remove the iFrame from
     * the DOM so we can load a new one when the drawer is re-opened.
     */
    tearDownCCP: function() {
        this.styleFooterButton('logged-out');
        connect.core.terminate();
        this.$el.find('#containerDiv').empty();
        this.ccpLoaded = false;
        this.agentLoggedIn = false;
    },

    /**
     * Load agent event listeners.
     */
    loadAgentEventListeners: function() {
        var self = this;
        connect.agent(function(agent) {
            // When CCP agent is authenticated, we set the footer style
            self.styleFooterButton('logged-in');
            self.agentLoggedIn = true;

            // Trigger global changes so all parts of the app can go into
            // "CCP mode"
            app.events.trigger('ccp:initiated');

            agent.onStateChange(function(agentStateChange) {
                let isOffline = agentStateChange.newState.toLowerCase() === connect.AgentStateType.OFFLINE;
                let omnichannelConfigButton = $('#omnichannel_sidebar-nav-item .sidebar-nav-item-kebab');
                let omnichannelDisabledButton = $('#omnichannel_sidebar-nav-item .sidebar-nav-item-kebab-disabled');
                if (isOffline) {
                    self._toggleDisplay(omnichannelDisabledButton, false);
                    self._toggleDisplay(omnichannelConfigButton, true);
                } else {
                    self._toggleDisplay(omnichannelDisabledButton, true);
                    self._toggleDisplay(omnichannelConfigButton, false);
                }
            });
        });
    },

    /**
     * Toggle show/hide on the element
     *
     * @param {Event} element to show/hide
     * @param {boolean} display True to show and False to hide
     * @private
     */
    _toggleDisplay: function(element, display) {
        if (display) {
            element.removeClass('hide');
            element.addClass('show');
        } else {
            element.removeClass('show');
            element.addClass('hide');
        }
    },

    /**
     * Gets the active contacts.
     * @return {contacts} Active contacts
     */
    getContacts: function() {
        return new connect.Agent().getContacts();
    },

    /**
     * Get the contact id for the active contact
     *
     * @return {string} the contact id or empty string if no active contact
     */
    getActiveContactId: function() {
        if (this.activeContact) {
            return this.activeContact.getContactId();
        }
        return '';
    },

    /**
     * Get active contact
     *
     * @return {Object} active contact
     */
    getActiveContact: function() {
        return this.activeContact;
    },

    /**
     * Gets the model for the current active contact
     * @return {Object} active model
     */
    getActiveModel: function() {
        return this.activeContact ? this.connectionRecords[this.activeContact.getContactId()] : null;
    },

    /**
     * Load contact event listeners.
     */
    loadContactEventListeners: function() {
        var self = this;

        connect.core.onViewContact(function(event) {
            if (self.connectedContacts[event.contactId]) {
                self._setActiveContact(event.contactId);

                app.events.trigger(
                    'omniconsole:contact:changed',
                    this.activeContact || null,
                    this.activeContact ? this.connectionRecords[this.activeContact.getContactId()] : null
                );
            }
        });

        connect.contact(function(contact) {
            var connection = contact.getAgentConnection();
            if (connection.getMediaType() === connect.MediaType.CHAT) {
                self.loadChatListeners(connection);
            }

            // Listener for when a connection is attempted (i.e. when the CCP
            // starts ringing)
            contact.onConnecting(function(contact) {
                if (app.omniConsole.isConfigPaneExpanded) {
                    connection.destroy();
                    app.alert.show('finish_configuring', {
                        level: 'warning',
                        messages: app.lang.get('LBL_OMNICHANNEL_FINISH_CONFIGURING_BEFORE_OUTBOUND_CALL'),
                    });
                } else {
                    // Create context information about the contact
                    self._createRecordMatchContext(contact);

                    self.layout.open();
                }
            });

            // Listener for when a connection is made (i.e. the call/chat is
            // answered)
            contact.onConnected(function(contact) {
                self.styleFooterButton('active-session');
                self.addContactToContactsList(contact);
                self._setActiveContact(contact.contactId);
                self._createConnectionRecord(contact);
            });

            // this listener is subscribed to both call and chat end (ENDED) event
            contact.onEnded(function(contact) {
                if (this.eventName.includes(connect.ContactEvents.ENDED)) {
                    // if the call/chat has ended but the contact is not closed
                    self._handleConnectionEnd(contact);
                }

                // Clear any context information created for the contact
                self._deleteRecordMatchContext(contact);
            });
        });
    },

    /**
     * Creates and stores an object that contains contextual information about
     * record matching on an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _createRecordMatchContext: function(contact) {
        var context = {};
        var contactAttrs = contact.getAttributes();

        if (this.isChat(contact)) {
            // The Case number from Portal chat
            if (contactAttrs.sugarCaseNumber && contactAttrs.sugarCaseNumber.value) {
                context.sugarCaseNumber = parseInt(contactAttrs.sugarCaseNumber.value);
            }

            // The Contact ID from Portal chat
            if (contactAttrs.sugarContactId && contactAttrs.sugarContactId.value) {
                context.sugarContactId = contactAttrs.sugarContactId.value;
            }

            // The Contact email from inbound chat
            if (contactAttrs.sugarContactEmail && contactAttrs.sugarContactEmail.value) {
                context.sugarContactEmail = contactAttrs.sugarContactEmail.value;
            }

            // The Contact name from inbound chat
            if (contactAttrs.sugarContactName && contactAttrs.sugarContactName.value) {
                context.sugarContactName = contactAttrs.sugarContactName.value;
            }
        } else if (this.isCall(contact)) {
            if (!contact.isInbound()) {
                // The record the user clicked a phone number on
                context.dialedRecord = this._determineDialedRecord();

                // The record the user was focusing on when the call was initiated
                context.focusedRecord = this._determineFocusedRecord();
            }

            // The phone number of the contact
            context.phoneNumber = this._determineGlobalSearchPhoneNumber(contact);
        }

        this.recordMatchContexts[contact.getContactId()] = context;
    },

    /**
     * Removes any stored contextual information about an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _deleteRecordMatchContext: function(contact) {
        delete this.recordMatchContexts[contact.getContactId()];
    },

    /**
     * Retrieves any stored contextual information about an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @return {Object|null} the context info if it exists; null otherwise
     * @private
     */
    _getRecordMatchContext: function(contact) {
        return this.recordMatchContexts[contact.getContactId()] || null;
    },

    /**
     * Determines what phone number to use for global search record matching by
     * phone number
     *
     * @param {Object} contact connect-streams Contact object
     * @return {Object} the global search term and module list array
     * @private
     */
    _determineGlobalSearchPhoneNumber: function(contact) {
        // Get the endpoint for the contact
        var connection = contact.getInitialConnection();
        var endpoint = connection.getEndpoint();

        // Get the phone number of the connection. Amazon converts all phone
        // numbers to E164 format, which can cause phone number matches to miss
        // if the numbers aren't stored that way in Sugar. Remove the first 4
        // characters of the number (the '+', and 3 digits for the maximum
        // length of the country code). This isn't perfect, but it will have to
        // do unless we can add a new phone number parsing rule to ElasticSearch
        return !_.isEmpty(endpoint.phoneNumber) ? endpoint.phoneNumber.substring(4) : '';
    },

    /**
     * Determines what record the user dialed by clicking a phone number field
     *
     * @return {Bean|null} the dialed record if it exists; null otherwise
     * @private
     */
    _determineDialedRecord: function() {
        var dialedRecord;

        if (!_.isEmpty(this.layout.context.get('lastDialedRecord'))) {
            dialedRecord = this.layout.context.get('lastDialedRecord');
            this.layout.context.unset('lastDialedRecord');
        }

        return dialedRecord || null;
    },

    /**
     * Determines what record the user is currently focusing on
     *
     * @return {Bean|null} the focused record if it exists; null otherwise
     * @private
     */
    _determineFocusedRecord: function() {
        var focusedRecord;

        if (app.sideDrawer && app.sideDrawer.isOpen()) {
            // The focused model the user is looking at in the side drawer
            var rowModelDataLayout = app.sideDrawer.getComponent('row-model-data');
            focusedRecord = rowModelDataLayout && rowModelDataLayout.getRowModel();
        } else if (app.controller.context && app.controller.context.get('layout') === 'record') {
            // The model of the record view the user is looking at
            focusedRecord = app.controller.context.get('model');
        }

        return focusedRecord || null;
    },

    /**
     * Attempts to find Sugar records associated with the given contact
     *
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _matchRecords: function(contact) {
        // Combine all record fetches/searches into one API call to improve
        // performance. When the search is complete, notify the layout with the
        // list of matched models for the contact
        var url = app.api.buildURL(null, 'bulk');
        var params = {
            requests: this._buildBulkRecordMatchingRequests(contact)
        };
        var callbacks = {
            success: _.bind(function(results) {
                // Notify the layout of the records that were matched, and the
                // context in which they were matched
                var models = this._formatRecordMatchResults(results);
                var context = this._getRecordMatchContext(contact);
                var moduleList = this._getSearchModules();
                this.layout.trigger('contact:records:matched', contact, models, context, moduleList);
            }, this)
        };

        // Initiate the bulk API call
        app.api.call('create', url, params, callbacks);
    },

    /**
     * Combines results of multiple searches returned from the bulk API to
     * produce a list of records to associate with a contact
     *
     * @param {Array} results the array of responses from the bulk API
     * @return {Array} the list of
     * @private
     */
    _formatRecordMatchResults: function(results) {
        // The bulk API returns returns as an array of response data. Iterate
        // through each response and process it individually
        var models = [];
        _.each(results, function(result) {
            models = _.union(models, this._formatRecordMatchResult(result));
        }, this);

        // Remove duplicate records from the results, in case multiple searches
        // find the same record
        models = _.uniq(models, function(model) {
            return model.id || model.get('id');
        });

        // Since we are combining results of multiple searches, and matching at
        // most one record per module, we need to remove duplicate modules from
        // the results. This will discard the lower-priority results in favor
        // of keeping the higher-priority ones
        return _.uniq(models, function(model) {
            return model.module || model.get('_module');
        });
    },

    /**
     * Parses the result of a single record search to extract any records that
     * are identified as lone matches for their module
     *
     * @param result
     * @private
     */
    _formatRecordMatchResult: function(result) {
        var models = [];

        // Iterate through the results. For any result such that it is the only
        // record found for its module, create a bean of it and add it to the
        // models to return
        if (result.contents && result.contents.records) {
            var records = result.contents.records;
            _.each(this._getSearchModules(), function(module) {
                var moduleResults = _.filter(records, function(record) {
                    return record._module === module;
                });

                if (moduleResults.length === 1) {
                    var model = app.data.createBean(module, moduleResults[0]);
                    models.push(model);
                }
            }, this);
        }

        return models;
    },

    /**
     * Builds a list of requests to pass in to the bulk API based on the type of
     * contact
     *
     * @param {Object} contact connect-streams Contact object
     * @return {Array} the list of requests, in order of search priority
     * @private
     */
    _buildBulkRecordMatchingRequests: function(contact) {
        // Build the list of requests specific to the direction of the call
        var requests;
        if (contact.isInbound()) {
            requests = this._buildInboundRecordMatchingRequests(contact);
        } else {
            requests = this._buildOutboundRecordMatchingRequests(contact);
        }

        // For calls, include the results of searching by phone number
        if (this.isCall(contact)) {
            var context = this._getRecordMatchContext(contact);
            requests.push(this._buildGlobalSearchBulkRequest(context.phoneNumber));
        }

        return requests;
    },

    /**
     * Builds a list of requests to pass in to the bulk API that are specific
     * to inbound contacts
     *
     * @param {Object} contact connect-streams Contact object
     * @return {Array} the list of requests, in order of search priority
     * @private
     */
    _buildInboundRecordMatchingRequests: function(contact) {
        var requests = [];
        var context = this._getRecordMatchContext(contact);

        if (!context) {
            return requests;
        }

        // If we have a Sugar Case Number, find its matching Case record
        if (context.sugarCaseNumber) {
            requests.push(this._buildRecordFetchBulkRequest('Cases', {
                case_number: context.sugarCaseNumber
            }));
        }

        // If we have a Sugar Contact ID, find its matching Contact record
        if (context.sugarContactId) {
            requests.push(this._buildRecordFetchBulkRequest('Contacts', {
                id: context.sugarContactId
            }));
        }

        // If we have an email address for the contact, search the Contacts module for the record with that email
        if (context.sugarContactEmail) {
            requests.push(this._buildRecordFetchBulkRequest('Contacts', {
                email: context.sugarContactEmail
            }));
        }

        // If we have a name for the contact, search the Contacts module for the record with that name
        if (context.sugarContactName) {
            const nameComponents = context.sugarContactName.split(' ');
            if (nameComponents.length >= 2) {
                // We don't know the format of the name as it comes from Amazon.
                // Assuming the first string is first name and the last string is last name.
                requests.push(this._buildRecordFetchBulkRequest('Contacts', {
                    first_name: nameComponents[0],
                    last_name: nameComponents[nameComponents.length - 1]
                }));
            }
        }

        return requests;
    },

    /**
     * Builds a list of requests to pass in to the bulk API that are specific
     * to outbound contacts
     *
     * @param {Object} contact connect-streams Contact object
     * @return {Array} the list of requests, in order of search priority
     * @private
     */
    _buildOutboundRecordMatchingRequests: function(contact) {
        var requests = [];
        var context = this._getRecordMatchContext(contact);

        // If we dialed out from a specific model, fetch the latest version of it
        if (context.dialedRecord) {
            var module = context.dialedRecord.module || context.dialedRecord.get('_module');
            requests.push(this._buildRecordFetchBulkRequest(module, {
                id: context.dialedRecord.get('id')
            }));
        }
        return requests;
    },

    /**
     * Given a module and filters, builds filter API request data to pass in to
     * the bulk API
     *
     * @param {string} module the module to filter
     * @param {Object} fieldFilters a mapping of {field name} => {value}, used
     *                 for exact matching on field values
     * @return {Object} the filter API request parameters to pass in to the bulk
     *                  API
     * @private
     */
    _buildRecordFetchBulkRequest(module, fieldFilters) {
        var filter = [];

        // If we have field filters defined, build them now
        _.each(fieldFilters, function(fieldValue, fieldName) {
            var fieldFilter = {};
            fieldFilter[fieldName] = {
                $equals: fieldValue
            };
            filter.push(fieldFilter);
        }, this);

        // Construct and return the parameters for a call to the filter API
        var url = app.api.buildURL(module + '/filter');
        return {
            url: url.substr(4),
            method: 'GET',
            data: {
                filter: filter
            }
        };
    },

    /**
     * Given a search term, builds data for a global search request data across the
     * SugarLive modules to pass into the bulk API
     *
     * @param {string} term the term to search
     * @private
     */
    _buildGlobalSearchBulkRequest: function(term) {
        // Construct and return the parameters for a call to the global search
        // API
        var url = app.api.buildURL('globalsearch');
        var data = {
            q: term || '',
            module_list: this._getSearchModules().join(',')
        };
        return {
            url: url.substr(4),
            method: 'GET',
            data: data
        };
    },

    /**
     * Gets the list of modules to perform record matching across
     *
     * @return {Array} the list of modules, excluding any modules without access
     * @private
     */
    _getSearchModules: function() {
        return _.filter(this.searchModules, function(module) {
            return !_.isEmpty(app.metadata.getModule(module)) && app.acl.hasAccess('view', module);
        });
    },

    /**
     * Update call/chat record when a call/chat is ended.
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _handleConnectionEnd: function(contact) {
        // do nothing if connection record doesn't exist
        if (!this._hasConnectionRecord(contact)) {
            return;
        }

        var data = {};
        var startTime = this.getContactConnectedTime(contact);
        var timeDuration = this.getTimeAndDuration(startTime);
        data.date_end = timeDuration.nowTime;

        if (this.isCall(contact)) {
            data.status = 'Held';
            data.duration_hours = timeDuration.durationHours;
            data.duration_minutes = timeDuration.durationMinutes;
        } else {
            data.status = 'Completed';
            data.conversation = this._getTranscriptForContact(contact);
        }

        this._updateConnectionRecord(contact, data);
    },

    /**
     * Get relevant contact information based on contact type.
     *
     * @param contact
     * @return {Object}
     */
    getContactInfo: function(contact) {
        if (this.isCall(contact)) {
            return this.getVoiceContactInfo(contact);
        } else if (this.isChat(contact)) {
            return this.getChatContactInfo(contact);
        }
    },

    /**
     * Load general event listeners. If the connect-streams API exposes an
     * object.onEvent function, we should prefer that method of event listening.
     * The EventBus should only be used for low-level events that aren't exposed
     * via the agent, contact, etc. object APIs.
     */
    loadGeneralEventListeners: function() {
        var self = this;
        var eventBus = connect.core.getEventBus();
        // This event is fired when an agent logs out, or the connection is lost
        eventBus.subscribe(connect.EventType.TERMINATED, function() {
            self.tearDownCCP();
            self.layout.trigger('ccp:terminated');

            // trigger global events to take app out of "CCP mode"
            app.events.trigger('ccp:terminated');
        });
        // This event is fired if we cannot synchronize with the CCP server
        eventBus.subscribe(connect.EventType.ACK_TIMEOUT, function() {
            if (self.agentLoggedIn) {
                self._showConnectionWarning();
            }
        });
        // This event is triggered when 'Clear Contact' button is clicked
        eventBus.subscribe(connect.ContactEvents.DESTROYED, function(contact) {
            self._closeConnectionRecord(contact);
            if (_.isEmpty(self.getContacts())) {
                // no more active contacts
                self.styleFooterButton('logged-in');

                // empty the active contact
                self._unsetActiveContact();
            }
            self.removeStoredContactData(contact);
            self.layout.trigger('contact:destroyed', contact.getContactId());
        });
    },

    /**
     * Util to trigger the footer style update
     *
     * @param status
     */
    styleFooterButton: function(status) {
        this.layout.context.trigger('omnichannel:auth', status);
    },

    /**
     * Warn users if their admin hasn't added Amazon Connect settings
     * @private
     */
    _showNonConfiguredWarning: function() {
        app.alert.show('omnichannel-not-configured', {
            level: 'warning',
            messages: 'ERROR_OMNICHANNEL_NOT_CONFIGURED'
        });
    },

    /**
     * Warn users if the attempt to contact their Connect instance timed out
     * @private
     */
    _showConnectionWarning: function() {
        app.alert.show('omnichannel-timeout', {
            level: 'warning',
            messages: 'ERROR_OMNICHANNEL_TIMEOUT'
        });
    },

    /**
     * Load admin configuration for AWS Connect. Return true if successful, else
     * false.
     *
     * @return {boolean} whether or not config was loaded
     * @private
     */
    _loadAdminConfig: function() {
        var instanceName = App.config.awsConnectInstanceName;
        var region = App.config.awsConnectRegion;
        var instanceUrl = App.config.awsConnectUrl;
        var identityProvider = App.config.awsConnectIdentityProvider;
        var loginSSO = App.config.awsLoginUrl;
        if (_.isEmpty(instanceName) || _.isEmpty(region)) {
            return false;
        }

        if (_.isEmpty(instanceUrl)) {
            this.defaultCCPOptions.ccpUrl = this.urlPrefix + instanceName + this.urlSuffix;
        } else {
            this.defaultCCPOptions.ccpUrl = instanceUrl;
        }
        if (!_.isUndefined(identityProvider) && identityProvider === 'SAML') {
            this.defaultCCPOptions.loginUrl = loginSSO;
        }
        this.defaultCCPOptions.region = region;
        return true;
    },

    /**
     * Caches the last viewed contact
     *
     * @param {string} id
     * @private
     */
    _setActiveContact: function(id) {
        this.activeContact = _.findWhere(this.getContacts(), {contactId: id});
        this.layout.trigger('contact:view', this.activeContact);
    },

    /**
     * Unset the active contact and other relevant data
     *
     * @private
     */
    _unsetActiveContact: function() {
        this.activeContact = null;
        app.events.trigger('omniconsole:contact:changed', null, null);
    },

    /**
     * Add the contact to the list of connected contacts
     *
     * @param contact
     */
    addContactToContactsList: function(contact) {
        this.connectedContacts[contact.getContactId()] = {
            connectedTimestamp: contact.getStatus().timestamp,
        };
    },

    /**
     * Remove the contact from the list of connected contacts, if it exists
     *
     * @param contact
     */
    removeStoredContactData: function(contact) {
        var contactId = contact.getContactId();

        if (_.has(this.connectedContacts, contactId)) {
            this.connectedContacts = _.omit(this.connectedContacts, contactId);
        }

        if (_.has(this.chatControllers, contactId)) {
            this.chatControllers = _.omit(this.chatControllers, contactId);
        }

        if (_.has(this.chatTranscripts, contactId)) {
            this.chatTranscripts = _.omit(this.chatTranscripts, contactId);
        }

        if (_.has(this.connectionRecords, contactId)) {
            this.connectionRecords = _.omit(this.connectionRecords, contactId);
        }
    },

    /**
     * Get generic contact info that all contact types should have
     *
     * @param contact
     * @return {Object}
     */
    getGenericContactInfo: function(contact) {
        var data = {};

        try {
            data.isContactInbound = contact.isInbound();
        } catch (err) {
            app.logger.error('Amazon Connect: Unable to determine contact inbound/outbound direction');
        }

        data.contactType = contact.getType();
        data.startTime = this.getContactConnectedTime(contact);
        data.aws_contact_id = contact.contactId;

        return data;
    },

    /**
     * Get the relevant information for a voice type contact
     *
     * @param contact
     * @return {Object}
     */
    getVoiceContactInfo: function(contact) {
        var conn = contact.getInitialConnection();
        var endpoint = conn.getEndpoint();

        return {
            phone_work: endpoint.phoneNumber,
            source: this.sourceType.voice
        };
    },

    /**
     * Get the relevant information for a chat type contact
     *
     * @param contact
     * @return {Object}
     */
    getChatContactInfo: function(contact) {
        var lastName = '';
        var data = contact._getData();

        var connectionInfo = _.findWhere(data.connections, {type: 'inbound'});
        if (connectionInfo) {
            lastName = connectionInfo.chatMediaInfo.customerName;
        }

        return {
            last_name: lastName,
            name: (lastName) ? lastName : app.lang.get('LBL_OMNICHANNEL_DEFAULT_CUSTOMER_NAME'),
            source: this.sourceType.chat,
        };
    },

    /**
     * Get the Utils/Date from the contact's timestamp
     *
     * @param contact
     * @return {Date}
     */
    getContactConnectedTime: function(contact) {
        var timestamp = this.connectedContacts[contact.getContactId()].connectedTimestamp;

        return app.date(timestamp);
    },

    /**
     * Get a readable title per the contact type
     *
     * @param module
     * @param data
     * @param contact
     * @return {string}
     */
    getRecordTitle: function(module, data, contact) {
        var title = '';

        // if unfamiliar type, return empty
        if (!(this.isChat(contact) || this.isCall(contact))) {
            return title;
        }

        if (this.isCall(contact)) {
            var contactTypeStr = 'Call';
            var identifier = data.phone_work;
        } else {
            var contactTypeStr = 'Chat';
            var identifier = data.name;
        }
        var direction = _.has(data, 'isContactInbound') ? (data.isContactInbound ? 'from' : 'to') : 'from';

        title = app.lang.get('TPL_OMNICHANNEL_NEW_RECORD_TITLE', module, {
            type: contactTypeStr,
            direction: direction,
            identifier: identifier,
            time: data.startTime.formatUser()
        });

        return title;
    },

    /**
     * Get the time in server format and calculate the duration
     *
     * @param {Date} startTime
     * @return {Object}
     */
    getTimeAndDuration: function(startTime) {
        var nowTime = app.date();

        var timeDiff = nowTime.diff(startTime);
        var durationHours = Math.floor(app.date.duration(timeDiff).asHours());
        var durationMinutes = app.date.duration(timeDiff).minutes();

        return {
            startTime: startTime.formatServer(),
            nowTime: nowTime.formatServer(),
            durationHours: durationHours,
            durationMinutes: durationMinutes,
        };
    },

    /**
     * Create a call/chat record for a new contact.
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _createConnectionRecord: function(contact) {
        // do nothing if contact type is unfamiliar
        if (!_.has(this.contactTypeModule, contact.getType())) {
            app.logger.error('Amazon Connect: Contact type: ' + contact.getType() + ' is not voice or chat');
            return;
        }

        var module = this.contactTypeModule[contact.getType()];
        var contactId = contact.getContactId();

        // do nothing if contact was from a previous session
        if (!_.has(this.connectedContacts, contactId)) {
            return;
        }

        var searchCallback = _.bind(function(results) {
            var model;

            // do not create a connection record if the unique contact id is already associated with a record
            if (_.isArray(results.records) && results.records.length > 0) {
                model = app.data.createBean(module, _.first(results.records));
                this._handlePostConnectionRecordCreation(model, contact);
            } else {
                var data = _.extendOwn(
                    this.getContactInfo(contact),
                    this.getGenericContactInfo(contact)
                );
                model = this.getNewModelForContact(module, data, contact);
                model.save({}, {
                    silent: true,
                    showAlerts: false,
                    success: _.bind(this._handlePostConnectionRecordCreation, this, model, contact),
                    error: function() {
                        app.logger.error('Failed to create call/chat record for ' + contactId);
                    }
                });
            }
        }, this);

        // before creating the connection record, ensure that the contact id is not
        // already associated with a record
        this._searchRecordByContactId(module, contactId, searchCallback);
    },

    /**
     * Handle actions after the connection record has been created or fetched
     *
     * @param model the model created or fetched
     * @param contact the contact
     * @private
     */
    _handlePostConnectionRecordCreation: function(model, contact) {
        var contactId = contact.getContactId();

        this.connectionRecords[contactId] = model;
        this.layout.trigger('contact:model:loaded', this.activeContact);
        this._matchRecords(contact);

        app.events.trigger('omniconsole:contact:changed', contact, model);
    },

    /**
     * Check if the connection record exists
     * @param {Object} contact connect-streams Contact object
     *
     * @return {boolean} true if the Connect-stream contact object is present in connection records
     * @private
     */
    _hasConnectionRecord: function(contact) {
        return _.has(this.connectionRecords, contact.getContactId());
    },

    /**
     * Update call/chat record when a contact is closed.
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _closeConnectionRecord: function(contact) {
        // do nothing if connection record doesn't exist
        if (!this._hasConnectionRecord(contact)) {
            return;
        }

        // Save the call or chat record data for the contact
        this._updateConnectionRecord(contact, {},  true);
    },

    /**
     * Failure handler for saving a model from the CCP.
     *
     * @param {Object} contact Connect-streams Contact object.
     */
    saveModelError: function(contact) {
        this.hideSavingMessage();
        app.logger.error('Failed to update call/chat record for ' + contact.getContactId());
    },

    /**
     * Success handler for saving a model from the CCP.
     *
     * @param {Bean} model The model to be saved.
     * @param {boolean} contactClosed True if coming from the _closeConnectionRecord chain.
     */
    saveModelSuccess: function(model, contactClosed = false) {
        var context = _.extend({
            module: model.module,
            moduleSingularLower: app.lang.getModuleName(model.module).toLowerCase()
        }, model.attributes);

        if (contactClosed) {
            app.alert.show('save_success', {
                level: 'success',
                autoClose: true,
                messages: app.lang.get('LBL_OMNICHANNEL_RECORD_CREATED', model.module, context)
            });
        }
    },

    /**
     * Create the options for saving a model tied to the given contact and save the model.
     *
     * @param {Bean} model The model to be saved.
     * @param {Object} contact Connect-streams Contact object.
     * @param {boolean} contactClosed True if coming from the _closeConnectionRecord chain.
     */
    saveModel: function(model, contact, contactClosed = false) {
        if (contactClosed) {
            this.hideSavingMessage();
            app.alert.dismiss('ccp-validation');
        }

        const omniDetailView = this.layout.getComponent('omnichannel-detail');
        if (omniDetailView) {
            omniDetailView.clearValidationErrors();
        }

        // if there's no model id, don't save the model
        if (!model.id) {
            return;
        }

        model.doValidate(this.getValidationFields(model), (isValid, errors) => {
            this.saveOnValidate(model, contact, contactClosed, isValid, errors);
        });
    },

    /**
     * Save a model after validation process
     *
     * @param {Bean} model The model to be saved.
     * @param {Object} contact Connect-streams Contact object.
     * @param {boolean} contactClosed True if coming from the _closeConnectionRecord chain.
     * @param {boolean} isValid Status of validation
     * @param {Array} errors List of errors
     */
    saveOnValidate: function(model, contact, contactClosed, isValid, errors) {
        var options = {
            silent: true,
            showAlerts: false,
            error: _.bind(this.saveModelError, this, contact)
        };

        options.success = () => {
            this.showSavingMessage(true);

            if (_.contains(['Held', 'Completed'], model.get('status'))) {
                this.saveModelSuccess(model, contactClosed);
            }
        };

        if (isValid) {
            this.showSavingMessage();
            model.save(null, options);
        } else {
            this.onValidationFailed(model, options, errors, contactClosed);
        }
    },

    /**
     * Show an error message if there is an error during validation process.
     * Set previous value for invalid field if other field was saved
     *
     * @param {Bean} model Current model
     * @param {Object} options Options for the saving
     * @param {Array} errors List of errors
     * @param {boolean} contactClosed True if coming from the _closeConnectionRecord chain.
     */
    onValidationFailed: function(model, options, errors, contactClosed) {
        const changedAttrs = model.changedAttributes(model.getSynced());
        const invalidAttrs = _.keys(errors || {});
        const validAttrs = _.omit(changedAttrs, invalidAttrs);

        if (!_.isEmpty(validAttrs)) {
            // Use old valid value for an invalid field in case
            // if a data from other field was changed and it's valid (this data should be saved)
            this.showSavingMessage();

            const invA = invalidAttrs[0];
            const prevValue = model.getSynced()[invA];
            model.set(invA, prevValue);
            model.save(null, options);
        }

        if (!contactClosed) {
            app.alert.show('ccp-validation', {
                level: 'error',
                messages: app.lang.get('ERR_RESOLVE_ERRORS'),
            });
        }
    },

    /**
     * Return list of fields those should be validated
     *
     * @param {Bean} model Current model
     * @return {Array} List of fields for the validation
     */
    getValidationFields: function(model) {
        const omniDetailView = this.layout.getComponent('omnichannel-detail');

        if (!omniDetailView || !omniDetailView.meta) {
            return [];
        }

        const fields = omniDetailView.meta.fields || [];
        const keys = fields.map((v) => v.name);

        return _.pick(model.fields, keys);
    },

    /**
     * Hide saving message
     */
    hideSavingMessage: function() {
        const blockEl = $('.omni-detail-title .loading');
        blockEl.removeClass(['saving', 'saved']);
    },

    /**
     * Show saving message
     *
     * @param {boolean} saved True if saving process is completed
     */
    showSavingMessage: function(saved = false) {
        const blockEl = $('.omni-detail-title .loading');
        const textEl = blockEl.find('> span');

        blockEl.removeClass(['saving', 'saved']);
        blockEl.addClass(saved ? 'saved' : 'saving');
        textEl.html(app.lang.get(saved ? 'LBL_SAVED' : 'LBL_SAVING'));
    },

    /**
     * Re-apply the values of fields that can be changed only through other sources.
     *
     * @param {Bean} model The model tied to the active call/chat.
     * @param {Bean} dbModel The model tied to the active call/chat holding the most up to date data.
     */
    preserveDBFieldValues: function(model, dbModel) {
        //contact_id is handled separately in `updateContactIdField`.
        var fieldNames = _.without(this.multiSourceFields, 'contact_id');
        _.each(fieldNames, function(name) {
            if (dbModel.get(name)) {
                model.set(name, dbModel.get(name));
            }
        });
    },

    /**
     * Check and compare the value of the contact id field from different sources and apply the relevant one.
     *
     * @param {Bean} model The model kept on the view (and thus might be outdated).
     * @param {Bean} dbModel The same model the one kept on the view, but holding the most recent data.
     * @param {Bean} contactModel The contact module record related to the current call model.
     * @deprecated Since 11.1, this is now handled in omnichannel-detail
     */
    updateContactIdField: function(model, dbModel, contactModel) {
        var contacts = {};
        var dbContactId = dbModel.get('contact_id');
        if (contactModel) {
            if (dbContactId && dbContactId !== contactModel.get('id')) {
                contacts.delete = [dbContactId];
                contacts.add = [contactModel.attributes];
            } else if (!dbContactId) {
                contacts.add = [contactModel.attributes];
            }
            model.set('contact_id', contactModel.get('id'));
        } else {
            if (dbContactId) {
                contacts.delete = [dbContactId];
            }
            model.set('contact_id', '');
        }
        model.set('contacts', contacts);
    },

    /**
     * Given a data structure apply the values on the model.
     * In case a contact model or a case model is given, apply only specific fields.
     *
     * @param {Bean} model The model tied to the active call/chat.
     * @param {Bean} dbModel The model tied to the active call/chat
     * and holding the most up to date data.
     * @param {Object} contact Connect-streams Contact object.
     * @param {*} value A value to be applied on the model.
     * @param {string} key Field name or related model module name.
     * @deprecated Since 11.1, this is now handled in omnichannel-detail
     */
    applyChangesToModel: function(model, dbModel, contact, value, key) {
        if (key === 'Contacts') {
            if (this.isCall(contact)) {
                this.updateContactIdField(model, dbModel, value);
            } else if (value) {
                model.set('contact_id', value.get('id'));
            }
        } else if (key === 'Cases' && value) {
            model.set('parent_type', 'Cases');
            model.set('parent_id', value.get('id'));
        } else {
            model.set(key, value);
        }
    },

    /**
     * It will apply a given set of data on the model then save it.
     *
     * @param {Bean} viewModel The model tied to the active contact.
     * @param {Object} clientData A set of details to be saved on the model.
     * @param {Object} contact Connect-streams Contact object.
     * @param {boolean} contactClosed True if coming from the _closeConnectionRecord chain.
     * @param {Bean} dbModel The viewModel with the most up to date field values.
     */
    _updateFetchedRecord: function(viewModel, clientData, contact, contactClosed = false, dbModel) {
        viewModel.set(clientData);
        this.preserveDBFieldValues(viewModel, dbModel);
        this.saveModel(viewModel, contact, contactClosed);
    },

    /**
     * It will find the model tied to the given contact and fetch a copy of it.
     * The given set of data will be applied on the model only after it has been
     * re-fetched. We do this in order to avoid overriding model data saved
     * through other sources.
     *
     * @param {Object} contact Connect-streams Contact object.
     * @param {Object} data A set of details to be saved on the model.
     * @param {boolean} contactClosed True if coming from the _closeConnectionRecord chain.
     */
    _updateConnectionRecord: function(contact, data, contactClosed = false) {
        // if there's no contact, dont update the record
        if (!contact) {
            return;
        }

        var model = this.connectionRecords[contact.getContactId()];
        if (model) {
            var baseModel = app.data.createBean(model.module, {id: model.get('id')});

            baseModel.fetch({
                silent: true,
                showAlerts: false,
                fields: this.multiSourceFields,
                success: _.bind(this._updateFetchedRecord, this, model, data, contact, contactClosed)
            });
        }
    },

    /**
     * Create the model and set appropriate attributes for the contact
     *
     * @param module
     * @param data
     * @param {Object} contact connect-streams Contact object.
     * @return {Object} the model
     */
    getNewModelForContact: function(module, data, contact) {
        var model = app.data.createBean(module);

        if (_.has(data, 'isContactInbound')) {
            model.set({
                direction: data.isContactInbound ? 'Inbound' : 'Outbound',
            });
        }

        if (this.isChat(contact)) {
            model.set({
                channel_type: 'Chat',
            });
        } else {
            model.set({
                duration_hours: 0,
                duration_minutes: 0,
                users: {
                    add: [{
                        id: app.user.id,
                        _module: 'Users'
                    }]
                }
            });
        }

        model.set({
            name: this.getRecordTitle(module, data, contact),
            date_start: data.startTime.formatServer(),
            status: 'In Progress',
            assigned_user_id: app.user.id,
            aws_contact_id: data.aws_contact_id || '',
            invitees: [],
        });

        return model;
    },

    /**
     * Load event listeners specific to chat sessions
     *
     * @param {Object} connection - connect-streams Connection object
     */
    loadChatListeners: function(connection) {
        var controllerHandler = _.bind(this._handleChatMediaController, this);
        connection.getMediaController().then(controllerHandler);
    },

    /**
     * Bind any event listeners onto chat media controllers.
     *
     * @param {Object} controller - ChatSessionController from connect-streams-chatjs
     * @private
     */
    _handleChatMediaController: function(controller) {
        var contactId = controller.controller.contactId;
        this.chatControllers[contactId] = controller;
        controller.onMessage(_.bind(this._handleChatMessage, this));
    },

    /**
     * ChatSessionController.onMessage event handler. Receives the API response
     * object from when messages are sent/received. Overwrites the existing chat
     * transcript for this contact with the most up-to-date version so whenever
     * the chat is ended we can save the transcript.
     *
     * @param {Object} response - connect-streams-chatjs API response
     * @private
     */
    _handleChatMessage: function(response) {
        var controller = this.chatControllers[response.chatDetails.contactId];
        controller.getTranscript({})
            .then(_.bind(this._setChatTranscript, this))
            .catch(function(error) {
                console.log(error);
            });
        if (response.data &&
            response.data.Type === 'MESSAGE' &&
            response.data.ParticipantRole === 'CUSTOMER') {
            this.layout.trigger('omnichannel:message');
        }
    },

    /**
     * Sets a chat transcript to this object's context for reference when the
     * chat session ends
     *
     * @param {Object} transcript - connect-streams-chatjs Transcript object
     * @private
     */
    _setChatTranscript: function(transcript) {
        var currentTranscript = this.chatTranscripts[transcript.data.InitialContactId];
        this.chatTranscripts[transcript.data.InitialContactId] = _.uniq(_.union(
            currentTranscript, transcript.data.Transcript
        ), function(message) {
            return message.Id;
        });
    },

    /**
     * Get a human-readable chat transcript for this contact. This function is
     * called when chat sessions end, and the return value is set on the model
     * when the Messages create drawer opens.
     *
     * @param {Object} contact - connect-streams Contact object
     * @return {string} readableTranscript - human readable chat transcript
     * @private
     */
    _getTranscriptForContact: function(contact) {
        var readableTranscript = '';
        var transcriptJson = this.chatTranscripts[contact.contactId] ||
            this.chatTranscripts[contact.getInitialContactId()] || [];

        _.each(transcriptJson, function(message) {
            readableTranscript += this._formatChatMessage(message);
        }, this);

        return readableTranscript.trim();
    },

    /**
     * Convert a single chat message from JSON to a human-readable format
     *
     * @param {Object} message - JSON-format chat message
     * @return {string} readableMessage - single human-readable chat message
     * @private
     */
    _formatChatMessage: function(message) {
        if (_.isEmpty(message.Content)) {
            return '';
        }
        var offset = app.user.getPreference('tz_offset_sec');
        var dateTime = app.date(message.AbsoluteTime).utcOffset(offset / 60);
        var timeStamp = dateTime.format(app.date.getUserTimeFormat());
        var header = '[' + message.ParticipantRole + ' ' + message.DisplayName + ']';
        header += ' ' + timeStamp;
        return header + '\n' + message.Content + '\n\n';
    },

    /**
     * Search for a record per the specified contact id
     *
     * @param {string} module
     * @param {string} contactId
     * @param {Function} successCallback
     * @private
     */
    _searchRecordByContactId: function(module, contactId, successCallback) {
        var url = app.api.serverUrl + '/' + module + '?filter[0][aws_contact_id][$equals]=' + contactId;
        app.api.call('read', url, null, {
            success: successCallback
        });
    },

    /**
     * Display warning message of potential data loss when user attempts to manually trigger a refresh
     *
     * @return {string|null}
     * @private
     */
    _warnOnRefresh: function() {
        // Only display browser popup if we have an active session
        if (!_.isNull(this.activeContact)) {
            return app.lang.get('LBL_WARN_ACTIVE_CCP_UNSAVED_CHANGES');
        }
    },

    isChat: function(contact) {
        return contact.getType() === connect.ContactType.CHAT;
    },

    isCall: function(contact) {
        return contact.getType() === connect.ContactType.VOICE;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.layout.off('omniconsole:open', null, this);
        this.layout.off('omniconsole:mode:set', this.resize, this);
        $(window).off('beforeunload', this._warnOnRefresh(), this);
        $(window).off('resize.' + this.cid);
        this._super('_dispose');
    }
})
