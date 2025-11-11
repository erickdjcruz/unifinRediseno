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
 * @class View.Views.Base.ActivityCardContentView
 * @alias SUGAR.App.view.views.BaseActivityCardContentView
 * @extends View.Views.Base.ActivityCardView
 */
({
    extendsFrom: 'ActivityCardView',

    className: 'activity-card-content',

    attachmentFieldNames: [
        'attachment_list',
        'attachments_collection'
    ],

    events: {
        'click .activity-card-show-more': 'expandCollapse',
        'click .activity-card-show-less': 'expandCollapse'

    },

    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.renderToggleShowMore = _.debounce(this.renderToggleShowMore, 10);
        this.initDateDetails();
    },

    /**
     * Initializes hbs date variables with date_modified
     */
    initDateDetails: function() {
        if (!this.activity) {
            return;
        }
        let dateEntered = app.date(this.activity.get('date_entered'));
        let dateModified = app.date(this.activity.get('date_modified'));

        if (dateEntered.isValid() && dateModified.isValid()) {
            dateEntered = dateEntered.formatUser();
            dateModified = dateModified.formatUser();

            if (dateEntered !== dateModified) {
                this.dateModified = dateModified;
            }
        }
    },

    /**
     * @inheritdoc
     * @private
     */
    _render: function() {
        this._super('_render');
        this.renderToggleShowMore();
    },

    /**
     * show/hide More button on render
     */
    renderToggleShowMore: function() {
        if (this.disposed) {
            return;
        }
        this.$('.activity-card-show-less').hide();
        var more = this.$('.activity-card-show-more');
        var curHeight = this.$('.activity-card-content-body').height();
        if (this.$('.panel-attachments img').length) {
            this.$('.panel-attachments img').on('load', function() {
                curHeight + this.height < 300 ? more.hide() : more.show();
            });
        } else {
            curHeight < 300 ? more.hide() : more.show();
        }
    },

    /**
     * show/hide More/Less buttons
     */
    toggleShowMore: function() {
        var curHeight = this.$('.activity-card-content-body').height();
        if (curHeight == 300) {
            this.$('.activity-card-show-more').show();
            this.$('.activity-card-show-less').hide();
        }else if (curHeight < 300) {
            this.$('.activity-card-show-more').hide();
            this.$('.activity-card-show-less').hide();
        } else {
            this.$('.activity-card-show-more').hide();
            this.$('.activity-card-show-less').show();
        }
    },

    /**
     * adds/removes class to expand/collapse cards
     * @param event
     */
    expandCollapse: function(event) {
        var shouldShowMore = $(event.currentTarget).hasClass('activity-card-show-more');
        if (shouldShowMore) {
            this.$('.activity-card-content-body').removeClass('collapsed-timeline').addClass('expanded-timeline');
        } else {
            this.$('.activity-card-content-body').removeClass('expanded-timeline').addClass('collapsed-timeline');
        }
        this.toggleShowMore();
    },

    /**
     * Adds HTML <br> tags for line breaks in the text
     * @param text the string to be formatted
     * @return {string}
     */
    formatContent: function(text) {
        return text ? _.escape(text).replace(/\n/g, '<br />') : '';
    },

    /**
     * Set the required details for the attachments to be shown in the activity card
     * This method can be used by child classes to initialize attachment fields
     *
     * @param {string} attachmentFieldName Name of the attachment field like, 'attachment_list'
     */
    initAttachmentDetails: function(attachmentFieldName) {
        if (this.activity && this.attachmentFieldNames.includes(attachmentFieldName)) {
            var cardAttachments = this.activity.get(attachmentFieldName);
            this.attachments = cardAttachments.length > 0 ? cardAttachments : null;
            if (this.attachments) {
                this.model = app.data.createBean(this.module, {id: this.activity.get('id')});
                this.model.set(attachmentFieldName, this.attachments);
            }
        }
    }
})
