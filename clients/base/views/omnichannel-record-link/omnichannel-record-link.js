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
 * @class View.Views.Base.OmnichannelRecordLinkView
 * @alias SUGAR.App.view.views.BaseOmnichannelRecordLinkView
 * @extends View.View
 */
({
    className: 'omni-record-link',

    events: {
        'click .btn-sugarlive-link.unlinked': 'handleRecordLink'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setOptions(options);
    },

    /**
     * Set options.
     * @param {Object} options
     */
    setOptions: function(options) {
        options = options || {};

        this.model = options.model || this.model || {};
        this.tooltip = options.tooltip || '';
        this.className = options.className || 'unlinked';
        this.icon = this.className === 'linked' ? 'sicon-check' : 'sicon-link';
    },

    /**
     * Link the record when the button is clicked
     */
    handleRecordLink: function() {
        if (app.omniConsole && this.model && this.model.get('id')) {
            app.omniConsole.trigger('omniconsole:record-link:clicked', this.model);

            this.setOptions({
                tooltip: app.lang.get('LBL_OMNICHANNEL_LINKED'),
                className: 'linked'
            });
            this.render();
        }
    }
})
