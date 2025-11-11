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
 * @class View.Fields.Base.EmailTemplates.Htmleditable_tinymceField
 * @alias SUGAR.App.view.fields.BaseEmailTemplatesHtmleditable_tinymceField
 * @extends View.Fields.Base.Htmleditable_tinymceField
 */
({
    extendsFrom: 'Htmleditable_tinymceField',

    /**
     * Email Template specific parameters.
     * @private
     */
    _tinyMCEConfig: {
        'height': '430',
    },

    /**
     * @inheritdoc
     *
     * Adds buttons for uploading a local file and selecting a Sugar Document
     * to attach to the email.
     *
     * @fires email_attachments:file on the view when the user elects to attach
     * a local file.
     */
    addCustomButtons: function(editor) {
        var attachmentButtons = [];

        // Attachments can only be added if the user has permission to create
        // Notes records. Only add the attachment button(s) if the user is
        // allowed.
        if (app.acl.hasAccess('create', 'Notes')) {
            attachmentButtons.push({
                text: app.lang.get('LBL_EMAIL_ATTACHMENTS', this.module),
                type: 'menuitem',
                onAction: (event) => {
                    // Track click on the file attachment button.
                    app.analytics.trackEvent('click', 'tinymce_email_attachment_file_button', event);
                    this.view.trigger('email_attachments:file');
                },
            });

            // The user can only select a document to attach if he/she has
            // permission to view Documents records in the selection list.
            // Don't add the Documents button if the user can't view and select
            // documents.
            if (app.acl.hasAccess('view', 'Documents')) {
                attachmentButtons.push({
                    text: app.lang.get('LBL_EMAIL_ATTACHMENTS2', this.module),
                    type: 'menuitem',
                    onAction: (event) => {
                        // Track click on the document attachment button.
                        app.analytics.trackEvent('click', 'tinymce_email_attachment_doc_button', event);
                        this._selectDocument();
                    },
                });
            }

            editor.ui.registry.addMenuButton('sugarattachment', {
                tooltip: app.lang.get('LBL_ATTACHMENTS', this.module),
                icon: 'plus',
                onAction: (event) => {
                    // Track click on the attachment button.
                    app.analytics.trackEvent('click', 'tinymce_email_attachment_button', event);
                },
                fetch: (callback) => {
                    callback(attachmentButtons);
                },
            });
        }
    },

    /**
     * @override
     *
     * Override base field to not return true if the field is readonly,
     * even if the action is "edit". This occurs when toggling visibility via
     * SugarLogic.
     *
     * @return {boolean} false if the field is readonly, else call parent
     * @private
     */
    _isEditView: function() {
        return !this.def.readonly && this._super('_isEditView');
    },

    /**
     * Allows the user to select a document to attach.
     *
     * @private
     * @fires email_attachments:document on the view with the selected document
     * as a parameter. {@link View.Fields.Base.EmailAttachmentsField} attaches
     * the document to the email.
     */
    _selectDocument: function() {
        var def = {
            layout: 'selection-list',
            context: {
                module: 'Documents'
            }
        };

        app.drawer.open(def, _.bind(function(model) {
            var document;

            if (model) {
                // `value` is not a real attribute.
                document = app.data.createBean('Documents', _.omit(model, 'value'));
                this.view.trigger('email_attachments:document', document);
            }
        }, this));
    },

    /**
     * @inheritdoc
     *
     * Adds custom TinyMCEConfig values for Email Templates view
     */
    getTinyMCEConfig: function() {
        // Grab the default config and add/override unique values for creation
        var config = this._super('getTinyMCEConfig');
        config = _.extend(config, this._tinyMCEConfig);
        return config;
    },
})
