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
 * @class View.Layouts.Base.DocumentMerges.SidebarMergeWidgetLayout
 * @alias SUGAR.App.view.layouts.BaseDocumentMergesSidebarMergeWidgetLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.layout, 'reload', this.reload);
        this.listenTo(app.events, 'document:merge', this._mergeDocument, this);
    },

    /**
     * Reload the list of merges
     */
    reload: function() {
        this.getComponent('merge-widget-list').loadData();
        this.render();
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
     * Triggers the parent layout to reposition the widget
     */
    reposition: function() {
        this.layout.trigger('reposition');
    }
});
