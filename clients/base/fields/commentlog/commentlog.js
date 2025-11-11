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
 * @class View.Fields.Base.CommentLogField
 * @alias SUGAR.App.view.fields.BaseCommentLogField
 * @extends View.Fields.Base.TextareaField
 */
({
    extendsFrom: 'TextareaField',

    fieldTag: 'textarea',

    plugins: ['Taggable'],

    /**
     * @inheritdoc
     */
    events: {
        'click [data-action=toggle]': 'toggleCollapsedEntry',
        'click [data-action=showall]': 'showAll',
        'click [data-action=save]': 'save',
    },

    /**
     * Object to keep track of what comment entries are collapsed
     */
    collapsedEntries: undefined,

    /**
     * Defaults
     */
    _defaultSettings: {
        max_display_chars: 500,
    },

    /**
     * Called when initializing the field
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.collapsedEntries = {};
        this._initSettings();
        this.setUpTaggable();
    },

    /**
     * Set model info to to properly check acl access for mentions
     */
    setUpTaggable: function() {
        var module;
        var id;
        if (this.def.dashlet) {
            module = this.view.collection.link.bean.module;
            id = this.view.collection.link.bean.id;
        } else {
            module = this.context.get('module');
            id = this.context.get('modelId');
        }
        this.setTaggableRecord(module, id);
    },

    /**
     * Initialize settings, default settings are used when none are supplied
     * through metadata.
     *
     * @return {View.Fields.BaseCommentlogField} Instance of this field.
     * @protected
     */
    _initSettings: function() {
        var configSettings = {
            max_display_chars: app.config.commentlog.maxchars,
        };
        this._settings = _.extend({}, this._defaultSettings, configSettings);
        return this;
    },

    /**
     * Get the correct collection depending if this is a collection field
     * or a dashlet collection
     *
     * @return {Data.BeanCollection}
     */
    getCollection: function() {
        if (this.def.dashlet) {
            return this.view.collection;
        } else {
            return this.model.get(this.name);
        }
    },

    /**
     * Called when rendering the field
     * @private
     */
    _render: function() {
        this.showCommentLog();
        this._super('_render'); // everything showing in the UI should be done before this line.
    },

    /**
     * Called when formatting the value for display
     * @param value
     */
    format: function(value) {
        return value;
    },

    /**
     * Builds model for handlebar to show pass commentlog messages in record view.
     * This should only be called when there is need to render past messages, only
     * when this.getFormattedValue() returns the data format for message.
     */
    showCommentLog: function() {
        var collection = this.getCollection();
        if (!collection) {
            return;
        }

        // Set if we should show the View All button on the dashlet view
        this._showViewAll = collection.dataFetched && collection.next_offset !== -1;

        var comments = collection.models;

        if (comments) {
            this.msgs = [];
            // add readable time and user link to users
            _.each(comments, function(commentModel) {
                var id = commentModel.get('id');
                if (_.isUndefined(this.collapsedEntries[id])) {
                    this.collapsedEntries[id] = true;
                }

                var entry = this._escapeValue(commentModel.get('entry'));
                var entryShort = this.getShortComment(entry);
                var showShort = entry !== entryShort;

                entry = this.insertHtmlLinks(entry);
                entryShort = this.insertHtmlLinks(entryShort);

                entry = this.formatTags(entry);
                entryShort = this.formatTags(entryShort);

                var msg = {
                    id: commentModel.get('id'),
                    entry: new Handlebars.SafeString(entry),
                    entryShort: new Handlebars.SafeString(entryShort),
                    created_by_name: commentModel.get('created_by_name'),
                    collapsed: this.collapsedEntries[id],
                    showShort: showShort,
                    date_entered: commentModel.get('date_entered'),
                };

                // to date display format
                var enteredDate = app.date(commentModel.get('date_entered'));
                if (enteredDate.isValid()) {
                    msg.entered_date = enteredDate.formatUser();
                }

                var link = commentModel.get('created_by_link');
                if (link && link.id) {
                    if (app.acl.hasAccess('view', 'Employees', {acls: link._acl})) {
                        msg.href = '#' + app.router.buildRoute('Employees', link.id, 'detail');
                    }
                } else if (commentModel.has('created_by')) {
                    msg.href = '#' + app.router.buildRoute('Employees', commentModel.get('created_by'), 'detail');
                }

                if (commentModel === this._newEntryModel) {
                    msg.isNew = true;
                }
                this.msgs.push(msg);
            }, this);
        }

        this.newValue = this._newEntryModel ? this._newEntryModel.get('entry') : '';
    },

    /**
     * Escapes any dangerous values from the string
     *
     * @param {string} comment The comment entry
     * @return {string} The escaped string
     * @private
     */
    _escapeValue: function(comment) {
        return Handlebars.Utils.escapeExpression(comment);
    },

    /**
     * Save the id in this.collapsedEntries to keep track of what entries are shortened on view or not
     * @param event
     */
    toggleCollapsedEntry: function(event) {
        var id = $(event.currentTarget).data('commentId');
        let msg = _.findWhere(this.msgs, {id: id});
        if (msg) {
            // Build a new comment element from the comment template
            this.collapsedEntries[id] = !this.collapsedEntries[id];
            msg.collapsed = !msg.collapsed;
            let commentTpl = app.template.getField(this.type, 'comment');
            let newCommentEl = commentTpl(msg);

            // Replace the existing comment element with the new one. Adjust
            // the scrollbar as needed so the change is less jarring for the user
            let scrollContainer = this.$el.find('.scroll-container');
            let scrollFromTop = scrollContainer.scrollTop();
            this.$el.find(`.msg[data-id=${id}]`).replaceWith(newCommentEl);
            if (!msg.collapsed) {
                scrollContainer.scrollTop(scrollFromTop);
            }
        }
    },

    /**
     * Load all comments into the dashlet
     */
    showAll: function() {
        this.showAllClicked = true;
        this.view.loadData({loadAll: true});
    },

    /**
     * Called when unformatting the value for storage
     * @param value
     */
    unformat: function(value) {
        return value;
    },

    /**
     * Save a new comment on the dashlet
     */
    save: function() {
        if (this.view._mode === 'preview') {
            return;
        }
        var value = this.getCurrentCommentText();
        if (_.isEmpty(value)) {
            return;
        }
        var commentBean = app.data.createRelatedBean(this.model, null, 'commentlog_link', {entry: value});
        var success = _.bind(function() {
            this.isSaving = false;
            this.setCurrentCommentText('');
            this.view.loadData({loadAll: !!this.showAllClicked});
        }, this);
        this.isSaving = true;
        commentBean.sync('create', commentBean, {success: success, relate: true});
    },

    /**
     * Get the current comment text.
     *
     * @return {string} The current comment text.
     */
    getCurrentCommentText: function() {
        var el = this.getTextArea();
        return this.unformat(el.val());
    },

    /**
     * Set the current comment text.
     *
     * @param {string} text The desired comment text.
     */
    setCurrentCommentText: function(text) {
        var el = this.getTextArea();
        el.val(text);
    },

    /**
     * Get the comment log textarea
     *
     * @return {jQuery} The textarea element
     */
    getTextArea: function() {
        return this.$el.find(this.fieldTag);
    },

    /**
     * Commentlog needs to check if it has any messages in the collection
     * @override
     */
    isFieldEmpty: function() {
        return this.getCollection().length === 0;
    },

    /**
     * @inheritdoc
     */
    bindDomChange: function() {
        if (this.def.dashlet) {
            return;
        }
        if (!(this.model instanceof Backbone.Model)) {
            return;
        }

        var el = this.getTextArea();
        var self = this;

        el.on('change', function() {
            var value = self.unformat(el.val());

            if (!self._newEntryModel) {
                var collectionField = self.model.get('commentlog');

                if (!collectionField) {
                    self.model.set(self.name, []);
                    collectionField = self.model.get('commentlog');
                }

                self._newEntryModel = app.data.createRelatedBean(self.model, null, 'commentlog_link', {
                    entry: value,
                    _link: 'commentlog_link',
                });

                collectionField.add(self._newEntryModel, {silent: true});
            }
            self._newEntryModel.set('entry', value);
        });
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.model) {
            var collectionField = this.model.get(this.name);
            if (collectionField) {
                this.listenTo(collectionField, 'reset', function() {
                    this.newValue = this._newEntryModel = null;
                });
            }
            this.model.on('change:' + this.name, function(model, value) {
                if (this.action !== 'edit') {
                    this.newValue = this._newEntryModel = null;
                }
                this.render();
            }, this);
        }

        if (this.def.dashlet) {
            var collection = this.getCollection();
            if (collection) {
                collection.on('sync', function() {
                    if (this.disposed) {
                        return;
                    }
                    this.render();
                }, this);
            }
        }
    }
})
