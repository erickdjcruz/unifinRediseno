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
 * @class View.Views.Portal.ContactInfoView
 * @alias SUGAR.App.view.views.PortalContactInfoView
 * @extends View.View
 */
({
    /**
     * Stores the Portal contact information configured by the admin
     */
    contactInfo: null,

    /**
     * @inheritdoc
     *
     * Grabs the Portal contact information to be displayed when the page loads
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.contactInfo = app.config.contactInfo;
        if (_.isString(this.contactInfo.contactURL) && !this.contactInfo.contactURL.match(/^(http|https):\/\//)) {
            this.contactInfo.contactURL = app.config.siteUrl + this.contactInfo.contactURL.trim();
        }
    },

    /**
     * Gets the logo image for portal
     * @return string containing the logo image URL
     */
    getLogoImage: function() {
        return app.config.logoURL || app.config.logomarkURL || app.metadata.getLogoUrl();
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.logoUrl = this.getLogoImage();
        this._super('_renderHtml');
    },
})
