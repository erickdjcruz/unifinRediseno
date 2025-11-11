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
 * @class View.Views.Base.DashboardHeaderpaneMainView
 * @alias SUGAR.App.view.views.DashboardHeaderpaneMainView
 * @extends View.Views.Base.HeaderpaneView
 */
({
    extendsFrom: 'HeaderpaneView',

    buttons: null,

    editableFields: null,

    className: 'preview-headerbar',

    events: {
        'mousemove .record-edit-link-wrapper, .record-lock-link-wrapper': 'handleMouseMove',
        'mouseleave .record-edit-link-wrapper, .record-lock-link-wrapper': 'handleMouseLeave',
        'click [name=edit_button]': 'editClicked',
        'click [name=save_button]': 'saveClicked',
        'click [name=cancel_button]': 'cancelClicked',
        'click [name=create_cancel_button]': 'createCancelClicked',
        'click [name=edit_overview_tab_button]': 'editOverviewTabClicked',
    },

    /**
     * Checks if tooltip is visible.
     *
     * @param {Object} field
     * @return {boolean}
     * @private
     */
    _isTooltipOn: function(field) {
        return !!$(field).attr('aria-describedby');
    },

    /**
     * Checks if ellipsis is active.
     *
     * @param {Object} field
     * @return {boolean}
     * @private
     */
    _isEllipsisOn: function(field) {
        return field.offsetWidth < field.scrollWidth;
    },

    /**
     * Gets target fields in a record-cell for a mouse event.
     * For now it only returns fields with tooltips.
     *
     * @param {Event} event Event object
     * @return {Object} collection of DOM elements of the target fields
     * @private
     */
    _getMouseTargetFields: function(event) {
        let target = this.$(event.target);
        let cell = target.parents('.record-cell');
        let fields = cell.find('[title]');
        return fields;
    },

    /**
     * Handles mousemove event.
     *
     * @param {Event} event Event object
     */
    handleMouseMove: function(event) {
        let fields = this._getMouseTargetFields(event);
        _.each(fields, function(field) {
            let rect = field.getBoundingClientRect();
            let tooltipOn = this._isTooltipOn(field);
            let ellipsisOn = this._isEllipsisOn(field);

            if (event.clientX >= rect.left && event.clientX < (rect.left + rect.width) &&
                event.clientY >= rect.top && event.clientY < (rect.top + rect.height)) {
                if (!tooltipOn && ellipsisOn) {
                    $(field).tooltip('show');
                }
            } else if (tooltipOn) {
                $(field).tooltip('hide');
            }
        }, this);
    },

    /**
     * Handles mouseleave event.
     *
     * @param {Event} event Event object
     */
    handleMouseLeave: function(event) {
        let fields = this._getMouseTargetFields(event);
        _.each(fields, function(field) {
            if (this._isTooltipOn(field)) {
                $(field).tooltip('hide');
            }
        }, this);
    },

    /**
     * @inheritdoc
     */
    adjustTitle: function() {
        let $titleCell = this.$el.find('.record-cell').first();
        if ($titleCell) {
            let $ellipsisDiv = $titleCell.find('.ellipsis_inline');
            let width = parseInt($titleCell.css('max-width'), 10) - 28; // minus width of dropdown toggle
            $ellipsisDiv.css({'max-width': width});
        }
    },

    /**
     * IDs for console dashboards
     *
     * @property {Object}
     */
    consoleDashboards: {
        'da438c86-df5e-11e9-9801-3c15c2c53980': 'renewal-console',
        'c108bb4a-775a-11e9-b570-f218983a1c3e': 'agent-dashboard'
    },

    initialize: function(options) {
        if (options.context.parent) {
            options.meta = app.metadata.getView(options.context.parent.get('module'), options.type, options.loadModule);
            options.template = app.template.getView(options.type);
        }
        this._super('initialize', [options]);
        if (this.model.isNew()) {
            this._setNewModelMeta();
        }
        this.context.set('dataView', '');
        this.model.on('change change:layout change:metadata', function() {
            if (this.inlineEditMode) {
                this.changed = true;
            }
        }, this);
        this.model.on('error:validation', this.handleValidationError, this);

        if (this.context.get('create')) {
            this.changed = true;
            this.action = 'edit';
            this.inlineEditMode = true;
        } else {
            this.action = 'detail';
        }
        this.buttons = {};

        this._bindEvents();
    },

    /**
     * Binds the events that are necessary for this view.
     *
     * @protected
     */
    _bindEvents: function() {
        this.context.on('record:set:state', this.setRecordState, this);
        this.context.on('tabbed-dashboard:switch-tab', this.switchTab, this);
    },

    /**
     * Handles the logic done when the state changes in the record.
     * This is the callback for the `record:set:state` event.
     *
     * @param {string} state The state that the record is set to.
     */
    setRecordState: function(state) {
        this.model.trigger('setMode', state);
        this.setButtonStates(state);
        this.inlineEditMode = state === 'edit';
        this.toggleEdit(this.inlineEditMode);
    },

    /**
     * Event handler for button 'Edit Overview Tab'.
     *
     * @param {Event} evt Triggered mouse event
     */
    editOverviewTabClicked: function(evt) {
        // switch to overview tab
        if (this.context.get('activeTab') !== 0) {
            this.context.trigger('tabbed-dashboard:switch-tab', 0);
        }
        this.editClicked(evt);
    },

    editClicked: function(evt) {
        this.previousModelState = app.utils.deepCopy(this.model.attributes);
        this.inlineEditMode = true;
        this.setButtonStates('edit');
        this.toggleEdit(true);
        this.model.trigger('setMode', 'edit');
    },

    /**
     * Get the dashboard name field and toggle states
     * @param {boolean} isEdit
     */
    toggleNameField: function(isEdit) {
        const field = this.getField('name');
        this.toggleField(field, !!isEdit);
    },

    /**
     * Run save function and switch to view mode
     */
    saveHandle: function() {
        const changes = this.model.changedAttributes(this.model.getSynced());
        if (changes && changes.name) {
            this.layout.handleSave();
        }

        this.setButtonStates('view');
        this.toggleEdit(false);
        this.model.trigger('setMode', 'view');
    },

    cancelClicked: function(evt) {
        this.changed = false;
        this.model.unset('updated');
        this.clearValidationErrors();
        this.setButtonStates('view');
        this.handleCancel();
        this.model.trigger('setMode', 'view');
        this.toggleNameField();
    },

    /**
     * Compare with last fetched data and return true if model contains changes
     *
     * See {@link app.plugins.view.editable}. Ignore the favorite icon for
     * checking for unsaved changes.
     *
     * @return {boolean} true if current model contains unsaved changes
     */
    hasUnsavedChanges: function() {
        if (this.model.get('updated')) {
            return true;
        }

        if (this.model.isNew()) {
            return this.model.hasChanged();
        }

        const changes = this.model.changedAttributes(this.model.getSynced());

        // If there are no changes, don't warn.
        if (_.isEmpty(changes)) {
            return false;
        }

        // if the only change is removal of legacy component from metadata then return false
        if (Object.keys(changes).length === 1 && Object.keys(changes) == 'metadata') {
            // if previous model had legacy components and the synced model doesn't
            if (!this.model.getSynced('metadata').legacyComponents && this.model.get('metadata').legacyComponents) {
                return false;
            }
        }

        // If the only change is to my_favorite, don't warn.
        const nonFavoriteChange = _.find(changes, function(obj, key) {
            return key !== 'my_favorite';
        });

        return !_.isUndefined(nonFavoriteChange);
    },

    /**
     * @override
     *
     * The save function is handled by {@link View.Layouts.Dashboards.DashboardLayout#handleSave}.
     */
    saveClicked: function(evt) {
        this.toggleNameField();
    },

    createCancelClicked: function(evt) {
        if (this.context.parent) {
            this.layout.navigateLayout('list');
        } else {
            app.navigate(this.context);
        }
    },

    /**
     * Handle event: 'tabbed-dashboard:switch-tab'.
     *
     * @param {number} tabIndex New tab's index
     */
    switchTab: function(tabIndex) {
        this.context.set('activeTab', tabIndex);
        this._enableEditButton(this._isDashboard());
    },

    /**
     * Check if this is a tabbed dashboard and active tab is a dashboard.
     *
     * @return {bool} True if this is not a tabbed dashboard
     * or active tab is a dashboard, false otherwise
     * @private
     */
    _isDashboard: function() {
        const tabs = this.context.get('tabs');
        if (!tabs) {
            return true;
        }
        const tabIndex = this.context.get('activeTab') || 0;
        return tabs[tabIndex] &&
            ((tabs[tabIndex].components && tabs[tabIndex].components[0].rows) || tabs[tabIndex].dashlets || false);
    },

    /**
     * Show/hide edit button.
     *
     * @param {bool} state True to show, false to hide
     * @private
     */
    _enableEditButton: function(state) {
        const dropdown = _.find(this.buttons, function(button) {
            return button.type === 'actiondropdown';
        });
        if (dropdown) {
            const editButton = _.find(dropdown.fields, function(field) {
                return field.name === 'edit_button';
            });
            if (editButton) {
                editButton.setDisabled(!state);
                editButton.isHidden = !state;
                dropdown._orderButtons();
                dropdown.render();
            }
        }
    },

    /**
     * Defer rendering until after the data loads. See #_renderHeader for more info.
     *
     * We defer rendering until after data load because by default, the fields
     * will render once on initialization and then will re-render once the data
     * is loaded. This means that while the model is being fetched, it is still
     * possible to interact with the fields, even if the field is in the wrong
     * state (such as favorite/unfavorite). Additionally, this causes a
     * distracting and annoying flickering effect.
     *
     * To avoid both the flickering effect and prevent users from accidentally
     * setting field values during data fetch, we defer rendering until after
     * the data is loaded.
     *
     * @override
     * @private
     */
    _render: function() {
        // When creating a dashboard, there is no model to load, so there is
        // no need to defer rendering.
        if (this.context.get('create')) {
            this._renderHeader();
        } else {
            this.model.once('sync', this._renderHeader, this);
        }
        return this;
    },

    /**
     * Render the view manually.
     *
     * This function handles the responsibility typically handled in _render,
     * but unlike `_render`, it is not called automatically.
     *
     * See #_render for more information.
     */
    _renderHeader: function() {
        app.view.View.prototype._render.call(this);

        let id = this.model.get('id');
        if (id && Object.keys(this.consoleDashboards).includes(id)) {
            let headerpane = this.el.querySelector('.headerpane');
            headerpane.classList.add('console-headerpane');
        }

        this._setButtons();
        this.setButtonStates(this.context.get('create') ? 'create' : 'view');
        this.setEditableFields();
        this._enableEditButton(false);

        // Give focus to the dashboard name input
        if (this.action === 'edit') {
            this.$('span[data-type="dashboardtitle"] .edit input').focus();
        }

        if (this.model.get('is_template')) {
            const templateEditableFields = this.getTemplateEditableFields();

            this.hidePencil(templateEditableFields);
        }
    },

    handleCancel: function() {
        this.inlineEditMode = false;
        if (!_.isEmpty(this.previousModelState)) {
            this.model.set(this.previousModelState);
        }
        this.toggleEdit(false);
    },

    bindDataChange: function() {
        //empty out because dashboard header does not need to switch the button sets while model is changed
    },

    toggleEdit: function(isEdit) {
        if (!this.editableFields) {
            this.editableFields = [];
        }

        this.editableFields = this.editableFields.filter(function(item) {
            return item.name !== 'name';
        });

        this.toggleFields(this.editableFields, isEdit);
    },

    /**
     * Initialize metadata on new dashboard
     * @private
     */
    _setNewModelMeta: function() {
        const metadata = {
            dashlets: []
        };
        this.model.set('metadata', metadata, {silent: true});
        this.model.trigger('change:metadata');
        this.model.changed = {};
    },
});
