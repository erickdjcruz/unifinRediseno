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
 * @class View.Views.Base.AdministrationPortalThemeConfigView
 * @alias SUGAR.App.view.views.BasePortalThemeConfigView
 * @extends View.Views.Base.AdministrationConfigView
 */
({
    extendsFrom: 'AdministrationConfigView',

    enableHeaderButtons: false,

    enableHeaderPane: false,

    /**
     * Sell & Serve specific text blocks
     */
    sellServeLicencedTextBlocks: [
        'portaltheme_open_aws_settings_link'
    ],

    events: {
        'click .restore-defaults-btn': 'restoreClicked',
        'click a.open-aws-settings-btn': 'openAwsSettingsChatTab'
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        this.displaySellServeLicensedTextBlocks();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        this.model.on('change', this.handleModelChange, this);
    },

    /**
     * @inheritdoc
     */
    loadSettingsSuccessCallback: function(settings) {
        this._super('loadSettingsSuccessCallback', [settings]);

        this._bindFieldEvents();

        // execute field change functions once on load with default or saved data
        this.handleBannerBackgroundStyleChange();
    },

    /**
     * Bind field events
     *
     * @private
     */
    _bindFieldEvents: function() {
        this.model.on(
            'change:portaltheme_banner_background_style',
            this.handleBannerBackgroundStyleChange,
            this
        );
    },

    /**
     * Handle the changing of portaltheme_banner_background_style field and
     * properly display associated fields
     */
    handleBannerBackgroundStyleChange: function() {
        var backgroundStyleField = this.getField('portaltheme_banner_background_style');

        if (!backgroundStyleField) {
            return;
        }

        var value = backgroundStyleField.getFormattedValue();
        var shouldShowBackgroundColorField;
        var shouldShowBackgroundImageField;
        var backgroundColorField = this.getField('portaltheme_banner_background_color');
        var backgroundImageField = this.getField('portaltheme_banner_background_image');

        switch (value) {
            case 'color':
                shouldShowBackgroundColorField = true;
                shouldShowBackgroundImageField = false;
                break;
            case 'image':
                shouldShowBackgroundColorField = false;
                shouldShowBackgroundImageField = true;
                break;
            default:
                shouldShowBackgroundColorField = shouldShowBackgroundImageField = false;
                break;
        }

        this._toggleFieldVisibility(backgroundColorField, shouldShowBackgroundColorField);
        this._toggleFieldVisibility(backgroundImageField, shouldShowBackgroundImageField);
    },

    /**
     * @inheritdoc
     * @override
     *
     * Always place labels on top
     */
    getLabelPlacement: function() {
        return true;
    },

    /**
     * If the user is licensed for Sell or Serve, remove the 'hide' class from specific text blocks
     */
    displaySellServeLicensedTextBlocks: function() {
        if (!app.user.hasSellServeLicense()) {
            return;
        }

        _.each(this.sellServeLicencedTextBlocks, function(name) {
            var $text = this.$el.find('[data-name=' + name + ']');
            $text.removeClass('hide');
        }, this);
    },

    /**
     * Set 'Sugar Portal Chat' tab as 'active' in
     * 'Amazon Connect Settings' Administration page
     */
    openAwsSettingsChatTab: function() {
        app.user.lastState.set(
            app.user.lastState.key('activeTab', this), '#panel_2'
        );
    },

    /**
     * @inheritdoc
     */
    toggleHeaderButton: function(state) {
        var header = this.layout.layout._components[0].getComponent(this.name + '-header');
        if (header) {
            header.enableButton(state);
        }
    },

    /**
     * Handles the change event from the fields
     */
    handleModelChange: function() {
        _.each(this.model.changed, function(value, key) {
            var field = this.getField(key);
            var data = this.getPreviewContextData(field, value);

            // no preview definition defined for field
            if (_.isEmpty(data.preview_components)) {
                return;
            }

            this.triggerPreview(data);
        }, this);
    },

    /**
     * Triggers 'portal:config:preview' on the layout context and let
     * the layout component react to this event
     *
     * @param data
     */
    triggerPreview: function(data) {
        var context = this.layout.context;

        if (context && context.get('config-layout')) {
            context.trigger('portal:config:preview', data);
        }
    },

    /**
     * Get the preview_components definition from field metadata
     *
     * @param field
     * @return []
     */
    getPreviewComponentsDef: function(field) {
        var def = [];

        if (field && field.def && field.def.preview_components) {
            def = field.def.preview_components;
        }

        return def;
    },

    /**
     * Get the prepared context data
     *
     * @param field
     * @param value
     * @return {Object} the prepared context data
     */
    getPreviewContextData: function(field, value) {
        return {
            preview_components: this.getPreviewComponentsDef(field),
            preview_data: value
        };
    },

    /**
     * Unbind field events
     *
     * @private
     */
    _unbindFieldEvents: function() {
        this.model.off(
            'change:portaltheme_banner_background_style',
            this.handleBannerBackgroundStyleChange,
            this
        );
    },

    /*
    * Check if the field is named and has a default value set.
     *
     * @param {Object} field A field from the current view.
     * @return {boolean} True or false.
     */
    hasDefaultValue: function(field) {
        var value = field.def.default;
        var isDefined = !_.isUndefined(value) && !_.isNull(value) && !_.isNaN(value);
        return !!field.name && isDefined;
    },

    /**
     * It will set the field to its default value from metadata.
     * Some field types require a render to display the value correctly.
     *
     * @param {Object} field A field from the current view.
     */
    resetFieldToDefault: function(field) {
        if (field.name) {
            var defaultValue = '';

            if (this.hasDefaultValue(field)) {
                switch (field.type) {
                    case 'text':
                        defaultValue = app.lang.get(field.def.default, field.module);
                        break;
                    default:
                        defaultValue = field.def.default;
                }
            }

            field.model.set(field.name, defaultValue);
            if (field.type === 'image-url') {
                // the previous set generates the default css,
                // but we must clear the inputs too
                // this will not alter the css file
                field.model.set(field.name, '');
            }
            field.render();

            this.context.trigger('portal:config:preview', {preview_components: [field.def]});
        }
    },

    /**
     * Restore default settings click handler.
     */
    restoreClicked: function() {
        app.alert.show('restore_default_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_RESTORE_DEFAULT_PORTAL_CONFIG_CONFIRM', 'Administration'),
            onConfirm: _.bind(function() {
                this.restoreFields();
            }, this)
        });
    },

    /**
     * Restore all fields on portaltheme-config
     */
    restoreFields: function() {
        _.each(this.fields, this.resetFieldToDefault, this);
    },

    /**
    * @inheritdoc
    */
    _dispose: function() {
        this._unbindFieldEvents();
        this.model.off('change', this.handleModelChange, this);
        this._super('_dispose');
    }
})
