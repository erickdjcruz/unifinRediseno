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
 * Action button properties view
 *
 * @class View.Views.Base.AdministrationActionbuttonPropertiesView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonPropertiesView
 * @extends View.View
 */
({
    events: {
        'input .ab-admin-button-property input[type=text]': 'textPropChanged',
        'change .ab-admin-button-property input[type=checkbox]': 'boolPropChanged',
        'change .ab-admin-button-property select': 'enumPropChanged',
        'click .btn.btn-invisible': 'panelCollapsedChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this.properties = {};
        this.buttonData = this._getActiveButtonData();
        this._formulaBuilder = false;
    },

    /**
     * Register context event handlers
     *
     */
    _registerEvents: function() {
        this.listenTo(this.context.get('model'), 'update:button:view', this.changeProperties, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.properties.isDependent.value) {
            this.$('div[data-container="formula"]').show();
            this._createFormulaBuilder();
        } else {
            this.$('div[data-container="formula"]').hide();
        }

        _.each(this.properties, function createSelect2(data, propId) {
            if (data.options) {
                this.$('select[data-id="' + propId + '"]').select2(this._getOptions(data));
            }
        }, this);
    },

    /**
     * Generic text property change handler
     *
     * @param {UIEvent} e
     *
     */
    textPropChanged: function(e) {
        var propertyId = $(e.currentTarget).data('id');

        this.properties[propertyId].value = e.currentTarget.value;
        this._updateActionButtons();

        this.context.get('model').trigger('refresh:ui');
    },

    /**
     * Generic checkbox property change handler
     *
     * @param {UIEvent} e
     *
     */
    boolPropChanged: function(e) {
        var propertyId = $(e.currentTarget).data('id');

        this.properties[propertyId].value = e.currentTarget.checked;
        this._updateActionButtons();

        if (propertyId === 'isDependent') {
            this.render();
        }
    },

    /**
     * Generic list property change handler
     *
     * @param {UIEvent} e
     *
     */
    enumPropChanged: function(e) {
        var propertyId = $(e.currentTarget).data('id');

        this.properties[propertyId].value = e.currentTarget.value;
        this._updateActionButtons();
    },

    /**
     * Handler for panel collapse
     *
     * @param {UIEvent} e
     *
     */
    panelCollapsedChanged: function(e) {
        const $el = $(e.currentTarget);
        const isCollapsed = $el.hasClass('collapsed');
        let $icon = $el.find('i');

        $icon.toggleClass('sicon-chevron-down', isCollapsed);
        $icon.toggleClass('sicon-chevron-right', !isCollapsed);
    },

    /**
     * Some basic validation of properties
     *
     * @return {bool}
     */
    canSave: function() {
        if (!(app.utils.isTruthy(this.buttonData.properties.showIcon) ||
            app.utils.isTruthy(this.buttonData.properties.showLabel))) {
            app.alert.show('validation-error', {
                level: 'error',
                title: app.lang.get('LBL_ACTIONBUTTON_VALIDATION_ERROR'),
                messages: ['LBL_ACTIONBUTTON_VALIDATION_ERROR_NEED_LABEL_OR_ICON'],
                autoClose: true
            });

            return false;
        }

        if (app.utils.isTruthy(this.buttonData.properties.showLabel) &&
            _.isEmpty((this.buttonData.properties.label || '').trim())) {
            app.alert.show('validation-error', {
                level: 'error',
                title: app.lang.get('LBL_ACTIONBUTTON_VALIDATION_ERROR'),
                messages: ['LBL_ACTIONBUTTON_VALIDATION_ERROR_NEED_LABEL'],
                autoClose: true
            });

            return false;
        }

        if (this.properties.isDependent.value) {
            return this._formulaBuilder.isValid();
        }

        return true;
    },

    /**
     * Handler for formula change
     *
     * @param {string} formula
     *
     */
    formulaChanged: function(formula) {
        this._formula = formula;
        this._updateActionButtons();
    },

    /**
     * Update button proerties
     *
     * @param {string} buttonId
     *
     */
    changeProperties: function(buttonId) {
        this.buttonData = this._getActiveButtonData();
        this.render();
    },

    /**
     * Build select2 control options
     *
     * @param {Object} data
     *
     * @return {Object}
     */
    _getOptions: function(data) {
        var select2Options = {
            dropdownCssClass: 'ab-admin-select-icon'
        };

        if (data.formatOptions) {
            select2Options.formatResult = data.formatOptions.bind(this);
        }

        return select2Options;
    },

    /**
     * Create the formula builder sidecar field
     *
     */
    _createFormulaBuilder: function() {
        this._disposeFormulaBuilder();

        this._formulaBuilder = app.view.createField({
            def: {
                type: 'formula-builder',
                name: 'ABCustomAction'
            },
            view: this,
            viewName: 'edit',
            targetModule: this.options.context.get('model').get('module'),
            returnType: 'boolean',
            callback: this.formulaChanged.bind(this),
            formula: this._formula
        });

        this._formulaBuilder.render();

        this.$('span[data-fieldname="formula"]').append(this._formulaBuilder.$el);
    },

    /**
     * Update Action buttons configuration
     *
     */
    _updateActionButtons: function() {
        var ctxModel = this.context.get('model');
        var buttonsData = ctxModel.get('data');
        var updatedProps = {};

        _.each(this.properties, function updateProps(property, name) {
            updatedProps[name] = property.value;
        });

        updatedProps.formula = this._formula;

        buttonsData.buttons[this.buttonData.buttonId].properties = updatedProps;

        ctxModel.set('data', buttonsData);
        this.context.trigger('update-buttons-preview', buttonsData);
    },

    /**
     * Return action button configuration
     *
     * @return {Array}
     */
    _getActiveButtonData: function() {
        var buttons = this.context.get('model').get('data').buttons;
        var activeButton = _.filter(buttons, function getActiveButtonData(buttonData) {
            return buttonData.active === true;
        })[0];

        this._updateProperties(activeButton);

        return activeButton;
    },

    /**
     * Build action button properties
     *
     * @param {Object} activeButton
     *
     * @return {undefined}
     */
    _updateProperties: function(activeButton) {
        this._formula = activeButton.properties.formula;

        if (activeButton.properties.label && activeButton.properties.label.includes('LBL_')) {
            const module = this.context.get('model').get('module');
            activeButton.properties.label = app.lang.get(activeButton.properties.label, module);
        }

        this.properties = {
            label: {
                template: 'actionbutton-text-property',
                label: 'LBL_ACTIONBUTTON_LABEL_TITLE',
                id: 'label',
                value: activeButton.properties.label,
            },
            description: {
                template: 'actionbutton-text-property',
                label: 'LBL_ACTIONBUTTON_DESC',
                id: 'description',
                value: activeButton.properties.description,
            },
            showLabel: {
                template: 'actionbutton-bool-property',
                label: 'LBL_ACTIONBUTTON_SHOW_LABEL',
                id: 'showLabel',
                value: app.utils.isTruthy(activeButton.properties.showLabel),
            },
            showIcon: {
                template: 'actionbutton-bool-property',
                label: 'LBL_ACTIONBUTTON_SHOW_ICON',
                id: 'showIcon',
                value: app.utils.isTruthy(activeButton.properties.showIcon),
            },
            colorScheme: {
                template: 'actionbutton-enum-property',
                label: 'LBL_ACTIONBUTTON_SCHEME',
                id: 'colorScheme',
                value: activeButton.properties.colorScheme,
                options: {
                    primary: app.lang.get('LBL_ACTIONBUTTON_THEME_PRIMARY'),
                    secondary: app.lang.get('LBL_ACTIONBUTTON_THEME_SECONDARY'),
                    highViz: app.lang.get('LBL_ACTIONBUTTON_THEME_HIGHVIZ'),
                    ocean: app.lang.get('LBL_ACTIONBUTTON_THEME_OCEAN'),
                    pacific: app.lang.get('LBL_ACTIONBUTTON_THEME_PACIFIC'),
                    teal: app.lang.get('LBL_ACTIONBUTTON_THEME_TEAL'),
                    green: app.lang.get('LBL_ACTIONBUTTON_THEME_GREEN'),
                    army: app.lang.get('LBL_ACTIONBUTTON_THEME_ARMY'),
                    yellow: app.lang.get('LBL_ACTIONBUTTON_THEME_YELLOW'),
                    orange: app.lang.get('LBL_ACTIONBUTTON_THEME_ORANGE'),
                    red: app.lang.get('LBL_ACTIONBUTTON_THEME_RED'),
                    coral: app.lang.get('LBL_ACTIONBUTTON_THEME_CORAL'),
                    pink: app.lang.get('LBL_ACTIONBUTTON_THEME_PINK'),
                    purple: app.lang.get('LBL_ACTIONBUTTON_THEME_PURPLE')
                }
            },
            icon: {
                template: 'actionbutton-enum-property',
                label: 'LBL_ACTIONBUTTON_ICON',
                id: 'icon',
                useIcon: true,
                value: activeButton.properties.icon,
                formatOptions: function(option) {
                    return '<div><i class="sicon ' + option.text + '"></i>' + option.text + '</div>';
                },
                options: {}
            },
            isDependent: {
                template: 'actionbutton-bool-property',
                label: 'LBL_ACTIONBUTTON_IS_DEPENDENT',
                id: 'isDependent',
                value: app.utils.isTruthy(activeButton.properties.isDependent),
            },
            stopOnError: {
                template: 'actionbutton-bool-property',
                label: 'LBL_ACTIONBUTTON_STOP_ON_ERROR',
                id: 'stopOnError',
                value: app.utils.isTruthy(activeButton.properties.stopOnError),
            },
        };

        this.properties.icon.options = {
            'sicon-arrow-down': 'sicon-arrow-down',
            'sicon-chevron-down': 'sicon-chevron-down',
            'sicon-chevron-left': 'sicon-chevron-left',
            'sicon-chevron-right': 'sicon-chevron-right',
            'sicon-check': 'sicon-check',
            'sicon-clock': 'sicon-clock',
            'sicon-dashboard-default': 'sicon-dashboard-default',
            'sicon-dashboard': 'sicon-dashboard',
            'sicon-edit': 'sicon-edit',
            'sicon-caret-down': 'sicon-caret-down',
            'sicon-folder': 'sicon-folder',
            'sicon-info': 'sicon-info',
            'sicon-kebab': 'sicon-kebab',
            'sicon-link': 'sicon-link',
            'sicon-list': 'sicon-list',
            'sicon-logout': 'sicon-logout',
            'sicon-minus': 'sicon-minus',
            'sicon-folder-open': 'sicon-folder-open',
            'sicon-plus-sm': 'sicon-plus-sm',
            'sicon-refresh': 'sicon-refresh',
            'sicon-plus': 'sicon-plus',
            'sicon-arrow-up': 'sicon-arrow-up',
            'sicon-settings': 'sicon-settings',
            'sicon-arrow-right-double': 'sicon-arrow-right-double',
            'sicon-reports': 'sicon-reports',
            'sicon-user': 'sicon-user',
            'sicon-upload': 'sicon-upload',
            'sicon-user-group': 'sicon-user-group',
            'sicon-arrow-left-double': 'sicon-arrow-left-double',
            'sicon-chevron-up': 'sicon-chevron-up',
            'sicon-remove': 'sicon-remove',
            'sicon-caret-up': 'sicon-caret-up',
            'sicon-star-fill': 'sicon-star-fill',
            'sicon-download': 'sicon-download',
            'sicon-close': 'sicon-close',
            'sicon-tile-view': 'sicon-tile-view',
            'sicon-list-view': 'sicon-list-view',
            'sicon-thumbs-down': 'sicon-thumbs-down',
            'sicon-warning-circle': 'sicon-warning-circle',
            'sicon-phone': 'sicon-phone',
            'sicon-email': 'sicon-email',
            'sicon-document': 'sicon-document',
            'sicon-note': 'sicon-note',
            'sicon-preview': 'sicon-preview',
            'sicon-copy': 'sicon-copy',
            'sicon-launch': 'sicon-launch',
            'sicon-mask': 'sicon-mask',
            'sicon-lock': 'sicon-lock',
            'sicon-arrow-top-right': 'sicon-arrow-top-right',
            'sicon-full-screen': 'sicon-full-screen',
            'sicon-full-screen-exit': 'sicon-full-screen-exit',
            'sicon-expand-left': 'sicon-expand-left',
            'sicon-expand-right': 'sicon-expand-right',
            'sicon-focus-drawer': 'sicon-focus-drawer',
            'sicon-ban': 'sicon-ban',
            'sicon-thumbs-up': 'sicon-thumbs-up',
            'sicon-search': 'sicon-search',
            'sicon-calendar': 'sicon-calendar',
            'sicon-calendar-lg': 'sicon-calendar-lg',
            'sicon-mobile-lg': 'sicon-mobile-lg',
            'sicon-star-fill-lg': 'sicon-star-fill-lg',
            'sicon-star-outline-lg': 'sicon-star-outline-lg',
            'sicon-refresh-lg': 'sicon-refresh-lg',
            'sicon-exchange-lg': 'sicon-exchange-lg',
            'sicon-help-lg': 'sicon-help-lg',
            'sicon-close-lg': 'sicon-close-lg',
            'sicon-plus-lg': 'sicon-plus-lg',
            'sicon-shortcuts-lg': 'sicon-shortcuts-lg',
            'sicon-search-lg': 'sicon-search-lg',
            'sicon-phone-lg': 'sicon-phone-lg',
            'sicon-email-lg': 'sicon-email-lg',
            'sicon-note-lg': 'sicon-note-lg',
            'sicon-document-lg': 'sicon-document-lg',
            'sicon-add-dashlet-lg': 'sicon-add-dashlet-lg',
            'sicon-collapse-lg': 'sicon-collapse-lg',
            'sicon-hamburger-lg': 'sicon-hamburger-lg',
            'sicon-pin-lg': 'sicon-pin-lg',
            'sicon-expand-lg': 'sicon-expand-lg',
            'sicon-copy-lg': 'sicon-copy-lg',
            'sicon-dashboard-lg': 'sicon-dashboard-lg',
            'sicon-trash-lg': 'sicon-trash-lg',
            'sicon-star-outline': 'sicon-star-outline'
        };
    },

    /**
     * Clear out formula builder field wrapper contents and dispose the sidecar component
     *
     */
    _disposeFormulaBuilder: function() {
        this.$('span[data-fieldname="formula"]').empty();

        if (this._formulaBuilder) {
            this._formulaBuilder.dispose();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeFormulaBuilder();

        this._super('_dispose');
    },
});
