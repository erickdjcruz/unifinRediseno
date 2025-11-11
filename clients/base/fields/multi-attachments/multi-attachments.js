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
 * Field for storing multiple attachments using Notes.
 *
 * @class View.Fields.Base.MultiAttachmentsField
 * @alias SUGAR.App.view.fields.BaseMultiAttachmentsField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click [data-action=download-all]': 'startDownloadArchive'
    },

    plugins: ['DragdropAttachments', 'Attachments'],

    /**
     * @property {Object} `Select2` object.
     */
    $node: null,

    /**
     * @property {string} Selector for `Select2` dropdown.
     */
    fieldSelector: '',

    /**
     * @property {string} Unique ID for file input.
     */
    cid: null,

    /**
     * @property {string} Selector for file input.
     */
    fileInputSelector: '',

    /**
     * @property {Object} Handlebar object.
     */
    _select2formatSelectionTemplate: null,

    /**
     * Count of uploaded files.
     */
    uploaded: 0,

    /**
     * Count of files to upload.
     */
    filesToUpload: 0,

    /**
     * @inheritdoc
     */
    initialize: function (opts) {
        var evt = {},
            relate,
            self = this;
        evt['change ' + this.fileInputSelector + '[data-type=fileinput]'] = '_uploadFile';
        this.events = _.extend({}, this.events, opts.def.events, evt);

        this.fileInputSelector = opts.def.fileinput || '';
        this.fieldSelector = opts.def.field || '';
        this.value = opts.view.attachments || [];
        this.cid = _.uniqueId('attachment');

        this._super('initialize', [opts]);
        this._select2formatSelectionTemplate = app.template.get('f.multi-attachments.selection-partial');
        this._select2formatTmpSelectionTemplate = app.template.get('f.multi-attachments.selection-partial-tmp');

        /**
         * Override handling on drop attachment.
         */
        this.before('attachments:drop', this._onAttachmentDrop, this);
    },

    /**
     * Bind data changes to the field
     * @override Base attachments field made this a noop
     */
    bindDataChange: function() {
        if (this.model) {
            this.createTooltipText();
            this.model.on('change:' + this.name, function() {
                this.createTooltipText();
                if (!_.isEmpty(this.$node.data('select2'))) {
                    this.$node.select2('data', this.getFormattedValue());
                } else {
                    this.render();
                }
            }, this);
        }
    },

    /**
     * When the user clicks "Cancel" in edit or create mode, we destroy notes
     * created to show pills if they haven't been saved on the parent model.
     *
     * TODO: In the future this could be improved to not create Note records for
     * Attachments during edit/create until the user clicks save. That change
     * seems too risky for the week before release freeze.
     * @deprecated
     */
    cancelClicked: function() {
        app.logger.warn('View.Fields.Base.MultiAttachmentsField#cancelClicked is deprecated.');
    },

    /**
     * @inheritdoc
     */
    format: function (value) {
        var value = value instanceof app.BeanCollection ? value.models : value;

        return _.map(value, function (item) {
            item = item instanceof Backbone.Model ? item.toJSON() : item;
            var id = item.id || item.filename_guid;
            var name = item.name || item.filename;
            var isImage = item.file_mime_type && item.file_mime_type.indexOf('image') !== -1;
            var forceDownload = !isImage;
            var mimeType = isImage ? 'image' : 'application/octet-stream';
            var fileName = name.substring(0, name.lastIndexOf('.'));
            var fileExt = name.substring(name.lastIndexOf('.') + 1).toLowerCase();
            var urlOpts = {
                    module: this.def.module,
                    id: item.id,
                    field: this.def.modulefield
                };

            fileExt = !_.isEmpty(fileExt) ? '.' + fileExt : fileExt;

            return {
                id: id,
                mimeType: mimeType,
                fileName: fileName,
                fileExt: fileExt,
                tmpFile: (typeof(item.id) === 'undefined'),
                url: app.api.buildFileURL(
                    urlOpts,
                    {
                        htmlJsonFormat: false,
                        passOAuthToken: false,
                        cleanCache: true,
                        forceDownload: forceDownload
                    }
                )
            };
        }, this);
    },

    /**
     * Creates a list of file names that could be shown on a list view
     * in case the list of files is collapsed.
     *
     * @return {string} The list of files.
     */
    createTooltipText() {
        var files = this.model.get(this.name);
        this.fileList = '';

        if (files && files.length) {
            this.fileList = _.reduce(files.models, function(list, model) {
                list.push(model.get('filename'));
                return list;
            }, []).join(', ');
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this.action == 'noaccess') {
            return;
        }
        // Please, do not put this._super call before acl check,
        // due to _loadTemplate function logic from sidecar/src/view.js file
        this._super('_render', []);

        this.$node = this.$(this.fieldSelector + '[data-type=attachments]');
        this.setSelect2Node();
        if (this.$node.length > 0) {
            this.$node.select2({
                allowClear: true,
                multiple: true,
                containerCssClass: 'select2-choices-pills-close span12 with-padding multi-attachments-detail-view',
                tags: [],
                formatSelection: _.bind(this.formatSelection, this),
                width: 'off',
                escapeMarkup: function(m) {
                    return m;
                }
            });
            $(this.$node.data('select2').container).attr('data-attachable', true);
            this.refreshFromModel();
        }
        this._IEDownloadAttributeWorkaroud();
    },

    /**
     * 'Download' attribute workaround for IE browser (which does not support it)
     */
    _IEDownloadAttributeWorkaroud: function () {
        var isIE = /*@cc_on!@*/false || !!document.documentMode;
        var field = "";
        var href = "";
        if (isIE) {
            var downloadFile = function (event) {
                field = this.getAttribute("download");
                href = this.getAttribute("href");
                event.preventDefault();
                var request = new XMLHttpRequest();
                request.addEventListener("load",requestListener, false);
                request.open("get", this, true);
                request.responseType = 'blob';
                request.send();
            }
            var requestListener = function () {
                if (field == "") {
                    field = href;
                }
                var blobObject = this.response;
                window.navigator.msSaveBlob(blobObject, field);
            }
            var items = document.querySelectorAll('a[download], area[download]');
            for (var i = 0; i < items.length; i++) {
                items[i].addEventListener('click', downloadFile, false);
            }
        }
    },

    /**
     *  Update `Select2` data from model.
     */
    refreshFromModel: function () {
        this.$node.select2('data', this.getFormattedValue());
    },

    /**
     * Set `$node` as `Select2` object.
     * Unlink and delete attached notes on remove from select2.
     */
    setSelect2Node: function () {
        var self = this;
        if (!this.$node || this.$node.length == 0) {
            return;
        }
        this.$node.off('select2-removed');
        this.$node.off('select2-opening');

        this.$node.on('select2-removed', function(evt) {
            self.removeAttachment(evt);
        });

        /**
         * Disables dropdown for `Select2`
         */
        this.$node.on('select2-opening', function (evt) {
            evt.preventDefault();
        });

    },

    /**
     * Remove selected attachment.
     * @param {Event} event
     */
    removeAttachment: function(event) {
        var file = _.find(this.model.get(this.name).models, function(model) {
            return model.get('id') === event.val || model.get('filename_guid') === event.val;
        });
        if (file) {
            this.model.get(this.name).remove(file);
        }
    },

    /**
     * Return file input.
     * @return {Object}
     */
    getFileNode: function () {
        return this.$(this.fileInputSelector + '[data-type=fileinput]');
    },

    /**
     * @inheritdoc
     */
    bindDomChange: function () {
        this.setSelect2Node();
    },

    /**
     * A private helper function to call Attachment's uploadFile, as it needs extra arguments
     *
     * @private
     */
    _uploadFile: function() {
        this._toggleUploading(true);

        $input = this.getFileNode();
        this.uploaded = 0;
        this.filesToUpload = $input[0].files.length;

        _.each($input[0].files, _.bind(function(file) {
            var dt = new DataTransfer();
            dt.items.add(file);

            var input = document.createElement('input');
            input.type = 'file';
            input.files = dt.files;

            this.uploadFile([input], 'filename', {
                temp: true,
            });
        }, this));
    },

    /**
     * Private function which toggles the "Uploading..." message on file uplaods
     *
     * @param flag true to show, false to dismiss
     * @private
     */
    _toggleUploading: function(flag) {
        $('a[data-event="button:save_button:click"]').toggleClass('disabled', flag);
        if (flag && _.isUndefined(app.alert.get('uploading_file'))) {
            app.alert.show('uploading_file', {
                level: 'process',
                title: app.lang.get('LBL_UPLOADING_DOTS'),
            });
        } else if (app.alert.get('uploading_file')) {
            app.alert.dismiss('uploading_file');
        }
    },

    /**
     * Handle a successful file upload
     *
     * @param {Object} data
     * @private
     */
    _handleFileUploadSuccess: function(data) {
        if (!data.record || !data.record.id) {
            error = new Error('Temporary file has no GUID');
            app.logger.error(error.message);
            app.alert.show('upload_error', {
                level: 'error',
                messages: app.lang.get('ERROR_UPLOAD_FAILED')
            });
            return;
        }

        if (app.config.platform === 'portal') {
            data.record.portal_flag = 1;
        }

        var file = this.getUploadedFileBean(data);
        this.addUploadedFileToCollection(this.model.get(this.name), file);

        this.uploaded++;
        if (this.filesToUpload <= this.uploaded) {
            this._toggleUploading(false);
        }
    },

    /**
     * Clear input field after file is uploaded.
     *
     * @param {Object} data
     * @private
     */
    _handleFileUploadComplete: function(data) {
        $input = this.getFileNode();
        $input.val('');
    },

    /**
     * Handles an error response from the API for uploading the file.
     *
     * If the error code is 'request_too_large' or status is 413, then an error is
     * shown to the user indicating that the error was due to exceeding the
     * maximum filesize. Otherwise, the error is handled by the framework.
     *
     * @param {HttpError} error.
     * @private
     */
    _handleFileUploadError: function(error) {
        if (error && (error.code === 'request_too_large' || error.status === 413)) {
            // Mark the error as having been handled so that it doesn't get
            // handled again.
            error.handled = true;
            app.alert.show(error.code, {
                level: 'error',
                autoClose: true,
                messages: app.lang.get('ERROR_MAX_FILESIZE_EXCEEDED')
            });
        }

        if (error && !error.handled && _.isFunction(app.api.defaultErrorHandler)) {
            app.api.defaultErrorHandler(error);
        }

        this._toggleUploading(false);
    },

    /**
     * Handler for 'attachments:drop' event.
     * This event is triggered when user drops file on the file field.
     *
     * @param {Event} event Drop event.
     * @return {boolean} Returns 'false' to prevent running default behavior.
     */
    _onAttachmentDrop: function(event) {
        event.preventDefault();
        $input = this.getFileNode();

        _.each(event.dataTransfer.files, function(file) {
            var dt = new DataTransfer();
            dt.items.add(file);
            $input[0].files = dt.files;
            this._uploadFile();
        }, this);

        return false;
    },

    /**
     * Format selection for `Select2` to display.
     * @param {Object} attachment
     * @return {string}
     */
    formatSelection: function (attachment) {
        return (attachment.tmpFile) ?
            this._select2formatTmpSelectionTemplate(attachment) : this._select2formatSelectionTemplate(attachment);
    },

    /**
     * Download archived files from server.
     */
    startDownloadArchive: function () {
        var params = {
            format:'sugar-html-json',
            link_name: this.def.link,
            platform: app.config.platform
        };
        params[(new Date()).getTime()] = 1;

        // todo: change buildURL to buildFileURL when will be allowed "link" attribute
        var uri = app.api.buildURL(this.model.module, 'file', {
            module: this.model.module,
            id: this.model.id,
            field: this.def.modulefield
        }, params);

        app.api.fileDownload(
            uri,
            {
                error: function (data) {
                    // refresh token if it has expired
                    app.error.handleHttpError(data, {});
                }
            },
            {iframe: this.$el}
        );
    },

    /**
     * Check if a field has a value (selected file/files).
     * @return {boolean} True if the field is empty.
     */
    isFieldEmpty: function() {
        let value = this.model.get(this.name);
        return _.isObject(value) && _.isEmpty(value.models);
    },

    /**
     * @inheritdoc
     *
     * Disposes event listeners on `Select2` object.
     */
    dispose: function () {
        // Clean up uploading popup if its still there
        this._toggleUploading(false);

        if (this.$node) {
            this.$node.off('select2-removed');
            this.$node.off('select2-opening');
        }
        this._super('dispose');
    },
})
