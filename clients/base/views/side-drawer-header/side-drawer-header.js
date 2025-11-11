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
 * @class View.Views.Base.SideDrawerHeaderView
 * @alias SUGAR.App.view.views.BaseSideDrawerHeaderView
 * @extends View.Views.Base.HeaderpaneView
 * @deprecated SideDrawerHeader View is deprecated as of 11.2.0. Use SideDrawerHeaderpane instead.
 */
({
    /**
     * This is a special header for side drawers that contain a dashlet.
     * Such drawers need a header with just the minimum setup.
     * This file is a partial copy of {@link View.Views.Dashboards.DashboardHeaderpaneView}.
     */
    extendsFrom: 'HeaderpaneView',

    editableFields: null,

    display: false,

    className: 'preview-headerbar',

    events: {
        'click [name=edit_button]': 'editClicked',
        'click [name=cancel_button]': 'cancelClicked'
    },

    initialize: function(options) {
        app.logger.warn('SideDrawerHeader View is deprecated as of 11.2.0. Use SideDrawerHeaderpane instead.');
        if (options.context.parent) {
            options.meta = app.metadata.getView(options.context.parent.get('module'), options.type, options.loadModule);
            options.template = app.template.getView(options.type);
        }
        this._super('initialize', [options]);
        this.context.set('dataView', '');
        this.model.on('change change:layout change:metadata', function() {
            if (this.inlineEditMode) {
                this.changed = true;
            }
        }, this);
        this.model.on('error:validation', this.handleValidationError, this);
        this.action = 'detail';
        this.bindEvents();
    },

    /**
     * Binds the events that are necessary for this view.
     */
    bindEvents: function() {
        this.context.on('record:set:state', this.setRecordState, this);
        app.events.on('drawer:edit', this.showEditHeader, this);
    },

    /**
     * Handles the logic done when the state changes in the record.
     *
     * @param {string} state The state that the record is set to.
     */
    setRecordState: function(state) {
        this.model.trigger('setMode', state);
        this.setButtonStates(state);
        this.inlineEditMode = state === 'edit';
        this.toggleFields(this.editableFields, this.inlineEditMode);
        if (state === 'view') {
            this.hideHeader();
            app.events.trigger('drawer:enable:actions');
        }
    },

    /**
     * Sets edit mode on the dashboard.
     */
    editClicked: function() {
        this.previousModelState = app.utils.deepCopy(this.model.attributes);
        this.inlineEditMode = true;
        this.setButtonStates('edit');
        this.toggleFields(this.editableFields, true);
        this.model.trigger('setMode', 'edit');
    },

    /**
     * Sets view mode on the dashboard and resets to the state before edit.
     */
    cancelClicked: function() {
        this.changed = false;
        this.model.unset('updated');
        this.clearValidationErrors();
        this.setButtonStates('view');
        this.handleCancel();
        this.model.trigger('setMode', 'view');
        app.events.trigger('drawer:enable:actions');
    },

    /**
     * Compare with last fetched data and return true if model contains changes.
     *
     * @return {boolean} True if current model contains unsaved changes.
     */
    hasUnsavedChanges: function() {
        if (this.model.get('updated')) {
            return true;
        }

        if (this.model.isNew()) {
            return this.model.hasChanged();
        }

        var changes = this.model.changedAttributes(this.model.getSynced());

        return !_.isEmpty(changes);
    },

    /**
     * @override
     *
     * The save function is handled by {@link View.Layouts.Dashboards.DashboardLayout#handleSave}.
     */
    saveClicked: $.noop,

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
        this._setButtons();
        this.setButtonStates('view');
        this.setEditableFields();
    },

    /**
     * Handle the cancellation of edit mode.
     */
    handleCancel: function() {
        this.inlineEditMode = false;
        if (!_.isEmpty(this.previousModelState)) {
            this.model.set(this.previousModelState);
        }
        this.toggleFields(this.editableFields, false);
        this.hideHeader();
    },

    /**
     * Will display the header in edit mode with the cancel and save buttons present.
     */
    showEditHeader: function() {
        this.display = true;
        this._renderHeader();
        this.editClicked();
    },

    /**
     * Will remove the header from the parent view.
     */
    hideHeader: function() {
        this.display = false;
        this._renderHeader();
    }
})
