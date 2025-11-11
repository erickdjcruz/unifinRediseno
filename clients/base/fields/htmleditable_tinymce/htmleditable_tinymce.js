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
 * @class View.Fields.Base.Htmleditable_tinymceField
 * @alias SUGAR.App.view.fields.BaseHtmleditable_tinymceField
 * @extends View.Fields.Base.BaseField
 */
({
    plugins: ['Tinymce'],

    fieldSelector: '.htmleditable', //iframe or textarea selector
    _htmleditor: null, // TinyMCE html editor
    _isDirty: false,
    // When the model already has the value being set, there is no need to trigger the "SetContent" event, which calls
    // our callback to save the content to the model. But we don't want to short-circuit events in TinyMCE's workflow,
    // so the following flag can be toggled to false to indicate that we don't need to save the content to the model
    // inside of the callback.
    _saveOnSetContent: true,

    /**
     * Disable field pill decoration on tinyMCE fields.
     *
     * Click-to-edit is disabled for this field due to our click listeners not
     * working as intended within the tinyMCE iFrame. Blocking field pills makes
     * the behavior consistent whether the field has contents or not.
     */
    disableDecoration: true,

    /**
     * Current version of tinyMCE editor. This is appended to script requests made
     * by tinyMCE and the tinyMCE jquery plugin to ensure we don't load from the
     * browser cache after a library upgrade.
     */
    tinyMCEVersion: '6.4.2',

    /**
     * Render an editor for edit view or an iframe for others
     *
     * @private
     */
    _render: function() {
        this.destroyTinyMCEEditor();

        this._super('_render');

        this._getHtmlEditableField().attr('data-name', this.name);

        // Hide the field for now. Once the field loads its contents completely, we will show it. This helps to prevent
        // a momentary white background/flash in the iframe before it finishes loading in dark mode
        this.hide();

        if (this._isEditView()) {
            this._renderEdit(this.def.tinyConfig || null);
        } else {
            this._renderView();
        }
    },

    /**
     * Populate the editor or textarea with the value from the model
     */
    bindDataChange: function() {
        this.model.on('change:' + this.name, function(model, value) {
            if (this._isEditView()) {
                this._saveOnSetContent = false; // the model already has the value being set, so don't set it again
                this.setEditorContent(value);
            } else {
                this.setViewContent(value);
            }
        }, this);
        // handle embedded images when saved
        this.model.on('validation:success', this.handleImageSave, this);
    },

    /**
     * Prepare content to show
     *
     * @param {string} value Sanitize HTML before addition to view
     * @private
     */
    sanitizeContent: function(value) {
        return DOMPurify.sanitize(value, {
            ADD_TAGS: ['iframe'],
            ADD_ATTR: ['frameborder', 'target'],
        });
    },

    /**
     * Determines if the iframe is loaded and has a body element
     *
     * @param {Object} editable A reference to a field jQuery object
     * @protected
     */
    _iframeHasBody: function(editable) {
        return editable.contents().length > 0 && editable.contents().find('body').length > 0;
    },

    /**
     * Sets the content displayed in the non-editor view
     *
     * @param {String} value Sanitized HTML to be placed in view
     * @param {string} styleSrc relative path to iframe_sugar.css
     */
    setViewContent: function(value, styleSrc = 'styleguide/assets/css/iframe-sugar.css') {
        var editable = this._getHtmlEditableField();
        var styleExists = false;
        var css = [];
        css.push(styleSrc);

        if (!editable) {
            return;
        }

        // Prepare content to show
        var sanitizedValue = this.sanitizeContent(value);

        if (this.view.module === 'Reports') {
            editable.html(sanitizedValue);
            this.$el.html(editable);
        }

        if (this._iframeHasBody(editable)) {
            // Only add the stylesheet that is sugar-specific while making sure not to add any duplicates
            editable.contents().find('link[rel="stylesheet"]').each(function() {
                if ($(this).attr('href') === styleSrc) {
                    styleExists = true;
                }
            });

            if (!styleExists) {
                _.each(document.styleSheets, function(style) {
                    if (style.href) {
                        css.push(style.href);
                    }
                });

                _.each(css, function(href) {
                    editable.contents().find('head').append($('<link/>', {
                        rel: 'stylesheet',
                        href: href,
                        type: 'text/css'
                    }));
                });
            }

            const body = editable.contents().find('body');
            if (body && _.isFunction(body.addClass)) {
                body.addClass(app.utils.isDarkMode() ? 'sugar-dark-theme' : 'sugar-light-theme')
                    .addClass('iframe-content');
            }

            var frame = _.find(editable, function(item) {
                return item.tagName === 'IFRAME';
            });
            if (frame && frame.contentWindow && frame.contentWindow.document && !_.isNull(value)) {
                frame.contentDocument.body.innerHTML = sanitizedValue;

                // Set the styling of the view mode based on the current sugar theme
                this._setViewContentThemeStyling(frame);

                // Show the field now that we have everything loaded (prevents the field flashing white in dark mode)
                this.show();
            }
        } else {
            // If the element has no body, the iframe hasn't loaded. Wait until it loads
            editable.on('load', _.bind(function() {
                this.setViewContent(value);
            }, this));
        }
    },

    /**
     * Sets the styling of the view mode iframe based on the current Sugar light/dark theme
     *
     * @param {Object} frame the iframe jQuery object
     * @private
     */
    _setViewContentThemeStyling: function(frame) {
        try {
            // Get the style variables of the current theme
            const themeClass = app.utils.isDarkMode() ? 'sugar-dark-theme' : 'sugar-light-theme';
            const themeElement = _.first(document.getElementsByClassName(themeClass));
            const styles = getComputedStyle(themeElement);

            // Apply the proper styles to the background and text color of the iframe
            frame.contentDocument.body.style.background = styles.getPropertyValue('--primary-content-background');
            frame.contentDocument.body.style.color = styles.getPropertyValue('--text-color');
        } catch (e) {
            frame.contentDocument.body.style.background = '#ffffff';
            frame.contentDocument.body.style.color = '#000000';
        }
    },

    /**
     * Render editor for edit view
     *
     * @param {Array} value TinyMCE config settings
     * @private
     */
    _renderEdit: function(options) {
        var self = this;
        this.initTinyMCEEditor(options);
        this._getHtmlEditableField().on('change', function(){
            self.model.set(self.name, self._getHtmlEditableField().val());
        });
    },

    /**
     * Render read-only view for other views
     *
     * @private
     */
    _renderView: function() {
        this.setViewContent(this.value);
    },

    /**
     * Is this an edit view?  If the field contains a textarea, it will assume that it's in an edit view.
     *
     * @return {Boolean}
     * @private
     */
    _isEditView: function() {
        return this.action === 'edit';
    },

    /**
     * Returns a default TinyMCE init configuration for the htmleditable widget.
     * This function can be overridden to provide a custom TinyMCE configuration.
     *
     * See [TinyMCE Configuration Documentation](http://www.tinymce.com/wiki.php/Configuration)for details.
     *
     * @return {Object} TinyMCE configuration to use with this widget
     */
    getTinyMCEConfig: function(){
        return {
            // Location of TinyMCE script
            script_url: 'include/javascript/tinymce6/tinymce.min.js?v=' + this.tinyMCEVersion,
            // Force loading of current version of tinyMCE plugin
            cache_suffix: '?v=' + this.tinyMCEVersion,

            // General options
            theme: 'silver',
            skin: app.utils.isDarkMode() ? 'oxide-dark' : 'oxide',
            content_css: [
                app.utils.isDarkMode() ? 'dark' : 'default',
                'styleguide/assets/css/sugar-theme-variables.css',
                'styleguide/assets/css/iframe-sugar.css',
            ],
            body_class: app.utils.isDarkMode() ? 'sugar-dark-theme' : 'sugar-light-theme',
            plugins: 'code,help,insertdatetime,table,charmap,' +
                'image,link,anchor,directionality,searchreplace,lists',
            browser_spellcheck: true,

            // User Interface options
            min_height: 250,
            menubar: false,
            statusbar: false,
            resize: false,
            toolbar_mode: 'wrap',
            toolbar: 'code | bold italic underline strikethrough | alignleft aligncenter alignright ' +
                'alignjustify | forecolor backcolor | fontfamily fontsize blocks | ' +
                'cut copy paste pastetext | search searchreplace | bullist numlist | ' +
                'outdent indent | ltr rtl | undo redo | link unlink anchor image | subscript ' +
                'superscript | charmap | table | hr removeformat | insertdatetime',
            // Sets the text of the Target element of the link plugin. To disable
            // this completely, set target_list: false
            link_target_list: [
                {
                    text: app.lang.getAppString('LBL_TINYMCE_TARGET_SAME'),
                    value: ''
                },
                {
                    text: app.lang.getAppString('LBL_TINYMCE_TARGET_NEW'),
                    value: '_blank'
                }
            ],

            // Output options
            entity_encoding: 'raw',

            // URL options
            relative_urls: false,
            convert_urls: false,

            // Insert image
            file_browser_callback: _.bind(this.tinyMCEFileBrowseCallback, this),

            // Allow image copy&paste
            paste_data_images: true,
            images_upload_handler: _.bind(this.tinyMCEImagePasteCallback, this)
        };
    },

    /**
     * Initializes the TinyMCE editor.
     *
     * @param {Object} optConfig Optional TinyMCE config to use when initializing editor.  If none provided, will load config provided from {@link getTinyMCEConfig}.
     */
    initTinyMCEEditor: function(optConfig) {
        var self = this;
        if(_.isEmpty(this._htmleditor)){
            var config = _.extend({}, this.getTinyMCEConfig(), optConfig || {});
            var __superSetup__ = config.setup;
            // Preserve custom setup if it exists, add setup function needed for widget to work properly
            config.setup = function(editor){
                if(_.isFunction(__superSetup__)){
                    __superSetup__.call(this, editor);
                }
                self._htmleditor = editor;
                self._htmleditor.on('init', function(event) {
                    self.setEditorContent(self.getFormattedValue());
                    $(event.target.getWin()).blur(function(e){ // Editor window lost focus, update model immediately
                        self._saveEditor(true);
                    });

                    // Show the field now that we have everything loaded (prevents a white flash in dark mode)
                    self.show();
                });
                self._htmleditor.on('deactivate', function(ed){
                    self._saveEditor();
                });
                self._htmleditor.on('change', function(ed, l) {
                    // Changes have been made, mark widget as dirty so we don't lose them
                    self._isDirty = true;
                });
                self._htmleditor.on('paste', function() {
                    // Some content has been pasted, mark widget as dirty so we don't lose pasted content.
                    self._isDirty = true;
                });
                self.addCustomButtons(editor);
            };
            config.oninit = function(inst) {
                self.context.trigger('tinymce:oninit', inst);
            };

            this._getHtmlEditableField().tinymce(config);
        }
    },

    /**
     * Add custom buttons.
     * @param {Object} editor TinyMCE editor
     */
    addCustomButtons: function(editor) {},

    /**
     * Destroy TinyMCE Editor instance
     */
    destroyTinyMCEEditor: function() {
        // Clean up existing TinyMCE editor
        if(!_.isNull(this._htmleditor)){
            // A known issue with Firefox and TinyMCE produces a NS_ERROR_UNEXPECTED Exception
            try {
                // Save Editor data only in case if Editor exists.
                if (this._htmleditor.getBody()) {
                    this._saveEditor(true);
                }

                this._htmleditor.remove();
                this._htmleditor.destroy();
            } catch (e) {
            }
            this._htmleditor = null;
        }
    },

    /**
     * Save the TinyMCE editor's contents to the model
     * @private
     */
    _saveEditor: function(force){
        var save = force | this._isDirty;
        if(save){
            this.model.set(this.name, this.getEditorContent(), {silent: true});
            this._isDirty = false;
        }
    },

    /**
     * Finds textarea or iframe element in the field template
     *
     * @return {HTMLElement} element from field template
     * @private
     */
    _getHtmlEditableField: function() {
        return this.$el.find(this.fieldSelector);
    },

    /**
     * Sets TinyMCE editor content
     *
     * @param {String} value HTML content to place into HTML editor body
     */
    setEditorContent: function(value) {
        if(_.isEmpty(value)){
            value = "";
        }
        if (this._isEditView() && this._htmleditor && this._htmleditor.dom) {
            this._htmleditor.setContent(value);
        }
        // setup embedded images
        this.handleEmbeddedImages(value);
    },

    /**
     * Retrieves the  TinyMCE editor content
     *
     * @return {String} content from the editor
     */
    getEditorContent: function() {
        return this._htmleditor.getContent({format: 'raw'});
    },

    /**
     * Get the content height of the field's iframe.
     *
     * @private
     * @return {number} Returns 0 if the iframe isn't found.
     */
    _getContentHeight: function() {
        var editable = this._getHtmlEditableField();

        if (this._iframeHasBody(editable)) {
            return editable.contents().find('body')[0].offsetHeight;
        }

        return 0;
    },

    /**
     * Destroy TinyMCE Editor on dispose
     *
     * @private
     */
    _dispose: function() {
        this.destroyTinyMCEEditor();
        app.view.Field.prototype._dispose.call(this);
    }

})
