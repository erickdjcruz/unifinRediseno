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
        this._helpModal = false;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createHelpModal();
    },

    /**
     * Updates the body of the popup
     *
     * @param {string} popupInfo
     */
    setPopupInfo: function(popupInfo) {
        this._helpModal.setPopupInfo(popupInfo);
    },

    /**
     * Show Help modal
     */
    showHelpModal: function() {
        this._helpModal._helpObjectCreated = true;
        this._helpModal.toggle();
    },

    /**
     * Creates the help modal
     */
    _createHelpModal: function() {
        this._disposeHelpModal();

        const helpButton = this.$('[data-modal="popup-help"]');
        const def = this.options.def;

        this._helpModal = app.view.createLayout({
            type: 'popup-help',
            button: helpButton,
            popupTitle: def.popupTitle,
            popupBody: def.popupBody,
        });

        this._helpModal.initComponents();

        this.listenTo(this._helpModal, 'show hide', function(view, active) {
            helpButton.toggleClass('active', active);
        });
    },

    /**
     * Dispose the help layout
     */
    _disposeHelpModal: function() {
        if (this._helpModal) {
            this._helpModal.dispose();
            this._helpModal = false;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeHelpModal();

        this._super('_dispose');
    },
})
