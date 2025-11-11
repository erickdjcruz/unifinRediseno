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
         * - Field level plugin
         * Adds ability to override TinyMCE default file upload functionality via the EmbeddedFiles module and fileAPI.
         * Just override the TinyMCE file_browser_callback function with the tinyMCEFileBrowseCallback method.
         * Once attached the plugin creates a hidden input to upload files.
         *
         * - View level plugin
         * Adds resize event listeners on the tinyMCE editors on create views
         * Extends a create view to handle resize events and make the editor responsive
         */
        app.plugins.register('Tinymce', ['view', 'field'], {
            /**
             * File input element.
             */
            $embeddedInput: null,

            /**
             * Name of file input.
             */
            fileFieldName: null,

            /**
             * Images already exist.
             */
            existingImages: null,

            /**
             * Newly added images.
             */
            newImages: null,

            /**
             * The tinyMCE editor's height can never be smaller than this constant.
             */
            MIN_TINYMCE_EDITOR_HEIGHT: 200,

            /**
             * The padding that needs to be accounted for to prevent the scroll bar
             * from appearing when the tinyMCE editor is resized.
             */
            TINYMCE_EDITOR_RESIZE_PADDING: 5,

            /**
             * @inheritdoc
             */
            onAttach: function(component) {
                if (component instanceof app.view.Field) {
                    this._fieldOnAttach(component);
                } else if (component instanceof app.view.View) {
                    this._viewOnAttach(component);
                }
            },

            /**
             * onAttach for a View component
             *
             * @param {App.view.Component} component
             * @protected
             */
            _viewOnAttach: function(component) {
                component.on('render', function() {
                    this.setupEditorResize();
                }, this);
            },

            /**
             * onAttach for a Field component
             *
             * @param {App.view.Component} component
             * @protected
             */
            _fieldOnAttach: function(component) {
                component.on('init', function() {
                    this.fileFieldName = component.options.def.name + '_file';
                    this.$embeddedInput = $('<input />', {name: this.fileFieldName, type: 'file'}).hide();
                }, this);
                component.on('render', function() {
                    component.$el.append(this.$embeddedInput);
                }, this);
            },

            /**
             * @inheritdoc
             */
            onDetach: function(component) {
                if (component instanceof app.view.Field) {
                    this._fieldOnDetach(component);
                } else if (component instanceof app.view.View) {
                    this._viewOnDetach(component);
                }
            },

            /**
             * onDetach for a View component
             *
             * @param {App.view.Component} component
             * @protected
             */
            _viewOnDetach: function(component) {
                $(window).off('resize.' + this.cid);
            },

            /**
             * onDetach for a Field component
             *
             * @param {App.view.Component} component
             * @protected
             */
            _fieldOnDetach: function(component) {
                this.$embeddedInput.remove();
            },

            /**
             * Sets the listeners to resize the TinyMCE editor on create view
             */
            setupEditorResize: function() {
                // batch queued calls to editor resize function
                this.resizeEditor = _.debounce(_.bind(this._resizeEditor, this), 100);

                this.listenTo(this.context, 'tinymce:oninit', function() {
                    this.resizeEditor();
                });
                this.listenTo(app.drawer, 'drawer:resize', function() {
                    this.resizeEditor();
                });
                this.on('more-less:toggled', function() {
                    this.resizeEditor();
                }, this);
                if (this.module === 'Emails') {
                    this.on('email-recipients:toggled', function() {
                        this.resizeEditor();
                    }, this);
                }
                $(window).on('resize.' + this.cid, this.resizeEditor);
            },

            /**
             * Resize the editor based on the height of the layout container.
             *
             * @protected
             */
            _resizeEditor: function() {
                var $editor;
                var layoutHeight;
                var recordHeight;
                var showToggleHeight;
                var editorHeight;
                // The difference in height between the current editor and the actual
                // available height of the space available to it.
                var diffHeight;
                var newEditorHeight;

                if (this.disposed) {
                    return;
                }

                $editor = this.$('.mce-stack-layout .mce-stack-layout-item iframe');
                // Cannot resize it if the editor is not already rendered.
                if ($editor.length === 0) {
                    return;
                }

                layoutHeight = this.layout.$el.outerHeight(true);
                // This is the total height including the html editor and other
                // record fields. It does not include the show-hide toggle.
                recordHeight = this.$('.record').outerHeight(true);

                // Don't include the negative top margin on show-hide toggle because it
                // has no affect on the layout because the .record has no bottom margin
                showToggleHeight = this.$('.show-hide-toggle').outerHeight(false);
                editorHeight = $editor.height();
                // Calculate the difference between the current editor height and
                // maximum available height. Subtracts padding to prevent the scrollbar.
                diffHeight = layoutHeight - recordHeight - showToggleHeight - this.TINYMCE_EDITOR_RESIZE_PADDING;
                // Add the space left to fill to the current height of the editor to
                // get the new height.
                newEditorHeight = editorHeight + diffHeight;

                // Don't drop below the minimum height.
                if (newEditorHeight < this.MIN_TINYMCE_EDITOR_HEIGHT) {
                    newEditorHeight = this.MIN_TINYMCE_EDITOR_HEIGHT;
                }

                // Set the new height for the editor.
                $editor.height(newEditorHeight);
            },

            /**
             * Handle embedded images.
             * @param {string} value
             */
            handleEmbeddedImages: function(value) {
                // remove new images when content is reset
                if (!_.isEmpty(this.newImages)) {
                    this._removeImages(this.newImages);
                }
                this._initImages();
                if (value) {
                    var images = this._parseImages(value);
                    this.existingImages.push(...images);
                }
            },

            /**
             * Remove both existing and new images which have been removed from editor when 'Save' is clicked.
             */
            handleImageSave: function() {
                if (typeof tinymce == 'object' && tinymce.activeEditor) {
                    var currentImages = this._parseImages(tinymce.activeEditor.getContent());
                    var removedImages = _.difference(_.union(this.existingImages, this.newImages), currentImages);
                    if (!_.isEmpty(removedImages)) {
                        this._removeImages(removedImages);
                    }
                    this._initImages();
                }
            },

            /**
             * Set initial values for image data.
             */
            _initImages: function() {
                this.newImages = [];
                this.existingImages = [];
            },

            /**
             * Parse a string to get file ids of all embedded images.
             * @param {string} value
             * @return {Array}
             */
            _parseImages: function(value) {
                var images = [];
                var matches = value.matchAll(/\/EmbeddedFiles\/(\S+)\/file\//g);
                for (const match of matches) {
                    if (!_.contains(images, match[1])) {
                        images.push(match[1]);
                    }
                }
                return images;
            },

            /**
             * Remove images from server.
             * @param {Array} fileIds
             */
            _removeImages: function(fileIds) {
                _.each(fileIds, function(fileId) {
                    var embeddedFile = app.data.createBean('EmbeddedFiles', {id: fileId});
                    embeddedFile.destroy({showAlerts: false});
                });
            },

            /**
             * Handle image paste.
             *
             * This callaback creates new EmbeddedFile object, so this module should be present in SugarCRM.
             * If there is no EmbeddedFile module, this method does nothing.
             *
             * To enable image paste in tinymce you need to specify 'paste_data_images' and 'images_upload_handler'.
             * See [TinyMCE documentation](http://www.tinymce.com/wiki.php/Configuration:paste_data_images) and
             * [TinyMCE documentation](http://www.tinymce.com/wiki.php/Configuration:images_upload_handler)
             *
             * Example:
             *
             * config.paste_data_images = true;
             * config.images_upload_handler = _.bind(this.tinyMCEImagePasteCallback, this);
             *
             * @param {Object} blobInfo Blob containing a pasted image.
             * @return {Promise}
             */
            tinyMCEImagePasteCallback: function(blobInfo) {
                var embeddedFile = app.data.createBean('EmbeddedFiles');

                return new Promise((resolve) => {
                    embeddedFile.save({name: blobInfo.filename()}, {
                        success: (model) => {
                            this._savePastedImage(blobInfo, model)
                                .then((src) => {
                                    resolve(src);
                                });
                        }
                    });
                });
            },

            /**
             * Handler to save pasted image.
             *
             * @param {Object} blobInfo Blob containing a pasted image.
             * @param {EmbeddedFile} model Model to save.
             * @return {Promise}
             * @private
             */
            _savePastedImage: function(blobInfo, model) {
                return new Promise((resolve, reject) => {
                    // we need to use the same data structure for a file input to use our file api
                    var imageData = [
                        {
                            files: [
                                blobInfo.blob()
                            ]
                        }
                    ];
                    model.uploadFile(
                        this.fileFieldName,
                        imageData,
                        {
                            success: _.bind(function(rsp) {
                                var url = app.api.buildFileURL(
                                    {
                                        module: 'EmbeddedFiles',
                                        id: rsp.record.id,
                                        field: this.fileFieldName
                                    },
                                    {
                                        htmlJsonFormat: false,
                                        passOAuthToken: false,
                                        cleanCache: true,
                                        forceDownload: false
                                    }
                                );

                                if (this.newImages) {
                                    this.newImages.push(rsp.record.id);
                                }
                                resolve(url);
                            }, this),
                            error: _.bind(function() {
                                app.alert.show('upload-error', {
                                    level: 'error',
                                    messages: 'ERROR_UPLOAD_FAILED'
                                });

                                reject({
                                    message: app.lang.get('ERROR_UPLOAD_FAILED'),
                                    remove: true,
                                });
                            }, this)
                        }
                    );
                });
            },

            /**
             * Handle embedded file upload process.
             *
             * This callaback creates new EmbeddedFile object, so this module should be present in SugarCRM.
             * If there is no EmbeddedFile module, this method does nothing.
             *
             * To enable the usage of embeded files in tinymce you need to specify 'file_browser_callback'.
             * See [TinyMCE documentation](http://www.tinymce.com/wiki.php/Configuration:file_browser_callback)
             *
             * Example:
             *
             * config.file_browser_callback = _.bind(this.tinyMCEFileBrowseCallback, this);
             *
             * @param {string} fieldName The name (and ID) of the dialogue window's input field.
             * @param {string} url Carries the existing link URL if you modify a link.
             * @param {string} type Either 'image', 'media' or 'file'.
             * (called respectively from image plugin, media plugin and link plugin insert/edit dialogs).
             * @param {Object} win A reference to the dialogue window itself.
             */
            tinyMCEFileBrowseCallback: function(fieldName, url, type, win) {

                if (_.isUndefined(app.metadata.getModule('EmbeddedFiles'))) {
                    return;
                }

                var attributes = {
                    fieldName: fieldName,
                    type: type,
                    win: win
                };

                this.$embeddedInput.unbind().change(_.bind(this._onEmbededFile, this, attributes));
                this.$embeddedInput.trigger('click');
            },

            /**
             * Handler called when user chooses file to upload.
             *
             * @param {Object} attributes
             * @param {string} attributes.fieldName The name (and ID) of the dialogue window's input field.
             * @param {string} attributes.type Either 'image', 'media' or 'file'
             * @param {string} attributes.win A reference to the dialogue window itself.
             * @param {Event} event Dom event.
             * @private
             */
            _onEmbededFile: function(attributes, event) {
                var $target = $(event.target),
                    fileObj = $target[0].files[0];

                if (attributes.type === 'image' && fileObj.type.indexOf('image') === -1) {
                    this.clearFileInput($target);
                    tinymce.activeEditor.windowManager.alert(app.lang.get('LBL_UPLOAD_ONLY_IMAGE', 'EmbeddedFiles'));
                    return;
                }

                var embeddedFile = app.data.createBean('EmbeddedFiles');
                embeddedFile.save({name: fileObj.name}, {
                    success: _.bind(this._saveEmbededFile, this, attributes)
                });
            },

            /**
             * Handler to save new embeded file.
             *
             * @param {Object} attributes
             * @param {string} attributes.fieldName The name (and ID) of the dialogue window's input field.
             * @param {string} attributes.win A reference to the dialogue window itself.
             * @param {EmbeddedFile} model Model to save.
             * @private
             */
            _saveEmbededFile: function(attributes, model) {
                model.uploadFile(
                    this.fileFieldName,
                    this.$embeddedInput,
                    {
                        success: _.bind(function(rsp) {
                            var forceDownload = !(rsp[this.fileFieldName]['content-type'].indexOf('image') !== -1);
                            var url = app.api.buildFileURL(
                                {
                                    module: 'EmbeddedFiles',
                                    id: rsp.record.id,
                                    field: this.fileFieldName
                                },
                                {
                                    htmlJsonFormat: false,
                                    passOAuthToken: false,
                                    cleanCache: true,
                                    forceDownload: forceDownload
                                }
                            );

                            $(attributes.win.document).find('#' + attributes.fieldName).val(url);
                            this.newImages.push(rsp.record.id);

                            if (attributes.type === 'image') {
                                // We are, so update image dimensions.
                                this.updateImageData(url);
                            }

                            this.clearFileInput(this.$embeddedInput);
                        }, this),
                        error: _.bind(function() {
                            app.alert.show('upload-error', {
                                level: 'error',
                                messages: 'ERROR_UPLOAD_FAILED'
                            });
                            this.clearFileInput(this.$embeddedInput);
                        }, this)
                    }
                );
            },

            /**
             * Clears input file value.
             *
             * @param {Object} $field Jquery input selector.
             */
            clearFileInput: function($field) {
                $field.val('');
                // For IE.
                $field.replaceWith($field.clone(true));
            },

            /**
             * Updates image data such as dimensions for example.
             *
             * @param {string} url Uploaded image url.
             */
            updateImageData: function(url) {
                var win = tinymce.activeEditor.windowManager.windows[0];
                win.find('#src').value(url).fire('change');
            }
        });
    });
})(SUGAR.App);
