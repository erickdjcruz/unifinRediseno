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
 * PortalPreviewMegamenu contains the components that make up the preview megamenu
 * on the portal config page
 *
 * @class View.Layouts.Base.AdministrationPortalThemeMegaMenu
 * @alias SUGAR.App.view.layouts.PortalThemeMegaMenu
 * @extends View.Layouts.Base.HeaderLayout
 */
({
    extendsFrom: 'HeaderLayout',

    /**
     * @inheritdoc
     * @override
     * Add button with portal help text to header-help component
     * @param components
     * @param options
     * @param context
     */
    initComponents: function(components, options, context) {
        this._super('initComponents', [components, options, context]);
        var helpComponent = this.getComponent('header-help');
        // overwrite header help buttons with our portal header help button
        if (_.isObject(helpComponent) && _.isObject(helpComponent.meta)) {
            helpComponent.meta.buttons = this._getPortalHelpButton();
        }
    },

    /**
     * Create metadata for the portal preview megamenu button
     * with the correct label
     *
     * @return {Array} An array with a single button's metadata for portal megamenu
     * @private
     */
    _getPortalHelpButton: function() {
        return [{
            type: 'button',
            name: 'help_button',
            css_class: 'btn-primary',
            label: this._getHelpButtonLabel(),
            events: {
                click: 'button:help_button:click'
            }
        }];
    },

    /**
     * Get the appropriate label string for the megamenu help button. Defaults
     * to 'New Case'
     *
     * @return {string} Translated label for portal megamenu button
     * @private
     */
    _getHelpButtonLabel: function() {
        return app.lang.get(
            'LBL_PORTALTHEME_NEW_CASE_BUTTON_TEXT_DEFAULT',
            'Administration'
        );
    }
})
