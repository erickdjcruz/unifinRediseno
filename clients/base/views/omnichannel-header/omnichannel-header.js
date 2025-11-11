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
 * The layout for the Omnichannel console.
 *
 * @class View.Layouts.Base.OmnichannelHeaderView
 * @alias SUGAR.App.view.layouts.BaseOmnichannelHeaderView
 * @extends View.View
 */
({
    className: 'omni-header',

    toggleModeButtonSettings: {
        compact: {
            iconClass: 'sicon sicon-full-screen',
            tooltip: 'LBL_OMNICHANNEL_FULL_VIEW'
        },
        full: {
            iconClass: 'sicon sicon-full-screen-exit',
            tooltip: 'LBL_OMNICHANNEL_COMPACT_VIEW'
        }
    },

    /**
     * @inheritdoc
     *
     * Updates the toggle mode button when the console mode changes
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.layout.on('omniconsole:mode:set', this.updateToggleModeButton, this);
        this.layout.on('omniconsole:activeCall', this.updateShowHideToggleModeButton, this);
    },

    /**
     * Toggles the Compact/Full view button if a call is active or not
     *
     * @param isCallActive
     */
    updateShowHideToggleModeButton: function(isCallActive) {
        this.$('[data-action=toggleMode]').toggleClass('hidden', !isCallActive);
    },

    /**
     * Updates the styling of the "toggle mode" button in the Omnichannel header
     * based on the current mode that the console is in
     *
     * @param {string} currentMode the current mode of the console
     */
    updateToggleModeButton: function(currentMode) {
        // Get the button and button icon elements
        var toggleModeButton = this.$('[data-action=toggleMode]');
        var toggleModeButtonIcon = toggleModeButton.find('i');

        // If button settings are defined for the given mode, apply the correct
        // styling for the mode and show the button. Otherwise, hide it
        var modeSettings = null;
        switch (currentMode) {
            case this.layout.modes.COMPACT:
                modeSettings = this.toggleModeButtonSettings.compact;
                break;
            case this.layout.modes.FULL:
                modeSettings = this.toggleModeButtonSettings.full;
        }

        if (modeSettings && modeSettings.iconClass && modeSettings.tooltip) {
            toggleModeButtonIcon.removeClass().addClass(modeSettings.iconClass);
            toggleModeButton.attr('data-original-title', app.lang.get(modeSettings.tooltip));
            toggleModeButton.show();
        } else {
            toggleModeButton.hide();
        }
    }
})
