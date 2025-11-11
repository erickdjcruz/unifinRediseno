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
 * @class View.Views.Base.DocumentMerges.MergeWidgetListView
 * @alias SUGAR.App.view.views.BaseDocumentMergesMergeWidgetListView
 * @extends View.View
 */
({
    events: {
        'click .download': 'downloadDocument',
        'click .remove-merge': 'removeMergeFromWidget',
    },

    plugins: ['DocumentMergeActions'],

    /**
     * Merges to display inside the widget
     * @property array
     */
    merges: [],

    /**
     * Completion levels of the merge
     * @property {Object}
     */
    completion: {
        'processing': 15,
        'document_load': 30,
        'tags_extract': 45,
        'tags_validate': 60,
        'data_retrieving': 70,
        'serialize_document': 85,
        'send_document': 95,
        'success': 100,
        'error': 100
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        app.events.on('document_merge:poll_merge', this.pollMerge, this);

        this._fields = [
            'id',
            'name',
            'generated_document_id',
            'template_id',
            'parent_id',
            'parent_type',
            'parent_name',
            'file_type',
            'status',
            'message',
            'merge_type'
        ];

        /**
         * trigger loading merges
         */
        this.loadData();
    },

    /**
     * Load merge data
     */
    loadData: function() {
        var url = app.api.buildURL(this.module, 'create', {}, {
            'fields': this._fields,
            'max_num': app.config.maxQueryResult,
            'order_by': 'date_entered:desc',
            'filter': [{seen: 0, assigned_user_id: app.user.id},],
        });

        app.api.call('read', url, null, {
            success: _.bind(this._mergesRetrieved, this),
            error: function(error) {
                app.alert.show('merges-error', {
                    level: 'error',
                    autoClose: true,
                    messages: error.message,
                });
            }
        });
    },

    /**
     * Data retrieved.
     * Set the merge object and render.
     *
     * @param {Object} data
     */
    _mergesRetrieved: function(data) {
        _.isArray(data.records) ? this.merges = data.records : this.merges = [];
        _.map(this.merges, function(merge) {
            merge.completion = this.completion[merge.status];
            merge.isMultiMerge = this._isMultiMerge(merge.merge_type);
        }.bind(this));

        this.render();
        this.layout.reposition();
    },

    /**
     * @inheritdoc
     *
     * Render tooltips and the office icons
     */
    _render: function() {
        this._super('_render', arguments);
        this._renderDownloadIcons();
        this._renderTooltips();
        this._setMergeCompletion();
    },

    /**
     * render svgs for the download icons
     */
    _renderDownloadIcons: function() {
        this.$el.find('.filetype-thumbnail').each(function() {
            $(this).html('<svg xmlns:svg="http://www.w3.org/2000/svg" ' +
            'xmlns="http://www.w3.org/2000/svg" version="1.1" width="28" ' +
            'height="33" id="filetype-svg2"><g id="layer1"><path d="m 1,1 19,0 7,7 0,24 -26,0 z" ' +
            'id="rect2985" style="fill:#ececec;stroke:#000000;stroke-width:1px;stroke-linecap:butt; ' +
            'stroke-linejoin:miter;stroke-miterlimit:4" /><path d="m 20,1 0,7 7,0 z" ' +
            'style="fill:#cccccc;stroke:#000000;stroke-width:1px;stroke-linecap:square; ' +
            'stroke-linejoin:round;" /></g></svg>');
        });
    },

    /**
     * render tooltips
     */
    _renderTooltips: function() {
        this.$('#actions').tooltip({
            selector: '[rel="tooltip"]',
            container: 'body',
        });
    },

    /**
     * Download the document
     *
     * @param {Event} evt
     */
    downloadDocument: function(evt) {
        const documentId = evt.currentTarget.getAttribute('document-id');

        const fileUrl = app.api.buildFileURL({
            module: 'Documents',
            id: documentId,
            field: 'filename',
        },
        {
            forceDownload: true,
            cleanCache: true,
        });

        app.api.fileDownload(
            fileUrl,
            {},
        );
    },

    /**
     * poll the status and message of the DocumentMerge until we find success or error
     *
     * We stop polling only one of the following is true:
     *  - the merge was succesfull
     *  - the merge returned an error
     *  - 3 minutes have passed
     *
     * @param {string} documentMergeId
     */
    pollMerge: function(documentMergeId) {
        var timesRun = 0;
        var maxRun = 90; //equivalent of running the timer for 3 minutes if we run it once at 2 seconds

        this._documentGeneratedEventTriggered = false;
        var timer = setInterval(function() {
            timesRun++;
            //if it takes longer than 3 minutes do not wait
            if (timesRun === maxRun) {
                clearInterval(timer);
            }

            app.data.createBean('DocumentMerges', {id: documentMergeId}).fetch({
                fields: ['status', 'message', 'generated_document_id'],
                success: function(record) {
                    if (record.get('status') === 'success') {
                        clearInterval(timer);
                        if (!this._documentGeneratedEventTriggered) {
                            app.events.trigger('docmerge:document:generated', {
                                id: record.get('generated_document_id')
                            });
                            this._documentGeneratedEventTriggered = true;
                        }

                        app.alert.show('merge_success', {
                            level: 'success',
                            messages: app.lang.getModString('LBL_GENERATED_DOCUMENT', 'DocumentMerges'),
                        });
                    }

                    if (record.get('status') === 'error') {
                        clearInterval(timer);

                        app.alert.show('merge_error', {
                            level: 'error',
                            messages: record.get('message')
                        });
                    }
                }.bind(this),
                error: function(error) {
                    //Stop polling if the request failed
                    clearInterval(timer);
                    if (_.has(error, 'message')) {
                        app.alert.show('merge_error', {
                            level: 'error',
                            messages: error.message
                        });
                    }
                },
                complete: function(response) {
                    /**
                     * Here we manage the completion level of the progress bar
                     */
                    var record = response.xhr.responseJSON || {};

                    _.map(this.merges, _.bind(function(merge) {
                        if (merge.id === record.id) {
                            merge.completion = this.completion[record.status];
                            merge.message = record.message;
                            merge.status = record.status;
                            merge.generated_document_id = record.generated_document_id;
                        }
                    }, this));
                    this.render();
                }.bind(this)
            });
        }.bind(this), 500);
    },

    /**
     * Removes the merge from the widget
     *
     * @param {Event} evt
     */
    removeMergeFromWidget: function(evt) {
        evt.preventDefault();
        const mergeId = evt.target.closest('.merge-row').getAttribute('merge-id');
        if (mergeId) {
            const url = app.api.buildURL('DocumentMerges', mergeId);
            app.api.call('update', url, {'seen': true});
            this.merges = _.filter(this.merges, function(merge) {
                return merge.id !== mergeId;
            });
            this.render();
            this.layout.reposition();
        }
    },

    /**
     * Checks if the merge is a multimerge
     *
     * @param {string} mergeType
     * @return {bool}
     */
    _isMultiMerge: function(mergeType) {
        return mergeType === 'multimerge' ||
            mergeType === 'multimerge_convert' ||
            mergeType === 'labelsgenerate' ||
            mergeType === 'labelsgenerate_convert';
    },

    /**
     * Update merge completion
     */
    _setMergeCompletion: function() {
        _.each(this.merges, _.bind(function(merge) {
            this.$('[data-merge-id=' + merge.id + ']').css('width', merge.completion + '%');
        }, this));
    },

});
