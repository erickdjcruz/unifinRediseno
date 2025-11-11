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
 * This plugin creates uniformity on how files are uploaded throughout the app
 */
(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('Attachments', ['field'], {
            /**
             * Upload the file and return the file
             *
             * @param {Object} $inputFile
             * @param {string} fileFieldName
             * @param {Object} ajaxOptions
             * @return {Data.Bean}
             */
            uploadFile: function($inputFile, fileFieldName, ajaxOptions) {
                var file = app.data.createBean('Notes');

                file.uploadFile(
                    fileFieldName,
                    $inputFile,
                    {
                        success: _.bind(function(data) {
                            if (this.disposed) {
                                return;
                            }

                            if (this._handleFileUploadSuccess && _.isFunction(this._handleFileUploadSuccess)) {
                                this._handleFileUploadSuccess(data);
                            }
                        }, this),
                        complete: _.bind(function(data) {
                            if (this._handleFileUploadComplete && _.isFunction(this._handleFileUploadComplete)) {
                                this._handleFileUploadComplete(data);
                            }
                        }, this),
                        error: _.bind(function(data) {
                            if (this._handleFileUploadError && _.isFunction(this._handleFileUploadError)) {
                                this._handleFileUploadError(data);
                            }
                        }, this)
                    },

                    ajaxOptions
                );

                return file;
            },

            /**
             * Add the uploaded file to a related collection so ModuleApi can create
             * the related beans for free
             *
             * @param {Data.BeanCollection} collection
             * @param {Data.Bean} file
             */
            addUploadedFileToCollection: function(collection, file) {
                if (!collection || !collection instanceof app.BeanCollection) {
                    return;
                }

                if (!file || !file instanceof app.Bean) {
                    return;
                }

                collection.add(file, {merge: true});
            },

            /**
             * Create and return the uploaded file bean
             *
             * @param {Object} data
             * @param {Object} attributes
             * @return {Data.Bean}
             */
            getUploadedFileBean: function(data, attributes) {
                var attrs = !_.isEmpty(attributes) ?
                    attributes :
                    this.getUploadedFileBeanDefaultAttributes(data);

                return app.data.createBean('Notes', attrs);
            },

            /**
             * Default Notes bean attributes for an uploaded file
             *
             * @param {Object} data
             * @return {Object}
             */
            getUploadedFileBeanDefaultAttributes: function(data) {
                if (!data.record || !data.record.id) {
                    return {};
                }

                return {
                    _link: 'attachments',
                    name: data.record.name || data.record.filename,
                    filename_guid: data.record.id,
                    filename: data.record.filename || data.record.name,
                    file_mime_type: data.record.file_mime_type,
                    file_size: data.record.file_size,
                    file_ext: data.record.file_ext,
                    portal_flag: data.record.portal_flag
                };
            }
        });
    });
})(SUGAR.App);
