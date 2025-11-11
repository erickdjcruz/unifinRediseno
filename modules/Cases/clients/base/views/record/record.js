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
({
    extendsFrom: 'RecordView',

    contactsSubpanel: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['HistoricalSummary', 'KBContent']);
        this._super('initialize', [options]);

        this._bindEvents();
    },

    /**
     * Initiates listening to application events.
     */
    _bindEvents: function() {
        this.context.on('context:child:add', this.addChildHandler, this);
    },

    /**
     * Bind events on Contacts subpanel
     */
    addChildHandler: function(childModel) {
        if (childModel.get('link') === 'contacts') {
            this.contactsSubpanel = childModel;

            this.contactsSubpanel.on('reload', _.bind(function() {
                this.context.reloadData();
            }, this));
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.context.off('context:child:add', this.addChildHandler, this);

        this._super('_dispose');
    },
})
