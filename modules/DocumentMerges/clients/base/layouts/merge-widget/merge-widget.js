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
 * The base layout for the document merge widget component.
 *
 * @class View.Layouts.Base.DocumentMerges.MergeWidgetLayout
 * @alias SUGAR.App.view.layouts.BaseDocumentMergesMergeWidgetLayout
 * @extends View.Layouts.Base.HelpLayout
 */
({
    extendsFrom: 'HelpLayout',

    /**
     * @property {string}
     */
    _module: 'DocumentMerges',

    /**
     * Use this if the popover is not fully initialized
     */
    _popoverDefaultWidth: 400,

    /**
     * Leave 25px of space between rhs edge of popover and the screen.
     */
    _popoverLeftOffset: 25,

    /**
    * @inheritdoc
    */
    initialize: function(options) {
        this._super('initialize', [options]);

        /**
         * The internal state of this layout.
         * By default this layout is closed ({@link #toggle} will call render).
         *
         * FIXME TY-1798/TY-1800 This is needed due to the bad popover plugin.
         *
         * @type {boolean}
         * @private
         */
        this._isOpen = false;

        /**
          * This is the Help button in the footer.
          * Needed to render the modal by calling `popover` on the button.
          *
          * @type {jQuery}
          */
        this.button = options.button;

        app.events.on('document:merge', this._mergeDocument, this);

        $(window).on('resize.' + this.cid, _.bind(function() {
            this.reposition();
        }, this));
    },

    /**
     * Merge a document template
     *
     * @param {Object} options
     */
    _mergeDocument: function(options) {
        const recordId = options.currentRecordId;
        const recordModule = options.currentRecordModule;
        const templateId = options.templateId;
        const templateName = options.templateName;
        const isPdf = options.isPdf;

        const requestType = 'read';
        const apiPath = 'DocumentTemplates';

        const requestMeta = {
            fields: [
                'name',
                'file_ext',
                'use_revisions',
            ],
        };

        const apiCallbacks = {
            success: _.bind(function createTemplate(result) {
                const fileExt = result.file_ext;
                const useRevision = result.use_revisions;
                const mergeType = this._getMergeType(fileExt, isPdf);

                const mergeOptions = {
                    recordId,
                    recordModule,
                    templateId,
                    templateName,
                    useRevision,
                    mergeType,
                };

                this._startDocumentMerge(mergeOptions);
            }, this)
        };

        const apiUrl = app.api.buildURL(apiPath, templateId, null, requestMeta);
        app.api.call(requestType, apiUrl, null, null, apiCallbacks);
    },

    /**
     * Start document merging
     *
     * @param {Object} payload
     */
    _startDocumentMerge: function(payload) {
        const requestType = 'create';
        const apiPath = 'DocumentMerge';
        const apiPathDocumentType = 'merge';

        const apiCallbacks = {
            success: function createTemplate(documentMergeId) {
                //open widget in order to show the currently merging document
                app.events.trigger('document_merge:show_widget');
                //start polling for changes on the merge request
                app.events.trigger('document_merge:poll_merge', documentMergeId);
            },
            error: function(errorMessage) {
                app.alert.show('merge_error', {
                    level: 'error',
                    messages: errorMessage,
                });
            }
        };

        const apiUrl = app.api.buildURL(apiPath, apiPathDocumentType);

        app.api.call(requestType, apiUrl, payload, null, apiCallbacks);
    },

    /**
     * Sets the correct merge type based on the template extension
     *
     * @param {string} extension file extension
     * @param {bool} isPdf check if the document should be converted to pdf
     * @private
     *
     * @return {string} Merge type.
     */
    _getMergeType: function(extension, isPdf) {
        switch (extension) {
            case 'pptx':
                if (isPdf) {
                    return 'presentation_convert';
                }

                return 'presentation';
            case 'xlsx':
                if (isPdf) {
                    return 'excel_convert';
                }

                return 'excel';
            default:
                if (isPdf) {
                    return 'convert';
                }

                return 'merge';
        }
    },

    /**
     * Initializes the popover plugin for the button given.
     *
     * @param {jQuery} button The jQuery button.
     * @private
     */
    _initPopover: function(button) {
        button.popover({
            title: this._getTitle('LBL_DOCUMENT_MERGE_FOOTER'),
            content: _.bind(function() {
                return this.$el;
            }, this),
            html: true,
            placement: 'top',
            template: '<div class="popover footer-modal feedback document-merge-widget" data-modal="document-merge">' +
                '<div class="arrow"></div><h3 class="popover-title dm-popover-title"></h3>' +
                '<div class="popover-content"></div></div>'
        });
    },

    /**
     * Fetches the title of the widget modal.
     * If none exists, returns a default help title.
     *
     * @param {string} titleKey The modal title label.
     * @return {string} The converted title.
     * @private
     */
    _getTitle: function(titleKey) {
        var title = app.lang.get(titleKey, this._module, app.controller.context);

        return title === titleKey ? app.lang.get('LBL_DOCUMENT_MERGES') : title;
    },

    /**
     * Toggle this view (by re-rendering).
     *
     * @param {boolean} [show] `true` to show, `false` to hide, `undefined`
     *   to toggle the current state.
     */
    toggle: function(show) {
        if (!this.button) {
            return;
        }

        if (_.isUndefined(show)) {
            this._isOpen = !this._isOpen;
        } else {
            this._isOpen = show;
        }

        if (this._isOpen) {
            this.render();
            this._initPopover(this.button);
            this.button.popover('show');
            this.bindOutsideClick();
        } else {
            this.button.popover('hide');
        }

        this.trigger(this._isOpen ? 'show' : 'hide', this, this._isOpen);
    },

    /**
     * Closes the widget modal if event target is outside of the DocumentMerge widget modal.
     *
     * @param {Object} evt jQuery event.
     */
    closeOnOutsideClick: function(evt) {
        if ($(evt.target).closest('.document-merge-widget').length !== 0) {
            //if click inside the widget do not close
            return;
        }

        if ($(evt.target).closest('.merge-row').length !== 0) {
            //if click on the widget action buttons
            return;
        }

        if ($(evt.target).closest('[data-modal=document-merge]').length === 0) {
            //if not click on the button
            this.toggle(false);
        }
    },

    /**
     * Reload all the merges
     */
    reload: function() {
        this.getComponent('merge-widget-list').loadData();
        this.render();
    },

    /**
     * Reposition the layout so we can set the top of the layout.
     */
    reposition: function() {
        const $popoverContainer = this.button.data()['bs.popover'];

        if (!$popoverContainer) {
            return;
        }

        const $tip = $popoverContainer.tip();
        const height = $tip.height();
        const top = 0 - height;

        let left;
        if (app.lang.direction === 'rtl') {
            // Leave 25px of space between lhs edge of popover and the screen.
            left = this._popoverLeftOffset;
        } else {
            let popoverWidth = $popoverContainer.width ?
                $popoverContainer.width() : this._popoverDefaultWidth;
            // Leave 25px of space between rhs edge of popover and the screen.
            left = $(window).width() - popoverWidth - this._popoverLeftOffset;
        }

        $tip.css({top: top, left: left});
    },

    _dispose: function() {
        $(window).off('resize.' + this.cid);
        this._super('_dispose');
    }
});
