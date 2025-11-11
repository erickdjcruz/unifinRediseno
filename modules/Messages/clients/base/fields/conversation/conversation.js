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
 * @class View.Fields.Base.Messages.ConversationField
 * @alias SUGAR.App.view.fields.BaseMessagesConversationField
 * @extends View.Fields.BaseField
 */
({
    /**
     * Event listeners
     */
    events: {
        'click .more-btn': 'paginate',
    },

    /**
     * List of parsed messages
     */
    messagesList: [],

    /**
     * Current page of pagination
     */
    page: 1,

    /**
     * Default settings used when none are supplied through metadata
     */
    _defaultSettings: {
        max_display_messages: 'all',
        pagination: false,
        per_page: 10,
    },

    /**
     * Paginate messages
     */
    paginate: function() {
        this.page++;
        this.render();
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.page = 1;
        this.messagesList = [];
        this._initSettings();
        this.platform = app.config.platform;
    },

    /**
     * Initialize settings, default settings are used when none are supplied
     * through metadata.
     *
     * @return {View.Fields.BaseTextareaField} Instance of this field.
     * @protected
     */
    _initSettings: function() {
        this._settings = _.extend({},
            this._defaultSettings,
            this.def && this.def.settings || {}
        );
    },

    /**
     * Parse messages from the Conversation field
     *
     * @param {string} transcript
     * @return {Array} parsed messages
     */
    parseMessages: function(transcript) {
        if (this.messagesList.length) {
            return;
        }

        var allowedAuthors = ['CUSTOMER', 'AGENT'];
        var allAuthors = _.union(allowedAuthors, ['SYSTEM']);
        var allAuthorsStr = allAuthors.join('|');

        if (transcript) {
            var reg = new RegExp(`\\[(${allAuthorsStr}) [a-zA-Z0-9_\\s]+\\] \\d{2}:\\d{2}`, 'i');
            var result = transcript.split(reg);
            var maxShow = this._settings.max_display_messages;

            var author = '';
            _.each(result, _.bind(function(item) {
                var value = item.trim();

                if (allAuthors.indexOf(value) >= 0) {
                    author = value;
                }

                if (allowedAuthors.indexOf(author) >= 0 && value !== author &&
                    (maxShow === 'all' || this.messagesList.length < parseInt(maxShow))) {
                    this.messagesList.push({
                        author: author,
                        message: value,
                    });
                }
            }, this));
        }
    },

    /**
     * @inheritdoc
     *
     * @param {string} value The value to format
     * @return {Array} formatted value
     */
    format: function(value) {
        var messageBox = {};
        var messages = [];
        var showCount = this.page * this._settings.per_page;

        this.parseMessages(value);

        this.moreBtn = this._settings.pagination && this.messagesList.length > showCount;

        _.each(this.messagesList, _.bind(function(item, key) {
            if (this._settings.pagination && key >= showCount) {
                return;
            }

            if (item.author === messageBox.author) {
                messageBox.messagesList.push(item.message);
            } else {
                messageBox = {
                    author: item.author,
                    messagesList: [
                        item.message,
                    ],
                };
                messages.push(messageBox);
            }
        }, this));

        return messages;
    }
})
