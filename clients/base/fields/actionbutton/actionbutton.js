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
 * @class View.Fields.Base.ActionbuttonField
 * @alias SUGAR.App.view.fields.BaseActionbuttonField
 * @extends View.Fields.Base.BaseField
 */
({
    plugins: ['ActionRunner', 'LinkedModel'],

    events: {
        'click .actionbuttons': 'onClick',
    },

    actionMeta: {},
    activeButtonMeta: {
        buttons: {},
        settings: {},
    },

    loadingPlaceholderActionIdExt: '_loading',

    className: 'actionbutton-wrapper',
    supportedViews: [
        'record',
        'recordlist',
        'subpanel-list',
        'dashlet-toolbar',
        'dashablerecord',
        'preview',
        'actionbutton-preview-record',
        'actionbutton-preview-action-menu',
        'omnichannel-config',
        'omnichannel-detail',
        'config-preview',
        'side-drawer-headerpane',
    ],
    _calculateDependencyOnDemandViews: [
        'record',
        'recordlist',
        'dashlet-toolbar',
        'subpanel-list',
        'side-drawer-headerpane',
    ],

    /**
     * Initializes the ActionButton field.
     *
     * Unencodes, unserializes and resolves the field options/meta.
     *
     * @param {Object} options
     *
     */
    initialize: function(options) {
        /**
         * @inheritdoc
         *
         * This field doesn't support `showNoData`.
        */
        this.showNoData = false;
        this._dependencyCalculated = false;
        this._previousButtonState = false;
        this._initialViewName = options.viewName;
        this._initialTplName = this.tplName;

        this._super('initialize', [options]);

        if (this._isSugarLive() && this.view.type === 'config-preview') {
            this._setDisabledMode('disabled');

            return;
        }

        if (this.view.type === 'dashlet-toolbar') {
            this._initDashableActionButton();
        }

        if (this.view && _.contains(this.supportedViews, this.view.type)) {
            let encodedData = {};

            if (!_.has(options, 'def') || !_.has(options.def, 'options')) {
                encodedData = options.model.fields[options.def.name].options;
            } else {
                encodedData = options.def.options;
            }

            this.actionMeta = this.parseButtonMeta(encodedData);

            this._resolveActiveButtonMeta();
            this._registerEvents();
        }
    },

    /**
     * We have to change the view and model if we are on a dashable toolbar
     */
    _initDashableActionButton() {
        const dashableRecord = this.view.layout.getComponent('dashablerecord');

        this.model = dashableRecord.model;
    },

    /**
     * @inheritdoc
     */
    setMode(mode) {
        this._super('setMode', [mode]);

        if (!this._isSugarLive() ||
            !this.view.type === 'config-preview' ||
            !this.view.type === 'omnichannel-detail' ||
            this._previousButtonState === mode) {
            return;
        }

        this._setDisabledMode(mode);
        this.render();
    },

    /**
     * Check if we are in a OmniChannel layout.
     *
     * @return {boolean}
     */
    _isSugarLive() {
        if ((this.view && this.view.layout && this.view.layout.type.indexOf('omni') === 0) ||
            (_.has(this.view, 'layout') && _.has(this.view.layout, 'module') &&
            this.view.layout.module === 'SugarLive')) {
            return true;
        }

        return false;
    },

    /**
     * Change tpl to disabled
     *
     * @param {string} mode
     */
    _setDisabledMode(mode) {
        const disabledState = 'disabled';
        this._previousButtonState = mode;

        if (mode === disabledState) {
            this._initialViewName = this.options.viewName;
            this._initialTplName = this.tplName;

            this.tplName = disabledState;
            this.options.viewName = disabledState;
        } else {
            this.options.viewName = this._initialViewName;
            this.tplName = this._initialTplName;
        }
    },

    /**
     *
     * Calculate button visibility values and re-renders the component.
     *
     * @param {Data.Bean} model
     *
     */
    _resolveFieldDependency(model, skipRender) {
        if (this.$el === undefined) {
            return;
        }

        this._dependencyCalculated = true;
        this._resolveActiveButtonMeta();

        if (!skipRender) {
            this.render();
        }

        let computedFieldsMeta = {};
        let buttonFieldNameMapping = {};

        _.each(this.actionMeta.buttons, function fieldsEach(item, itemKey) {
            if (item.properties.isDependent) {
                const fieldName = app.utils.generateUUID();
                const formula = item.properties.formula;
                const isCalculated = true;

                buttonFieldNameMapping[fieldName] = {
                    id: item.buttonId,
                    isDropDown: false
                };

                if (this.actionMeta.settings.type === 'dropdown' && item.orderNumber === 0) {
                    buttonFieldNameMapping[fieldName].isDropDown = true;
                }

                computedFieldsMeta[fieldName] = {
                    fieldName: fieldName,
                    formula: formula,
                    isCalculated: isCalculated,
                };
            }
        }, this);

        if (this._hasDependentFields()) {
            this._resolveDependency(computedFieldsMeta, buttonFieldNameMapping);
        }
    },

    /**
     * Checks wether we have dependent fields
     *
     * @return {Bool}
     */
    _hasDependentFields() {
        const noCalculatedFields = _.filter(this.actionMeta.buttons, function search(button) {
            return button.properties.isDependent;
        }).length;

        return noCalculatedFields > 0;
    },

    /**
     * Listening to external events
     */
    _registerEvents: function() {
        this.listenTo(this.model, 'sync', this._resolveFieldDependency);

        if (this.view) {
            this.listenTo(this.view, 'editable:toggle_fields', function toggleActionButton(fields, viewName) {
                // This looks like it makes no sense, however the actionbutton field type is set as readonly
                // via metadata/studio, that means that it will not toggle edit/detail mode like the other fields
                // which is something that we need to enable the "Hide Label on Edit" functionality
                // Therefore we need to hook into the editable:toggle_fields event and implement this functionality
                // We only currently support edit/detail templates, that is why we're checking against the two
                // rather than just doing a this.setMode(viewName)
                this.options.def.readonly = false;

                if (viewName === 'edit' || viewName === 'detail') {
                    this.setMode(viewName);
                }
            }, this);
        }
    },

    /**
     * Flag buttons that have a visibility dependency
     *
     */
    _resolveActiveButtonMeta() {
        this.activeButtonMeta = {
            buttons: {},
            settings: this.actionMeta.settings
        };

        this.activeButtonMeta.settings.hasCalculatedButtons = false;

        _.each(this.actionMeta.buttons, function fieldsEach(item, itemKey) {
            var activeItem = Object.assign({}, item);

            let isDependent = activeItem.properties.isDependent;

            activeItem.properties.hasBeenCalculated = !isDependent;

            if (this.view.type === 'config-preview') {
                activeItem.properties.hasBeenCalculated = false;
                this.activeButtonMeta.settings.hasCalculatedButtons = true;
            }

            if (activeItem.properties.label && activeItem.properties.label.includes('LBL_')) {
                const currentModel = this._getCurrentModel();
                const module = (currentModel && currentModel.get('_module')) || this.module;
                activeItem.properties.label = app.lang.get(activeItem.properties.label, module);
            }

            this.activeButtonMeta.buttons[itemKey] = activeItem;

            if (isDependent) {
                this.activeButtonMeta.settings.hasCalculatedButtons = true;
            }
        }, this);

        const fromCalculatedValue = false;

        this._reorderActionButton(fromCalculatedValue);
    },

    /**
     * We need to make sure that the actionbutton field has access to both the edit and detail views
     *
     * @inheritdoc
     */
    _checkAccessToAction: function(actionName) {
        if (actionName === 'edit' || actionName === 'detail') {
            return true;
        }

        return this._super('_checkAccessToAction', [actionName]);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this._shouldCalculateOnRender() && this._hasDependentFields() && !this._dependencyCalculated) {
            this._resolveFieldDependency(this.model, true);
        } else {
            this._buildUIButtonsData();
        }

        this._super('_render');

        if (this.view && this.view.action === 'create') {
            // We do not support action buttons in create view
            this._hideShowFieldLabel(true);
        }
    },

    /**
     * If the button is part of a certain view, we calculate the dependency on render
     *
     * @return {Bool}
     */
    _shouldCalculateOnRender() {
        return _.contains(this._calculateDependencyOnDemandViews, this.view.type);
    },

    /**
     * Reorder buttons after visibility dependencies are applied
     *
     * @param {Bool} fromCalculatedValue
     *
     */
    _reorderActionButton(fromCalculatedValue) {
        let minOrderNumber = 999;
        let buttonId = null;

        _.each(this.activeButtonMeta.buttons, function reorder(item, key) {
            if (item.orderNumber < minOrderNumber) {
                minOrderNumber = item.orderNumber;
                buttonId = key;
            }
        });

        if (_.has(this.activeButtonMeta.buttons, buttonId)) {
            this.activeButtonMeta.buttons[buttonId].orderNumber = 0;
        }

        this._handleFieldLabel(fromCalculatedValue);
        this.setDropDownCaretTheme(this.activeButtonMeta);
    },

    /**
     * Build ordered buttons list
     */
    _buildUIButtonsData: function() {
        if (this.activeButtonMeta && this.activeButtonMeta.buttons) {
            this.activeButtonMeta.uiButtons = _.sortBy(this.activeButtonMeta.buttons, 'orderNumber');
        }
    },

    /**
     * Handle field label visibility
     *
     * @param {Bool} fromCalculatedValue
     *
     */
    _handleFieldLabel(fromCalculatedValue) {
        const buttonNr = Object.keys(this.activeButtonMeta.buttons).length;
        const showFieldLabelSetting = this.activeButtonMeta.settings.showFieldLabel;

        let hideLabel = true;

        if (!showFieldLabelSetting) {
            this._hideShowFieldLabel(hideLabel);
        } else if (fromCalculatedValue && buttonNr === 0) {
            this._hideShowFieldLabel(hideLabel);
        } else if (showFieldLabelSetting) {
            this._hideShowFieldLabel(!hideLabel);
        }
    },

    /**
     * Calculate and apply button visibility dependencies.
     *
     * @param {Object} computedFieldsMeta
     * @param {Object} buttonFieldNameMapping
     *
     */
    _resolveDependency(computedFieldsMeta, buttonFieldNameMapping) {
        const requestType = 'create';
        const apiPath = 'actionButton/evaluateExpression';
        const model = this._getCurrentModel();
        const requestMeta = {
            'targetFields': computedFieldsMeta,
            'targetRecordId': model.get('id'),
            'targetModule': model.get('_module') || this.module,
        };

        const apiCallbacks = {
            success: _.bind(function resolveDependencyCallback(result) {
                _.each(result, function resolveButtonVisibility(showField, fieldId) {
                    const buttonMeta = buttonFieldNameMapping[fieldId];
                    const buttonId = buttonMeta.id;

                    if (!showField) {
                        delete this.activeButtonMeta.buttons[buttonId];
                    } else {
                        this.activeButtonMeta.buttons[buttonId].properties.hasBeenCalculated = true;
                    }
                }, this);

                if (Object.keys(this.activeButtonMeta.buttons).length > 0) {
                    this.activeButtonMeta.settings.hasCalculatedButtons = false;
                } else {
                    this.activeButtonMeta.settings.hasCalculatedButtons = true;
                }

                const fromCalculatedValue = true;

                this._reorderActionButton(fromCalculatedValue);

                this.render();
            }, this)
        };

        const apiUrl = app.api.buildURL(apiPath, requestType, requestMeta, {});

        app.api.call(requestType, apiUrl, requestMeta, null, apiCallbacks);
    },

    /**
     * Show/Hide the field label
     *
     * @param {Bool} hideLabel
     *
     */
    _hideShowFieldLabel(hideLabel) {
        if (!this.$el) {
            return;
        }

        const $headerpane = this.$el.closest('headerpane');
        const isHeaderButton = $headerpane.length !== 0;

        if (isHeaderButton) {
            return true;
        }

        if (!this.view) {
            return;
        }

        if (this.view.name === 'record' || this.view.name === 'create') {
            this._hideShowFieldLabelRecordView(hideLabel);
        } else if (this.view.name === 'preview') {
            this._hideShowFieldLabelPreview(hideLabel);
        } else if (this.view.name === 'dashablerecord') {
            this._hideShowFieldLabelDashableRecordView(hideLabel);
        }
    },

    /**
     * Toggle field labels on RecordView
     *
     * @param {Bool} hideLabel
     */
    _hideShowFieldLabelRecordView(hideLabel) {
        const labelsOnTop = this._isLabelPlacementOnTop();

        let labelHolder = null;

        if (labelsOnTop) {
            labelHolder = this.$el.parent().siblings('[class*=label]');
        } else {
            labelHolder = this.$el.parents().eq(1).siblings('[class*=label]');
        }

        if (labelHolder) {
            this._showHideElement(labelHolder, !hideLabel);
        }
    },

    /**
     * Toggle field labels on PreviewView
     *
     * @param {Bool} hideLabel
     */
    _hideShowFieldLabelPreview(hideLabel) {
        let labelHolder = this.$el.parents().eq(1).find('[class*=label]');

        if (labelHolder) {
            this._showHideElement(labelHolder, !hideLabel);
        }
    },

    /**
     * Toggle field labels on DashablerecordView
     *
     * @param {Bool} hideLabel
     */
    _hideShowFieldLabelDashableRecordView(hideLabel) {
        let labelHolder = this.$el.parents().eq(1).find('[class*=label]');

        if (labelHolder) {
            this._showHideElement(labelHolder, !hideLabel);
        }
    },

    /**
     * Handle field label visibility
     *
     * @param {string} id
     * @param {Bool} showField
     * @param {Bool} isDropDown
     *
     */
    _hideShowFieldById(id, showField, isDropDown) {
        let buttonEl = this.$('#' + id);

        this._showHideElement(buttonEl, showField);

        if (isDropDown && !buttonEl.attr('is_action_button_loading')) {
            let dropdownToggleEl = buttonEl.siblings('.dropdown-toggle[class*=actionbtn]');

            this._showHideElement(dropdownToggleEl, showField);
        }
    },

    /**
     * Show/Hide a html element
     *
     * @param {jQuery} element
     * @param {Bool} showElement
     */
    _showHideElement(element, showElement) {
        element.toggle(showElement);
    },

    /**
     * Handle button click event, start executing button actions.
     *
     * @param {Event} e
     *
     */
    onClick(e) {
        const buttonId = e.currentTarget.id;

        let actions = this._getActionsByButtonId(buttonId);
        let model = this._getCurrentModel();
        this.execute(actions, {
            createLinkModelFct: _.bind(this.createLinkModel, this),
            recordModel: model,
            recordView: this.view,
            buttonField: this,
            stopOnError: this.actionMeta.buttons[buttonId].properties.stopOnError,
        });
    },

    /**
     * Get current model.
     * @return {Object}
     */
    _getCurrentModel: function() {
        return this.view.name === 'side-drawer-headerpane' &&
            !_.isEmpty(this.context) && !_.isEmpty(this.context.parent) &&
            !_.isEmpty(this.context.parent.parent) &&
            _.isFunction(this.context.parent.parent.get) ?
            this.context.parent.parent.get('rowModel') : this.model;
    },

    /**
     * Remove field label from record view
     *
     */
    _resolveElementLabel() {
        const showFieldLabel = this.actionMeta.settings.showFieldLabel;

        if (showFieldLabel) {
            return;
        }

        const labelsOnTop = this._isLabelPlacementOnTop();

        labelsOnTop ? this._hideElementLabelOnTop() : this._hideElementLabelOnLeft();
    },

    /**
     * Remove field label when label on top.
     *
     */
    _hideElementLabelOnTop() {
        const titleEl = this.$el.parent().siblings('div');

        if (titleEl.length > 0) {
            titleEl.hide();
        }

    },

    /**
     * Remove field label when label to the left
     *
     */
    _hideElementLabelOnLeft() {
        const titleEl = this.$el.parents().eq(2).siblings('div');

        if (titleEl.length > 0) {
            titleEl.hide();
        }
    },

    /**
     * Returns label placement.
     *
     * Used to set labelsOnTop in views. Returns true if user preference is
     * 'field_on_top', else false.
     *
     * @return {Bool}
     */
    _isLabelPlacementOnTop: function() {
        return app.user.getPreference('field_name_placement') === 'field_on_top';
    },

    /**
     *
     * Returns button actions definition
     *
     * @param {string} buttonId
     *
     * @return {Object}
     */
    _getActionsByButtonId(buttonId) {
        let actionsMeta = this.actionMeta;
        let buttons = actionsMeta.buttons;

        if (!_.has(buttons, buttonId)) {
            throw new Error('Failed to execute action for button id: '.buttonId);
        }

        const actions = buttons[buttonId].actions;

        return actions;
    },

    /**
     * Removes actions and dependencies from buttons
     *
     * @param {Object} buttonsData
     * @return {Object}
     */
    _getPreparedButtonsData: function(buttonsData) {
        var data = app.utils.deepCopy(buttonsData);

        // remove dependencies and actions
        data.buttons = _.each(data.buttons, function(buttonData, id) {
            buttonData.properties.isDependent = false;
            buttonData.actions = {};
        });

        // if there are no settings yet applied, set default ones
        if (Object.keys(data.settings).length < 1) {
            data.settings = {
                type: 'button',
                size: 'default'
            };
        }

        return data;
    },

    /**
     * Encode/Decode the base64 field meta, recursively.
     *
     *
     * @param {Object|string} data
     * @param {Bool} encode
     *
     * @return {Array}
     */
    base64Parse(data, encode) {
        _.each(data, function parseButtons(childData, key) {
            if (typeof childData === 'object' && childData !== null) {
                data[key] = this.base64Parse(childData, encode);
            } else if (typeof childData === 'string') {
                data[key] = encode ? btoa(encodeURIComponent(childData)) : decodeURIComponent(atob(childData));
            }
        }, this);

        return data;
    },

    /**
     * Inject the color schema to be used in hbs template.
     *
     * @param {Object} meta
     *
     * @return {Object}
     */
    setDropDownCaretTheme(meta) {
        if (meta.settings.type !== 'dropdown') {
            return meta;
        }

        var minItem = {index: 1000, id: null, colorScheme: null};

        _.each(meta.buttons, function parseMeta(item) {
            if (item.orderNumber < minItem.index) {
                minItem.index = item.orderNumber;
                minItem.id = item.buttonId;
                minItem.colorScheme = item.properties.colorScheme;
            }
        });

        meta.settings.colorScheme = minItem.colorScheme;

        return meta;
    },

    /**
     * Decode the metadata
     *
     * @param {string} meta
     *
     * @return {Object}
     */
    parseButtonMeta(meta) {
        let newMeta = {};

        try {
            let base64Encoded = JSON.parse(meta);
            let baseEncode = false;
            let decodedMeta = this.base64Parse(base64Encoded, baseEncode);

            newMeta = this.setDropDownCaretTheme(decodedMeta);
        } catch (e) {
            app.logger.fatal(e.stack);
        }

        return newMeta;
    },
});
