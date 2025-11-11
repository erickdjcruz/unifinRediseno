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
 * Show Help Popup action.
 *
 * This allows an user to open a help pupup
 *
 * @class View.Fields.Base.ShowHelpButtonField
 * @alias SUGAR.App.view.fields.BaseShowHelpButtonField
 * @extends View.Fields.Base.BaseField
 */
 ({
    events: {
        'click .popup-help': 'showHelpModal',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this._popupInfo = '';

        if (_.has(this, 'layout') && this.layout && _.has(this.layout, 'options') && this.layout.options) {
            this._popupInfo = this.layout.options.popupBody;
        }
    },

    /**
     * Updates the body of the popup
     *
     * @param {string} popupInfo
     */
    setPopupInfo: function(popupInfo) {
        this._popupInfo = popupInfo;

        this.render();
    },
})
