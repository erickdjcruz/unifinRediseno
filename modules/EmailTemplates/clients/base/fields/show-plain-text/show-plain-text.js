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
 * @class View.Fields.Base.EmailTemplates.ShowPlaintextField
 * @alias SUGAR.App.view.fields.BaseEmailTemplatesShowPlaintextField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    events: {
        'click [name="plaintext"]': 'buttonClicked'
    },

    plainTextField: 'body',

    plainTextExpanded: false,

    /**
     * Bind event listeners
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.model.on('change:text_only', this.resetState, this);
    },

    /**
     * If the user checks "text only" we toggle the editor, so this button will
     * be hidden. We want to reset it so when it is shown again it has the proper
     * text.
     */
    resetState: function() {
        if ($('input[type=checkbox]').prop('checked')) {
            this.toggleExpandPlainText(false);
        } else {
            this.toggleExpandPlainText(!this.plainTextExpanded);
        }
        this.render();
    },

    /**
     * The body and body_html are toggled based on `text_only` using SugarLogic
     * Dependencies. Using SugarLogic's SetVisibilityAction here ensures we toggle
     * them in the same way as the checkbox to avoid conflicts.
     */
    buttonClicked: function() {
        this.plainTextExpanded = !this.plainTextExpanded;
        this.toggleExpandPlainText(!this.plainTextExpanded);
        this.render();
    },

    /**
     * Toggles the expanded plain text field styling based on button press
     * @param {boolean} toggle value to determine whether its collapsed (true) or expanded (false)
     */
    toggleExpandPlainText: function(toggle) {
        $('textarea[name=body]').parent().toggleClass('collapsed-plain-text', toggle);
    },

    /**
     * Remove event listeners
     * @private
     */
    _dispose: function() {
        this.model.off('change:text_only', this.resetState, this);
        this._super('_dispose');
    }
})
