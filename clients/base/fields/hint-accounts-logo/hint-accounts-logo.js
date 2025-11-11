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
({
    extendsFrom: 'ImageField',

    plugins: ['MetadataEventDriven', 'Stage2CssLoader'],

    events: {
        'click .image_preview': 'addDeleteImageLabel',
        'click .image_btn_label': 'deleteImage',
    },

    activeClass: 'hint-accounts-logo--record-view',

    deleteImageLabel: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        // When the delete label is active and the user clicks anywhere outside of the label, we want
        // to hide it. This event is to track that so that the label does not persist.
        window.document.addEventListener('mousedown', function(event) {
            var container = $('.image_btn_label');
            if (!container.is(event.target) && container.has(event.target).length === 0) {
                $('.image_btn_label').hide();
            }
        }, false);
    },

    /**
     * Format value
     *
     * @param {string} value
     * @return {string}
     */
    format: function(value) {
        return value;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.model.fileField = this.name;
        this._super('_render');
        if (_.isEmpty(this.value)) {
            var template = app.template.getField(this.type, 'module-icon', this._getModuleName());
            if (template) {
                this.$('.image_field').replaceWith(template(this._getModuleIconMeta()));
            }
        } else {
            var layout = app.controller.layout.name;
            if (layout === 'record') {
                this.$('.image_field').addClass(this.activeClass);
            }
            //Resize widget once the image is loaded
            this.$('img').addClass('hide').on('load', $.proxy(this.resizeWidget, this));
        }
        return this;
    },

    /**
     * Gets the metadata to pass to the module-icon template based on the
     * module settings
     *
     * @return {Object} the set of module-icon template metadata
     * @private
     */
    _getModuleIconMeta: function() {
        let module = this._getModuleName();
        let moduleMeta = app.metadata.getModule(module);
        let classes = `label-module-color-${moduleMeta.color}`;
        let content = '';

        if (moduleMeta.display_type === 'abbreviation') {
            content = app.lang.getModuleIconLabel(this.module);
        } else {
            classes += ` sicon ${moduleMeta.icon}`;
        }

        return {
            module: module,
            labelSizeClass: 'label-module-lg',
            tooltipPlacement: app.lang.direction === 'ltr' ? 'right' : 'left',
            content: content,
            classes: classes
        };
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        var self = this;
        this.model.on('change', function() {
            self.render();
        }, this);
    },

    /**
     * Get module name for history-summary
     *
     * @return {string}
     */
    _getModuleName: function() {
        if (this.view.name === 'history-summary') {
            return this.model.get('_module');
        }
        return this.module;
    },

    /**
     * Delete hit account picture
     *
     * @param {Object} e
     */
    deleteImage: function(e) {
        if (this.model.get('hint_account_pic')) {
            this._warningAlertForLogoOverwrite();
        }
    },

    /**
     * Add delete image label
     */
    addDeleteImageLabel: function() {
        this.deleteImageLabel = true;
        this.render();
    },

    /**
     * Warning for hint account picture deletion
     */
    _warningAlertForLogoOverwrite: function() {
        var confirmMessage = app.lang.get('LBL_IMAGE_DELETE_CONFIRM', self.module);
        if (confirm(confirmMessage)) {
            this.model.save('hint_account_pic', '');
            this.deleteImageLabel = false;
            this.render();
        }
    }
});
