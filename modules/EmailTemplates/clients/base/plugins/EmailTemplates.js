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
(function(app) {
    app.events.on('app:init', function() {
        /**
         * This plugin is build to share common elements of EmailTemplates views
         */
        app.plugins.register('EmailTemplates', ['view'], {
            /**
             * Attach event listeners to toggle attachment visibility, and
             * insert HTML and text variables.
             * @param {Object} component
             * @param {Object} plugin
             * @return {void}
             */
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    this.model.on('change:attachments_collection', this._toggleAttachmentsVisibility, this);
                    this.on('insertClicked', this._insertVariable, this);
                });
            },

            /**
             * Toggle attachment field visibility based on whether or not it is empty.
             * Emails module hides its attachments when it is empty, so this makes the two
             * match
             * @private
             */
            _toggleAttachmentsVisibility: function() {
                var attachmentsField = this.getField('attachments_collection');
                if (!attachmentsField) {
                    return;
                }
                var $el = attachmentsField.getFieldElement();
                $el.closest('.record-cell').toggle(!attachmentsField.isEmpty());
            },

            /**
             * Insert the provided text into the text or HTML bodies based on
             * whether the Email Template is in text only mode.
             *
             * @param {string} text
             * @private
             */
            _insertVariable: function(text) {
                if (this.model.get('text_only')) {
                    this._insertVariableText(text);
                } else {
                    this._insertVariableHtml(text);
                }
            },

            /**
             * Insert variable into the textarea field at the current cursor
             * position
             *
             * @param {string} text
             * @private
             */
            _insertVariableText: function(text) {
                // Get textarea HTML element with safety checks in case field
                // is removed from the layout
                var field = this.getField('body');
                if (_.isEmpty(field)) {
                    return;
                }
                var textarea = _.first(field.$el.find('textarea'));
                if (_.isEmpty(textarea)) {
                    return;
                }
                // IE support
                if (document.selection) {
                    textarea.focus();
                    var sel = document.selection.createRange();
                    sel.text = text;
                }
                //Other Browsers
                else if (textarea.selectionStart || textarea.selectionStart === '0') {
                    var startPos = textarea.selectionStart;
                    var endPos = textarea.selectionEnd;
                    textarea.value = textarea.value.substring(0, startPos) + text +
                        textarea.value.substring(endPos, textarea.value.length);
                } else {
                    textarea.value += text;
                }
            },

            /**
             * Insert variable into the tinyMCE field at the current cursor
             * position
             *
             * @param {string} text
             * @private
             */
            _insertVariableHtml: function(text) {
                var bodyField = this.getField('body_html');
                var editor = bodyField._htmleditor;
                editor.insertContent(text, {format: 'raw'});
            },

            /**
             * Remove event listeners
             */
            onDetach: function() {
                if (this.model) {
                    this.model.off('change:attachments_collection', this._toggleAttachmentsVisibility);
                }
                this.off('insertClicked', this._insertVariable);
            }
        });
    });
})(SUGAR.App);
