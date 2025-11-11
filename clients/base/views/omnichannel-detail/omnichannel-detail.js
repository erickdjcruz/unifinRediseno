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
 * The call/chat detail panel.
 *
 * @class View.Layouts.Base.OmnichannelDetailView
 * @alias SUGAR.App.view.layouts.BaseOmnichannelDetailView
 * @extends View.View
 */
({
    plugins: [
        'ErrorDecoration',
    ],

    className: 'omni-detail',

    events: {
        'click [data-action=show-tab]': 'showTab',
    },

    /**
     * Sugar Models related to active calls or chats stored by aws Contact Id
     * e.g.
     * {
     *     'aws-contact-id-1': {
     *         'Cases': { Case Bean }
     *         'Contacts': { Contact Bean }
     *     },
     *     'aws-contact-id-2': {
     *         'Cases': { Case Bean 2 }
     *         'Contacts': { Contact Bean 2 }
     *     },
     *     ...
     * }
     * @property {Object}
     */
    modelsByContactId: {},

    /**
     * Fields to be displayed in omnichannel detail panel.
     * @property [Array]
     */
    summaryFields: [
        {
            name: 'invitees',
            type: 'guest',
            links: [
                'contacts',
                'leads',
                'users',
            ],
            label: 'LBL_INVITEES',
        },
        {
            name: 'parent_name',
            type: 'parent',
            label: 'LBL_LIST_RELATED_TO',
        },
    ],

    /**
     * A list of modules whose records can be set as "Guests" on a Call
     */
    guestModuleLinks: {
        Contacts: 'contacts',
        Leads: 'leads',
        Users: 'users'
    },

    /**
     * Current AWS connect contact id.
     * @property {string}
     */
    currentContactId: null,

    /**
     * Current module for the contact
     * @property {string}
     */
    currentContactModule: 'Calls',

    /**
     * Editable information from the summary panel.
     * @property {Object}
     */
    summary: {},

    /**
     * Title of the detail block.
     * @property {string}
     */
    summaryTitle: null,

    /**
     * Boolean if fields in the detail summary view are enabled or not
     */
    areFieldsEnabled: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options.model = app.data.createBean('Calls');
        this._super('initialize', [options]);

        this.updateMetadata();
        this.currentContactId = null;
        this.context.set('model', this.model);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.layout.on('contact:view', this.showContact, this);
        this.layout.on('contact:destroyed', this.removeContact, this);
        this.layout.on('contact:model:loaded', this._setInitialSummary, this);
        this.layout.on('contact:records:matched', this._handleContactRecordsMatched, this);
        this.layout.on('omniconfig:reopen', this._updateAndRender, this);
        this.layout.on('omniconsole:record-link:clicked', this._recordLinkButtonClicked, this);
        this.layout.on('omniconsole:activeCall', this._toggleEnabledFields, this);
        this.on('render', this._resizeCCP, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        this._toggleEnabledFields(this.layout.isCallActive);
    },

    /**
     * Toggles fields to enabled or disabled/readonly mode
     *
     * @param {boolean} enableFields True if enable fields, false if disable
     */
    _toggleEnabledFields: function(enableFields) {
        // if fields are already enabled, dont enable again and cause an extra render
        if (this.areFieldsEnabled === enableFields) {
            return;
        }

        this.areFieldsEnabled = enableFields;
        if (!this.areFieldsEnabled) {
            // clear out the summary panel model when disabling fields
            this.model.clear({
                silent: true
            });
        }

        // enable/disable all fields in the summary panel
        _.each(this.fields, field => {
            if (field.$el) {
                field.setMode(enableFields ? 'edit' : 'disabled');
            }
        }, this);
    },

    /**
     * Handles when records are matched to an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @param {Array} records the array of matched records
     * @param {Object} context the context in which the records were matched
     * @private
     */
    _handleContactRecordsMatched: function(contact, records, context) {
        // Set the Guest link from the record match results
        var guest = this._determineGuestFromMatch(contact, records, context);
        if (!_.isEmpty(guest)) {
            this.setModel(contact, guest);
        }

        // Set the Related To link from the record match results
        var relatedTo = this._determineRelatedToFromMatch(contact, records, context);
        if (!_.isEmpty(relatedTo)) {
            this.setModel(contact, relatedTo);
        }
    },

    /**
     * Determines what link to make for the "Guest" when records are matched
     * to an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @param {Array} records the array of matched records
     * @param {Object} context the context in which the records were matched
     * @return {Bean|null} the Guest model to link if found; null otherwise
     * @private
     */
    _determineGuestFromMatch(contact, records, context) {
        var guest = null;

        if (contact.isInbound()) {
            // If we have a sugarContactId, link its Contact
            guest = context && context.sugarContactId && _.find(records, function(record) {
                return record.module === 'Contacts' && record.get('id') === context.sugarContactId;
            }, this);
        } else {
            // Use the record the user dialed
            if (context && context.dialedRecord) {
                var module = context.dialedRecord.module || context.dialedRecord.get('_module');
                var id = context.dialedRecord.get('id');
                guest = _.find(records, function(record) {
                    return record.get('id') === id && record.module === module;
                }, this);
            }
        }

        // Only link the record as Guest if it is a valid Guest module
        guest = this._isValidGuest(guest) ? guest : null;

        return guest;
    },

    /**
     * Determines what link to make for the "Related To" field when records are
     * matched to an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @param {Array} records the array of matched records
     * @param {Object} context the context in which the records were matched
     * @return {Bean|null} the Related To model to link if found; null otherwise
     * @private
     */
    _determineRelatedToFromMatch(contact, records, context) {
        var relatedTo = null;

        if (contact.isInbound()) {
            // If we have a sugarCaseNumber, link its Case
            relatedTo = context && context.sugarCaseNumber && _.find(records, function(record) {
                return record.module === 'Cases' && record.get('case_number') === context.sugarCaseNumber;
            }, this);
        } else {
            // If the user dialed a Guest record, or there was no dialed record,
            // use the focused record. Otherwise, use the dialed record
            relatedTo = context && context.dialedRecord && !this._isValidGuest(context.dialedRecord) ?
                context.dialedRecord : context.focusedRecord;
        }

        // Only link the record as Related To if it is explicitly a valid
        // Related To module
        relatedTo = this._isValidRelatedTo(contact, relatedTo) && !this._isValidGuest(relatedTo) ? relatedTo : null;

        return relatedTo;
    },

    /**
     * Handles when an Omnichannel link button is clicked on a record view or record view dashlet
     *
     * @param {Bean} model the model of the record
     * @private
     */
    _recordLinkButtonClicked: function(model) {
        // Link the model to the Call or Message
        this.setModel(null, model);
    },

    /**
     * Link a record to the Call/Message details
     * @param contact
     * @param model
     */
    _linkRecord: function(contact, model) {
        contact = contact || this._getActiveContact();
        if (!_.isEmpty(contact) && this._canLinkRecord(contact, model)) {
            this._updateContactModelValuesFromModel(contact, model);
            this._setModelByContactId(contact, model);
            this._notifyContactChanged(contact);
        }
    },

    /**
     * set or remove appropriate record to the contactID inorder to keep the link record icon updated
     * @private
     */
    _guestFieldChanged: function() {
        var model = _.first(this.model.get('invitees').models);
        var contact = this._getActiveContact();
        if (model) {
            this._setModelByContactId(null, model);
        } else {
            this._removePreviousLinkedGuest();
        }
        this._notifyContactChanged(contact);
    },

    /**
     * Determines whether the given model is linkable to the Call/Message
     *
     * @param contact current contact
     * @param model the model to check
     * @return {bool} true if the model is linkable; false otherwise
     * @private
     */
    _canLinkRecord: function(contact, model) {
        return this._isValidGuest(model) || this._isValidRelatedTo(contact, model);
    },

    /**
     * Retrieves the active AWS contact from the Amazon CCP
     *
     * @return {Object} the active contact's connect-streams Contact object
     * @private
     */
    _getActiveContact: function() {
        var ccp = this.layout.getComponent('omnichannel-ccp');
        return ccp.getActiveContact();
    },

    /**
     * Retrieves the Call or Message model created for an AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @return {Bean|null} the Call or Message model, or null if the contact
     *                      doesn't have one
     * @private
     */
    _getModelForContact: function(contact) {
        var contactModel = null;

        if (!_.isEmpty(contact)) {
            var ccp = this.layout.getComponent('omnichannel-ccp');
            contactModel = ccp.connectionRecords[contact.getContactId()] || null;
        }

        return contactModel;
    },

    /**
     * Links the given model to the Call/Message that is associated with the
     * given AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @param {Bean} model the model to link to the contact's Call/Message
     * @private
     */
    _updateContactModelValuesFromModel: function(contact, model) {
        // Set either the "Guests" or "Relates To" values.
        if (this._isValidGuest(model)) {
            guestField = this.getField('invitees');
            if (guestField) {
                guestField.setValue(model.attributes);
            }
        } else if (this._isValidRelatedTo(contact, model)) {
            this._removePreviousLinkedRelatedRecord(contact);
            this._setRelatedToFieldFromModel(contact, model);
        }
    },

    /**
     * Updates modelsByContactId with the given model for the given AWS contact
     *
     * @param {Object} contact connect-streams Contact object
     * @param {Bean} model the model to set
     * @private
     */
    _setModelByContactId: function(contact, model) {
        let awsId = this._getContactId(contact);
        let module = !_.isEmpty(model) ? model.module || model.get('_module') : null;
        if (_.isUndefined(this.modelsByContactId[awsId])) {
            this.modelsByContactId[awsId] = {};
        }
        this.modelsByContactId[awsId][module] = model;
    },

    /**
     * Notifies the layout that the contact's Call/Message record has changed
     *
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _notifyContactChanged: function(contact) {
        var contactModel = this._getModelForContact(contact);
        app.events.trigger('omniconsole:contact:changed', contact, contactModel);
    },

    /**
     * Returns whether a contact is a call or chat session
     *
     * @param {Object} contact (optional) the AWS contact; if not provided, uses
     *                  the current active contact
     * @return {boolean} true if the contact is a call or chat
     * @private
     */
    _isCall(contact) {
        var ccp = this.layout.getComponent('omnichannel-ccp');
        contact = contact || ccp.getActiveContact();
        return ccp.isCall(contact);
    },

    /**
     * Returns whether the given model is a valid one to link via "Guests"
     *
     * @param model
     * @return {bool} true if the model can be linked as a "Guest"
     * @private
     */
    _isValidGuest(model) {
        if (_.isEmpty(model)) {
            return false;
        }
        var module = model.module || model.get('_module');
        return _.contains(_.keys(this.guestModuleLinks), module);
    },

    /**
     * Returns whether the given model is a valid one to link via "Related To"
     *
     * @param contact
     * @param model
     * @return {boolean} true if the model can be linked as a "Related To"
     * @private
     */
    _isValidRelatedTo(contact, model) {
        if (_.isEmpty(model)) {
            return false;
        }
        var modelModule = model.module || model.get('_module');

        let contactModuleMetadata = app.metadata.getModule(this._getModuleForContact(contact));
        let linkableModules = app.lang.getAppListKeys(contactModuleMetadata.fields.parent_name.options);
        return linkableModules.includes(modelModule);
    },

    /**
     * Gets the module used for the given contact
     * @param contact
     * @return {string}
     * @private
     */
    _getModuleForContact: function(contact) {
        return contact.getType() === 'voice' ? 'Calls' : 'Messages';
    },

    /**
     * Sets the "Guests" field of this model to add the given model
     *
     * @param contact
     * @param {Bean} model the model of the Contact or Lead record to set
     * @private
     */
    _setGuestFieldFromModel: function(contact, model) {
        var contactModel = this._getModelForContact(contact);
        if (!_.isEmpty(model) && !_.isEmpty(contactModel)) {
            var module = model.module || model.get('_module');
            var link = this._getGuestModuleLink(module);

            // Set the model to add to the guests
            var guestCollectionUpdate = {
                add: [model.attributes]
            };

            // Set the model to update the guest collection on the next save
            contactModel.set(link, guestCollectionUpdate);
        }
    },

    /**
     * Removes the previously linked guest, checking all guest links
     * @private
     */
    _removePreviousLinkedGuest: function() {
        let awsId = this._getContactId();
        let self = this;
        _.each(_.keys(this.guestModuleLinks), function(guestModule) {
            if (!self.modelsByContactId[awsId][guestModule]) {
                return;
            }
            delete self.modelsByContactId[awsId][guestModule];
        });
    },

    /**
     * Remove the previously linked related record from the stored models
     * @param contact
     * @private
     */
    _removePreviousLinkedRelatedRecord: function(contact) {
        var contactModel = this._getModelForContact(contact);
        let awsId = this._getContactId(contact);
        if (!this.modelsByContactId || !this.modelsByContactId[awsId] || !contactModel ||
            _.isEmpty(contactModel.get('parent_id'))) {
            return;
        }

        let parentModule = contactModel.get('parent_type');
        delete this.modelsByContactId[awsId][parentModule];
    },

    /**
     * Gets the name for the guest link for the given module
     * @param module
     * @return link
     * @private
     */
    _getGuestModuleLink: function(module) {
        let link = this.guestModuleLinks[module];

        // Message links are not named the same as Call links
        if (!this._isCall()) {
            link = 'invitee_' + link;
        }

        return link;
    },

    /**
     * Sets the "Related To" of this model to point to the given model
     *
     * @param {Bean} model the model of the "Related To" record to set
     * @private
     */
    _setRelatedToFieldFromModel: function(contact, model) {
        var contactModel = this._getModelForContact(contact);
        if (!_.isEmpty(model) && !_.isEmpty(contactModel)) {
            contactModel.set({
                parent_type: model.module || model.get('_module'),
                parent_id: model.get('id'),
                parent: model,
                parent_name: model.get('name')
            });
        }
    },

    /**
     * Show tab in active dashboard matching record user clicks in
     * omnichannel detail panel
     * @param {Event} event click event
     */
    showTab: function(event) {
        var module = event.target.getAttribute('data-module');
        var model = this.getModel(null, module);
        var dashboardSwitch = this.layout.getComponent('omnichannel-dashboard-switch');
        dashboardSwitch.setModel(this.currentContactId, model);
    },

    /**
     * Set title of the detail panel.
     * @param {Object} contact AWS contact
     */
    setSummaryTitle: function(contact) {
        var isChat = contact.getType() === connect.ContactType.CHAT;
        var lbl = isChat ? 'LBL_OMNICHANNEL_CHAT_SUMMARY' : 'LBL_OMNICHANNEL_CALL_SUMMARY';
        this.summaryTitle = app.lang.get(lbl, this.module);
    },

    /**
     * Set data of the active contact to model.
     * @param {Object} contact AWS contact
     */
    setSummary: function(contact) {
        this.setSummaryTitle(contact);
        var ccp = this.layout.getComponent('omnichannel-ccp');
        var model = ccp.connectionRecords[contact.getContactId()];
        if (model) {
            this.model = model;
        } else {
            if (this._isCall()) {
                this.model = app.data.createBean('Calls');
            } else {
                this.model = app.data.createBean('Messages');
            }
        }
    },

    /**
     * Save the summary data.
     */
    saveSummary: function() {
        var ccp = this.layout.getComponent('omnichannel-ccp');
        ccp._updateConnectionRecord(ccp.activeContact, {});
    },

    /**
     * Set the initial summary after the contact's Call/Message model is created
     *
     * @param {Object} contact connect-streams Contact object
     * @private
     */
    _setInitialSummary: function(contact) {
        var ccp = this.layout.getComponent('omnichannel-ccp');
        var model = ccp.connectionRecords[contact.getContactId()];

        if (model) {
            this.updateMetadata(contact);
            this.model = model;
            this.model.on('change', this.saveSummary, this);
            this.model.on('change:invitees', this._guestFieldChanged, this);
            this.model.on('change:parent_id', () => {
                this._notifyContactChanged(this._getActiveContact());
            }, this);
            this.render();
            this._resizeCCP();
        }
    },

    /**
     * Show/hide the detail panel
     *
     * @deprecated Since 11.1, this is no longer used
     */
    toggle: function() {
        this.$el.toggle();
    },

    /**
     * Show contact and case records for a different AWS contact.
     * @param {Object} contact AWS contact
     */
    showContact: function(contact) {
        if (!_.isEmpty(contact)) {
            this.updateMetadata(contact);
            this.setSummary(contact);
        }

        var contactId = this._getContactId(contact);

        this.currentContactId = contactId;
        this.render();
        this._resizeCCP();
    },

    /**
     * Remove linked records for an AWS contact.
     * @param {string} contactId The id of a contact.
     */
    removeContact: function(contactId) {
        this.modelsByContactId = _.omit(this.modelsByContactId, contactId);
    },

    /**
     * Set contact model.
     * @param {Object} contact AWS contact
     * @param {Bean} contactModel Sugar contact
     * @deprecated Since 11.1, use setModel() instead
     */
    setContactModel: function(contact, contactModel) {
        this.setModel(contact, contactModel);
    },

    /**
     * Set case model.
     * @param {Object} contact AWS contact
     * @param {Bean} caseModel Sugar case
     * @deprecated Since 11.1, use setModel() instead
     */
    setCaseModel: function(contact, caseModel) {
        this.setModel(contact, caseModel);
    },

    /**
     * Set a related model for display in the omnichannnel detail panel
     * @param {Object} contact connect-streams Contact object
     * @param {Bean} model the related model to set
     */
    setModel: function(contact, model) {
        contact = contact || this._getActiveContact();
        this._linkRecord(contact, model);
        if (this._getContactId(contact) === this.currentContactId) {
            this.showContact(contact);
        }
    },

    /**
     * Get all linked models for a given contact
     *
     * @param {Object} contact AWS contact
     * @return {Object} The set of models for the given contact
     */
    getModels: function(contact) {
        var contactId = this._getContactId(contact);
        return this.modelsByContactId[contactId] || {};
    },

    /**
     * Get the linked model for a given module for a given contact
     * @param {Object} contact AWS contact
     * @param {string} module module of model to return
     * @return {Bean|undefined} The model if it exists; undefined otherwise
     */
    getModel: function(contact, module) {
        return this.getModels(contact)[module];
    },

    /**
     * Util to get the ID of a connect-streams contact. If given an empty value,
     * return the ID of the currently active contact.
     *
     * @param {Object|null} contact AWS-streams contact
     * @return {string} ID of provided or current contact
     * @private
     */
    _getContactId: function(contact) {
        return !_.isEmpty(contact) ? contact.getContactId() : this.currentContactId;
    },

    /**
     * Get contact model.
     * @param {Object} contact AWS contact
     * @return {Bean} contactModel Sugar contact
     * @deprecated Since 11.1, use getModel() instead
     */
    getContactModel: function(contact) {
        return this.getModel(contact, 'Contacts');
    },

    /**
     * Get case model.
     * @param {Object} contact AWS contact
     * @return {Bean} caseModel Sugar case
     * @deprecated Since 11.1, use getModel() instead
     */
    getCaseModel: function(contact) {
        return this.getModel(contact, 'Cases');
    },

    /**
     * Updates view metadata to use appropriate module-specific custom metadata.
     * This is called when setting initial summary, and when viewing a new
     * contact.
     *
     * @param {Object} contact AWS Contact
     */
    updateMetadata: function(contact) {
        var ccp = this.layout.getComponent('omnichannel-ccp');
        if (!_.isUndefined(ccp) && !_.isUndefined(contact)) {
            this.currentContactModule = ccp.contactTypeModule[contact.getType()];
        }
        this.meta = app.metadata.getView(this.currentContactModule, this.name);
        this.model.module = this.currentContactModule;
    },

    /**
     * Private util function to update metadata and rerender the view.
     * @private
     */
    _updateAndRender: function() {
        this.updateMetadata();
        this.render();
    },

    /**
     * Resizes CCP after re-render as our height might have changed.
     * @private
     */
    _resizeCCP: function() {
        var ccp = this.layout.getComponent('omnichannel-ccp');
        ccp.resize();
    },

    _dispose: function() {
        this.layout.off('contact:view', this.showContact, this);
        this.layout.off('contact:destroyed', this.removeContact, this);
        this.layout.off('contact:model:loaded', this._setInitialSummary, this);
        this.layout.off('omniconfig:reopen', this._updateAndRender, this);
        this.off('render', this._resizeCCP, this);
        this._super('_dispose');
    }
})
