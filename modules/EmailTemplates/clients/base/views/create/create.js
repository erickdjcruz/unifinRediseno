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
 * @class View.Views.Base.EmailTemplatesCreatedView
 * @alias SUGAR.App.view.views.BaseEmailTemplatesCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * Add the EmailTemplates plugin
     * @param {Object} options
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins, ['EmailTemplates']);
        this._super('initialize', [options]);
    },

    /**
     * Call _toggleAttachmentsVisibility on render since Attachments should only
     * be visible if the field has a value
     *
     * @private
     */
    _render: function() {
        this._super('_render');
        this._toggleAttachmentsVisibility();
    }
})
