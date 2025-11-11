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
 * @class View.Fields.Base.AvatarField
 * @alias SUGAR.App.view.fields.BaseAvatarField
 * @extends View.Fields.Base.ImageField
 */
({
    extendsFrom: 'ImageField',

    plugins: ['File', 'FieldDuplicate'],

    MAPSIZECLASS: {
        'large': 'label-module-lg',
        'medium': 'label-module-md',
        'button': 'label-module-btn',
        'default': '',  //This field does not fallback to this size
        'small': 'label-module-sm',
        'mini': 'label-module-mini'
    },

    /**
     * @override
     * @private
     */
    _render: function() {
        this._super("_render");
        if (this.action !== 'edit' ||
            this.view.name === 'merge-duplicates' ||
            this.view.name === 'side-drawer-headerpane') {
            if (_.isEmpty(this.value)) {
                // Replace the image field with the module icon when there is
                // no avatar to display
                let template = app.template.getField(this.type, 'module-icon', this.module);
                const iconMeta = this._getModuleIconMeta();
                if (template && iconMeta) {
                    this.$('.image_field').replaceWith(template(iconMeta));
                }
            } else {
                // add the image_rounded class to the image_field div when there is an avatar to display
                this.$('.image_field').addClass('image_rounded');
            }
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
        let moduleMeta = app.metadata.getModule(this.module);
        if (!moduleMeta) {
            return;
        }

        let classes = `label-module-color-${moduleMeta.color}`;
        let content = '';

        if (moduleMeta.display_type === 'abbreviation') {
            content = app.lang.getModuleIconLabel(this.module);
        } else {
            classes += ` sicon ${moduleMeta.icon}`;
        }

        return {
            module: this.module,
            labelSizeClass: this.MAPSIZECLASS[this.def.size] || this.MAPSIZECLASS.large,
            tooltipPlacement: app.lang.direction === 'ltr' ? 'right' : 'left',
            content: content,
            classes: classes
        };
    },

    /**
     * To inherit templates from the image field, we want to tell sidecar to load the templates from the image field's
     * directory. To do this, we must change this.type to "image" temporarily. We want to restore this.type before
     * exiting, however, so that we don't really change the field's attributes.
     *
     * Beware that this causes sidecar to never automatically load any templates found in the avatar field's directory.
     * Sidecar will always look for templates in the image field's directory, by default.
     *
     * @override
     * @private
     */
    _loadTemplate: function() {
        this.type = 'image';
        this._super("_loadTemplate");
        this.type = this.def.type;
    }
})
