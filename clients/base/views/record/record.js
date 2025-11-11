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
 * @class View.Views.Base.RecordView
 * @alias SUGAR.App.view.views.BaseRecordView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    dataView: 'record',

    inlineEditMode: false,

    /**
     * Flag to keep track of the elements that clicked in-line on a detail view to edit view
     */
    inlineEditModeFields: [],

    createMode: false,

    plugins: [
        'SugarLogic',
        'ErrorDecoration',
        'GridBuilder',
        'Editable',
        'Audit',
        'Pii',
        'FindDuplicates',
        'ToggleMoreLess',
        'ActionButton',
        'DocumentMerge',
        'MappableRecord',
    ],

    enableHeaderButtons: true,

    enableHeaderPane: true,

    events: {
        'mousemove .record-edit-link-wrapper, .record-lock-link-wrapper': 'handleMouseMove',
        'mouseleave .record-edit-link-wrapper, .record-lock-link-wrapper': 'handleMouseLeave',
        'mouseup .record-link-wrapper': 'handleLinkWrapperMouseUp',
        'click .record-edit-link-wrapper': 'handleEdit',
        'click .label-pill .record-label': 'handleEdit',
        'click a[name=cancel_button]': '_deprecatedCancelClicked',
        'click [data-action=scroll]': 'paginateRecord',
        'click .record-panel-header-container': 'togglePanel',
        'click #recordTab > .tab > a:not(.dropdown-toggle)': 'setActiveTab',
        'click .tab .dropdown-menu a': 'triggerNavTab'
    },

    /**
     * Button fields defined in view definition.
     */
    buttons: null,

    /**
     * Button states.
     */
    STATE: {
        EDIT: 'edit',
        VIEW: 'view'
    },

    // current button states
    currentState: null,

    // fields that should not be editable
    noEditFields: null,

    // width of the layout that contains this view
    _containerWidth: 0,

    /**
     * Flag indicating if the model for this view contains fields that are locked.
     *
     * @private
     * @type {boolean}
     */
    _hasLockedFields: false,

    /**
     * Name of the field that contains the field and its surrounding elements
     * like the label, pencil icon, etc.
     */
    decoratorField: 'record-decor',

    /**
     * Current active contact and model in SugarLive. Store these here so that we can properly hide and show
     * the link button when leaving edit mode.
     */
    sugarLiveContact: null,
    sugarLiveContactModel: null,

    /**
     * Reference to the SugarLive record link button
     */
    sugarLiveLinkButton: null,

    /**
     * Variables to store data related to dropdown-based views
     * baseMetaPanels: The base record view metadata panels defintions
     * dbvMetaPanels: A map of dropdown value -> metadata panels definitions
     * dbvTriggerField: The name of the dropdown field that triggers dropdown-based view changes
     * dbvCurrentKey: The key of dbvMetaPanels that is currently in use
     */
    baseMetaPanels: null,
    dbvMetaPanels: null,
    dbvTriggerField: null,
    dbvCurrentKey: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        /**
         * @inheritdoc
         * @property {Object} meta
         * @property {boolean} meta.hashSync Set to `true` to update URL
         *   consistently with the view state (`edit` or `detail`)
         */
        options.meta = _.extend({}, app.metadata.getView(null, 'record'), options.meta);

        this.initCustomerJourneyMeta(options);

        this.inlineEditModeFields = [];
        options.meta.hashSync = _.isUndefined(options.meta.hashSync) ? true : options.meta.hashSync;
        if (options.meta.hasExternalFields) {
            this.plugins = _.union(this.plugins || [], ['ExternalApp']);
        }
        app.view.View.prototype.initialize.call(this, options);
        this.buttons = {};
        //Adding the favorite and follow fields.
        this.context.addFields(this._getDataFields());

        // FIXME: SC-5650 will handle removing these deprecation warnings in 7.10+
        _.each(this.meta.panels, function(panel) {
            _.each(panel.fields, function(field) {
                if (field.label_css_class) {
                    app.logger.warn('Warning: metadata property "label_css_class" found on field with name "' +
                        field.name + '" is deprecated and will be removed in a future release.');
                }
                if (field.cell_css_class) {
                    app.logger.warn('Warning: metadata property "cell_css_class" found on field with name "' +
                        field.name + '" is deprecated and will be removed in a future release.');
                }
            }, this);
        }, this);

        /**
         * An array of the {@link #alerts alert} names in this view.
         *
         * @property {Array}
         * @protected
         */
        this._viewAlerts = [];

        /**
         * A collection of alert messages to be used in this view. The alert methods
         * should be invoked by Function.prototype.call(), passing in an instance of
         * a sidecar view. For example:
         *
         *     // ...
         *     this.alerts.showInvalidModel.call(this);
         *     // ...
         *
         * FIXME: SC-3451 will refactor this `alerts` structure.
         * @property {Object}
         */
        this.alerts = {
            showInvalidModel: function() {
                if (!this instanceof app.view.View) {
                    app.logger.error('This method should be invoked by Function.prototype.call(), passing in as argument' +
                        'an instance of this view.');
                    return;
                }
                var name = 'invalid-data';
                this._viewAlerts.push(name);
                app.alert.show(name, {
                    level: 'error',
                    messages: 'ERR_RESOLVE_ERRORS'
                });
            },
            showNoAccessError: function(error) {
                if (!this instanceof app.view.View) {
                    app.logger.error('This method should be invoked by Function.prototype.call(), passing in as argument' +
                        'an instance of this view.');
                    return;
                }
                // dismiss the default error
                app.alert.dismiss('data:sync:error');
                // display no access error
                app.alert.show('server-error', {
                    level: 'error',
                    messages: this._getNoAccessErrorMessage(error)
                });
                // discard any changes before redirect
                this.handleCancel();
                // redirect to list view
                var route = app.router.buildRoute(this.module);
                app.router.navigate(route, {trigger: true});
            }
        };
        this.createMode = this.context.get('create') ? true : false;

        this.action = this.context.get('action') || 'detail';

        this.context.on('change:record_label', this.setLabel, this);
        this.context.set('viewed', true);
        this.model.on('duplicate:before', this.setupDuplicateFields, this);
        // displays error msg when required field is missing
        this.model.on('error:validation', this.alerts.showInvalidModel, this);
        this.on('editable:keydown', this.handleKeyDown, this);
        this.on('editable:mousedown', this.handleMouseDown, this);
        this.on('field:error', this.handleFieldError, this);
        this.on('editable:toggle_fields', this.focusFirstInput, this);
        this.model.on('acl:change', this.handleAclChange, this);
        this.context.on('field:disabled', this._togglePencil, this);

        this._initializeSugarLiveLink();

        //event register for preventing actions
        // when user escapes the page without confirming deleting
        app.routing.before('route', this.beforeRouteDelete, this);
        $(window).on('beforeunload.delete' + this.cid, _.bind(this.warnDeleteOnRefresh, this));

        this.delegateButtonEvents();

        if (this.createMode) {
            this.model.isNotEmpty = true;
        }

        this.noEditFields = [];
        // properly namespace SHOW_MORE_KEY key
        this.MORE_LESS_KEY = app.user.lastState.key(this.MORE_LESS_KEY, this);

        this.adjustHeaderpane = _.bind(_.debounce(this.adjustHeaderpane, 50), this);
        $(window).on('resize.' + this.cid, this.adjustHeaderpane);
        $(window).on('resize.' + this.cid, _.bind(this.overflowTabs, this));

        if (!this.getLabelPlacement()) {
            $(window).on('resize.' + this.cid, _.bind(_.debounce(this.relocatePencils, 500), this));
        }

        // initialize tab view after the component is attached to DOM
        this.on('append', function() {
            this.overflowTabs();
            this.handleActiveTab();
        }, this);

        this.on('render', this.registerShortcuts, this);

        // Option to specify additional noEdit/readonly fields
        this.extraNoEditFields = this.context.get('noEditFields') || [];

        // Option to open the record view to immediately validate/save
        this.saveImmediately = this.context.get('saveImmediately') || false;

        // Option to make this view edit-only
        this.editOnly = this.context.get('editOnly') || false;

        // Optional callbacks for after-save, after-cancel, and after-validation
        this.saveCallback = this.context.get('saveCallback') || null;
        this.cancelCallback = this.context.get('cancelCallback') || null;
        this.validationCallback = this.context.get('validationCallback') || null;

        // Option to avoid navigating to other routes during edit/save (useful for opening in a drawer)
        this.skipRouting = this.context.get('skipRouting') || false;

        // Listening for the save from preview to finish and reload data on the main record view
        // to reflect changes from preview's edit
        app.events.on('preview:edit:save', function() {
            this.context.reloadData();
        }, this);

        app.events.on('focusdrawer:close', this.handleFocusDrawerClose, this);

        this.cancelButtonClicked = false;

        // Run certain tasks after initialize() is completely done. We do this
        // because of the complex inheritance structure of base and module-
        // specific create and record views, and how metadata is manipulated
        // at each level during initialize()
        this.listenTo(this, 'init', this._afterInit);
    },

    /**
     * Handles tasks to be done after the initialize is fully complete
     *
     * @private
     */
    _afterInit: function() {
        if (this.showExtraMeta !== false) {
            this._initExtraMeta();
        }
        this.meta.panels = this._filterPanels(this.meta.panels);
        this._initDropdownBasedViews();
    },

    /**
     * Removes any panels that should not be visible to the current user
     *
     * @param {Array} panels the panels metadata to filter
     * @return {Array} the filtered metadata panels list
     */
    _filterPanels: function(panels) {
        let newPanels = [];
        _.each(panels, function(panel) {
            if (panel.visibility === 'self' && this.model.get('id') !== app.user.get('id')) {
                return;
            }
            if (panel.invisibility === this.type) {
                return;
            }
            newPanels.push(panel);
        }, this);
        return newPanels;
    },

    /**
     * Returns the message displayed when a 403/no access error is encountered
     *
     * @param {Object} error the error from the API
     * @return {string} the error message to display
     * @private
     */
    _getNoAccessErrorMessage: function(error) {
        return 'ERR_HTTP_404_TEXT_LINE1';
    },

    /**
     * Reload data if any records have been updated in focus drawer
     * after the focus drawer is closed.
     * @param {Array} updatedModels
     */
    handleFocusDrawerClose: function(updatedModels) {
        if (!_.isEmpty(updatedModels)) {
            this.model.fetch({
                showAlerts: true,
                view: 'record'
            });
        }
    },

    /**
     * Initializes any data needed to support dropdown-based views
     *
     * @private
     */
    _initDropdownBasedViews: function() {
        // If dropdown-based views are defined, no need to do anything
        let dropdownViews = app.metadata.getDropdownViews(this.module, this._getDropdownBasedViewName());
        if (_.isEmpty(dropdownViews)) {
            return;
        }

        // Store the original/base metadata the view was initialized with
        this.baseMetaPanels = this.meta.panels;

        // Store the name of the field that triggers dropdown-based view
        // changes, and the set of possible view metadata configurations by
        // dropdown value
        this.dbvTriggerField = _.first(_.keys(dropdownViews));
        this.dbvMetaPanels = _.mapObject(dropdownViews[this.dbvTriggerField], function(valueMeta) {
            let meta = this._addExtraMetaPanels(valueMeta.meta || {});
            return meta.panels || null;
        }, this);

        this._initDropdownBasedViewsForModel();
    },

    /**
     * Initializes the dropdown-conditional view metadata to be based on the
     * current model for the view
     *
     * @private
     */
    _initDropdownBasedViewsForModel: function() {
        if (_.isEmpty(this.dbvMetaPanels)) {
            return;
        }

        // Consider the trigger dropdown's value on the model as a "key" that
        // we can use to determine what the proper metadata is for the view
        this.dbvCurrentKey = this._getDropdownBasedViewKeyForModel(this.model);

        // Now, initialize the view metadata to the proper set based on the
        // current "key"/trigger field value
        this._setDbvMeta(this.dbvCurrentKey);

        // Whenever the "key"/trigger field value changes on the model,
        // recalculate and rerender based on which view metadata should be used
        this.listenTo(this.model, `change:${this.dbvTriggerField}`, this._handleDbvTriggerChange);

        // Don't show the dropdown-based view change warning when the record
        // is being fetched
        this.model.setOption('hideDbvWarning', true);
    },

    /**
     * Returns the name used by this view for dropdown-based view metadata
     *
     * @return {string} the dropdown-based view name
     * @private
     */
    _getDropdownBasedViewName: function() {
        return this.name;
    },

    /**
     * Given a model, determines which dropdown-based view metadata key to use
     *
     * @param {Bean} model the model for the view
     * @return {string|null} the view metadata key to use in this.dbvMetaPanels
     * @private
     */
    _getDropdownBasedViewKeyForModel: function(model) {
        if (model && this.dbvTriggerField) {
            let triggerFieldValue = model.get(this.dbvTriggerField);
            if (triggerFieldValue && !_.isString(triggerFieldValue)) {
                triggerFieldValue = triggerFieldValue.toString();
            }
            if (Object.keys(this.dbvMetaPanels).includes(triggerFieldValue)) {
                return triggerFieldValue;
            }
        }

        return null;
    },

    /**
     * Given a dropdown-based view metadata key, returns the proper metadata
     * to use in the view
     *
     * @param {string} key the dropdown-based view metadata key
     * @return {Object} the metadata configuration associated with the key
     * @private
     */
    _getMetaForDropdownBasedViewKey: function(key) {
        return this.dbvMetaPanels[key] || this.baseMetaPanels;
    },

    /**
     * For dropdown-based views, determines whether a change in the model's
     * dropdown value should trigger a view metadata refresh with the updated
     * set of panels. If it should, updates metadata and re-renders the view
     *
     * @private
     */
    _handleDbvTriggerChange: function(model, dbvTriggerFieldValue, options) {
        _.defer(() => {
            let newDropdownViewKey = this._getDropdownBasedViewKeyForModel(model);
            if (newDropdownViewKey !== this.dbvCurrentKey) {
                // Replace the view metadata with the dropdown-based view meta
                this.dbvCurrentKey = newDropdownViewKey;
                this._setDbvMeta(this.dbvCurrentKey);
                if (_.isFunction(this.stopSugarLogic)) {
                    this.stopSugarLogic();
                }
                this.render();
                if (_.isFunction(this.startSugarLogic)) {
                    this.startSugarLogic();
                }
                // If necessary, display a warning message that the view is
                // changing
                let hideDbvWarning = options && options.hideDbvWarning;
                if (!hideDbvWarning) {
                    this._displayDropdownBasedViewWarning();
                }
            }
        });
    },

    /**
     * Sets the view's metadata to the metadata specified by the given
     * dropdown-based view key
     *
     * @param dbvKey
     * @private
     */
    _setDbvMeta: function(dbvKey) {
        this.meta.panels = this._getMetaForDropdownBasedViewKey(dbvKey);
    },

    /**
     * Displays a warning to the user that a dropdown-based view change has
     * been triggered. If the "Undo" link is clicked in the warning, the
     * view changes are canceled
     *
     * @private
     */
    _displayDropdownBasedViewWarning: function() {
        app.alert.show('cancel-dropdown-view-change', {
            level: 'info',
            autoClose: true,
            autoCloseDelay: 4000,
            messages: app.lang.get('LBL_DROPDOWN_VIEW_CHANGE_WARNING', this.module)
        });
        let alert = app.alert.get('cancel-dropdown-view-change');
        if (alert) {
            alert.$el.find('#cancel_button').on('click', () => {
                this._cancelDropdownViewChange();
            });
        }
    },

    /**
     * Cancels the record edit when the user cancels a dropdown view change
     * @private
     */
    _cancelDropdownViewChange: function() {
        this.cancelClicked();
    },

    /**
     * Injects extra record-extra-meta metadata into the view if it is defined
     *
     * @param {Object} options the view initialization options
     * @private
     */
    _initExtraMeta: function() {
        this.extraMeta = app.metadata.getView(this.module, 'record-extra-meta');
        if (!_.isEmpty(this.extraMeta)) {
            // Add the extra fields to the context so they are fetched properly
            let extraFields = _.pluck(_.flatten(_.pluck(this.extraMeta.panels, 'fields')), 'name');
            let contextFields = this.context.get('fields') || [];
            contextFields = contextFields.concat(extraFields);
            this.context.set('fields', _.uniq(contextFields));

            // Add the extra panels to the view's metadata
            this.meta = this._addExtraMetaPanels(this.meta || {});
        }
    },

    /**
     * Injects any record-extra-meta panels into the metadata if defined
     *
     * @param {Object} meta the metadata to augment
     * @return {Object} the augmented metadata
     * @private
     */
    _addExtraMetaPanels(meta) {
        let extraMeta = this.extraMeta || {};

        // Add any extra panels to record view metadata
        if (!_.isEmpty(extraMeta.panels)) {
            meta.panels = meta.panels || [];
            meta.panels = meta.panels.concat(extraMeta.panels || []);

            // If any of the extra panels are tabs, force the first panel to be
            // a tab to allow for tabbed view
            let extraHasTabs = _.some(extraMeta.panels, function(panel) {
                return !!panel.newTab;
            });
            if (extraHasTabs) {
                let firstPanel = _.find(meta.panels, function(panel) {
                    return panel.header !== true;
                });
                if (firstPanel) {
                    firstPanel.newTab = true;
                }
            }
        }

        return meta;
    },

    /**
     * Relocate all pencils of the record
     */
    relocatePencils: function() {
        _.each(this.fields, function(field) {
            if (field.type === 'record-decor') {
                field.relocatePencil();
            }
        });
    },

    /**
     * Handler for when the ACLs change on the model. Toggles the `hide` class
     * on the pencil wrapper for each of the fields on this view that had ACL
     * changes.
     * Hide the wrapper if no access to edit.
     *
     * @param {Object} diff The diff object of fields with ACL changes.
     */
    handleAclChange: function(diff) {
        this._setNoEditFields();
        this.setEditableFields();

        var noEditFieldsMap = _.object(this.noEditFields, _.values(this.noEditFields));
        var $pencils = this.$('[data-wrapper=edit]');

        // Update the edit pencil availability for all fields
        _.each($pencils, function(pencilEl) {
            var $pencilEl = $(pencilEl);
            var field = $pencilEl.data('name');

            var isEditable = _.isUndefined(noEditFieldsMap[field]);
            $pencilEl.toggleClass('hide', !isEditable);

            if (this.action === 'edit') {
                $pencilEl.closest('.record-cell').toggleClass('edit', isEditable);
            }
        }, this);
    },

    /**
     * Shows or hides the edit pencil icon for a field.
     *
     * @param {string} fieldName The field name.
     * @param {boolean} hide `true` to hide the pencil, `false` to show it.
     * @private
     */
    _togglePencil: function(fieldName, hide) {
        var isEditable = !_.contains(this.noEditFields, fieldName) && app.acl.hasAccessToModel('edit', this.model);

        if (!hide && !isEditable) {
            return;
        }

        this.$('span.record-edit-link-wrapper[data-name=' + fieldName + ']').toggleClass('hide', hide);
    },

    /**
     * Go through the field controllers and set the locked states accordingly.
     */
    handleLockedFields: function() {
        var self = this;

        // Reset the locked field state
        this._setLockedFieldFlag(false);

        var lockedFields = this.model.get('locked_fields');

        // Loop and check locked field state of each field
        _.each(this.$('.record-lock-link-wrapper[data-name]'), function(el) {
            var $el = $(el);
            var fieldName = $el.data('name');

            // No field name, nothing to do
            if (fieldName == '') {
                return;
            }

            // Get the field object
            var field = this.getField(fieldName);

            // Is the current field locked?
            var isLocked = _.contains(lockedFields, fieldName);

            // Special handling for fieldsets
            if (field.fields) {
                var hasLockedChildField = false;
                isLocked = true;
                _.each(field.fields, function(fieldSetField) {
                    // Some fieldsets have fields that are only for viewing, like the
                    // `copy` field on alternate addresses. Those should be filtered
                    // out of the fields list.
                    if (_.isUndefined(this.model.get(fieldSetField.name))) {
                        return;
                    }

                    var isChildLocked = _.contains(lockedFields, fieldSetField.name);
                    hasLockedChildField = hasLockedChildField || isChildLocked;

                    // A fieldset is locked when all of its actual fields are locked
                    if (!isChildLocked) {
                        isLocked = false;
                    }
                }, this);
            }

            // Set the flag that says if we have locked fields
            this._setLockedFieldFlag(this.hasLockedFields() || isLocked || hasLockedChildField);

            // Handle toggling the class
            $el.toggleClass('hide', !isLocked);
        }, this);

        // Show the locked field warning if there is one
        if (this.hasLockedFields()) {
            this.warnLockedFields();
        }
    },

    /**
     * Returns the flag that tells whether this object has locked fields or not
     * @return {boolean}
     */
    hasLockedFields: function() {
        return this._hasLockedFields;
    },

    /**
     * Sets the locked field flag
     * @param {boolean} setFlag
     * @private
     */
    _setLockedFieldFlag: function(setFlag) {
        this._hasLockedFields = setFlag;
    },

    /**
     * Alert warning if there are locked fields on the model.
     */
    warnLockedFields: function() {
        if (this.getCurrentButtonState() !== this.STATE.EDIT) {
            return;
        }

        if (this.context.get('lockedFieldsWarning') === false) {
            this.context.set('lockedFieldsWarning', true);
        } else {
            app.alert.show('record_locked_field_warning', {
                level: 'warning',
                messages: 'LBL_LOCKED_FIELD_RECORD_VIEW_WARNING',
                autoClose: true,
                autoCloseDelay: 5000
            });
        }
    },

    /**
     * Compare with last fetched data and return true if model contains changes.
     *
     * Check changes for fields that are editable only.
     *
     * {@link app.plugins.view.editable}
     *
     * @return {Boolean} `true` if current model contains unsaved changes, otherwise `false`.
     */
    hasUnsavedChanges: function() {
        var changedAttributes,
            editableFieldNames = [],
            unsavedFields,
            self = this,
            setAsEditable = function(fieldName) {
                if (fieldName && _.indexOf(self.noEditFields, fieldName) === -1) {
                    editableFieldNames.push(fieldName);
                }
            };

        if (this.resavingAfterMetadataSync)
            return false;

        changedAttributes = this.model.changedAttributes(this.model.getSynced());

        if (_.isEmpty(changedAttributes)) {
            return false;
        }

        // get names of all editable fields on the page including fields in a fieldset
        _.each(this.meta.panels, function(panel) {
            _.each(panel.fields, function(field) {
                if (!field.readonly) {
                    setAsEditable(field.name);
                    if (field.fields && _.isArray(field.fields)) {
                        _.each(field.fields, function(field) {
                            setAsEditable(field.name);
                        });
                    }
                }
            });
        });

        // check whether the changed attributes are among the editable fields
        unsavedFields = _.intersection(_.keys(changedAttributes), editableFieldNames);

        return !_.isEmpty(unsavedFields);
    },

    /**
     * Called when current record is being duplicated to allow customization of
     * fields that will be copied into new record.
     *
     * Override to setup the fields on this bean prior to being displayed in
     * Create dialog.
     *
     * @param {Object} prefill Bean that will be used for new record.
     * @template
     */
    setupDuplicateFields: function(prefill) {
    },

    setLabel: function(context, value) {
        var plus = '<i class="sicon sicon-plus-sm label-plus"></i>';
        this.$('.record-label[data-name="' + value.field + '"]')
            .html(plus).append(document.createTextNode(value.label));
    },

    /**
     * Called each time a validation pass is completed on the model.
     *
     * Enables the action button and calls {@link #handleSave} if the model is
     * valid.
     *
     * @param {boolean} isValid TRUE if model is valid.
     */
    validationComplete: function(isValid) {
        this.toggleButtons(true);
        if (isValid) {
            this.handleSave();
        }
        if (typeof this.validationCallback === 'function') {
            this.validationCallback(isValid);
        }
    },

    /**
     * Assign events to button clicks.
     */
    delegateButtonEvents: function() {
        this.listenTo(this.context, 'button:edit_button:click', this.editClicked);
        this.listenTo(this.context, 'button:save_button:click', this.saveClicked);
        this.listenTo(this.context, 'button:delete_button:click', this.deleteClicked);
        this.listenTo(this.context, 'button:duplicate_button:click', this.duplicateClicked);
        this.listenTo(this.context, 'button:cancel_button:click', this.cancelClicked);
    },

    _render: function() {
        this._buildGridsFromPanelsMetadata(this.meta.panels);
        if (!_.isEmpty(_.get(this, ['meta', 'panels']))) {
            this._initTabsAndPanels();
        }
        // it seems like this.fields gets set somewhere here...
        // but that makes no sense.
        app.view.View.prototype._render.call(this);

        if (this.context.get('record_label')) {
            this.setLabel(this.context, this.context.get('record_label'));
        }

        // Prevent the pencil from appearing on fields that are always readonly
        _.each(this.fields, function(field) {
            if (app.utils.isFieldAlwaysReadOnly(field.def, field.viewDefs) ||
                !app.acl.hasAccessToModel('edit', this.model, field.name)) {
                this.$('.record-edit-link-wrapper[data-name=' + field.name + ']').addClass('hide');
            }
        }, this);

        if (this.action === 'edit') {
            this.setButtonStates(this.STATE.EDIT);
            this.toggleEdit(true);
        } else {
            this.setButtonStates(this.STATE.VIEW);
            if (this.createMode) {
                this.toggleEdit(true);
            }
        }

        // initialize tab view only if the component is attached to DOM,
        // otherwise it's initialized partially and cannot be properly
        // re-initialized after the component is attached to DOM
        if ($.contains(document.documentElement, this.$el[0])) {
            this.handleActiveTab();
            this.overflowTabs();
        }

        // If saveImmediately is set, programmatically click Edit -> Save
        if (this.saveImmediately) {
            this.editClicked();
            this.saveClicked();
        }

        this.createSugarLiveLinkButton();

        // If any fields were in inline edit mode when the view re-renderd,
        // place them back into that mode
        if (this.inlineEditMode) {
            _.each(this.inlineEditModeFields, function(field) {
                let element = this.getField(field);
                if (element) {
                    this.toggleField(element, true, true);
                }
            }, this);
            this.setButtonStates(this.STATE.EDIT);
        }
    },

    _renderField: function(field, $fieldEl) {
        // When we render the view, we need to enforce `action`
        // to be 'detail' if the field is non editable.
        // This is due to how View.Field#_loadTemplate currently works.
        // FIXME SC-6037: Will remove this hack.
        if (!_.contains(this.editableFields, field)) {
            // For fieldsets, we need to also set the actions of their subfields
            let fields = [field];
            if (field.type === 'fieldset' && !_.isEmpty(field.fields)) {
                fields = _.union(fields, field.fields);
            }

            _.each(fields, function(fieldToSet) {
                fieldToSet.action = 'detail';
                // Set viewName to `detail` if it was set to `edit` (because the field is non-editable)
                // but if it is not `edit` (hardcoded e.g. preview template), we want to keep it as it was.
                if (fieldToSet.options.viewName === 'edit') {
                    fieldToSet.options.viewName = 'detail';
                }
            }, this);
        }

        this._super('_renderField', [field, $fieldEl]);
    },

    /**
     * Handles initiation of Tabs and Panels view upon render
     * @private
     */
    _initTabsAndPanels: function() {
        this.meta.firstPanelIsTab = this.checkFirstPanel();
        this.meta.lastPanelIndex = this.meta.panels.length - 1;

        _.each(this.meta.panels, function(panel, i) {
            if (panel.header) {
                this.meta.firstNonHeaderPanelIndex = (i + 1);
            }
        }, this);

        // Tell the view to use Tabs and Panels view if either there exists a tab or if the number of panels isn't
        // equivalent to the amount expected for Business Card view (2 panels + possibly 1 if header exists)
        var headerExists = 0;
        if (_.first(this.meta.panels).header) {
            headerExists = 1;
        }

        this.meta.useTabsAndPanels = false;

        //Check if there are any newTabs
        for (i = headerExists; i < this.meta.panels.length; i++) {
            if (this.meta.panels[i].newTab) {
                this.meta.useTabsAndPanels = true;
            }
        }

        //Check for panel number
        if (this.meta.panels.length > (2 + headerExists)) {
            this.meta.useTabsAndPanels = true;
        }

        // set states
        _.each(this.meta.panels, function(panel){
            var panelKey = app.user.lastState.key(panel.name+':tabState', this);
            var panelState = app.user.lastState.get(panelKey);
            panel.panelState = panelState || panel.panelDefault;
        }, this);
    },
    /**
     * handles setting active tab
     */
    handleActiveTab: function() {
        var activeTabHref = this.getActiveTab(),
            activeTab = this.$('#recordTab > .tab > a[href="'+activeTabHref+'"]');

        // Always show first tab if we're on the create view
        if (this.createMode) {
            this.$('#recordTab a:first').tab('show');
            return;
        }

        if (activeTabHref && activeTab.length) {
            activeTab.tab('show');
            this.makeTabActiveForFirefox(activeTab);
        } else if (this.meta.useTabsAndPanels && this.checkFirstPanel()) {
            // If tabs and no last state set, show first tab on render
            this.$('#recordTab a:first').tab('show');
        }
    },
    /**
     * Gets the active tab in the user last state
     * @return {String} The active tab id in the user's last state.
     */
    getActiveTab: function() {
        var activeTabHref = app.user.lastState.get(app.user.lastState.key('activeTab', this));

        // Set to first tab by default
        if (!activeTabHref) {
            activeTabHref = this.$('#recordTab > .tab:first-child > a').attr('href') || '';
            app.user.lastState.set(
                app.user.lastState.key('activeTab', this),
                activeTabHref.substring(0, activeTabHref.indexOf(this.cid))
            );
        }
        else {
            activeTabHref += this.cid;
        }
        return activeTabHref;
    },

    /**
     * There is known issue in firefox 120, this can be removed
     * when this bug: https://bugzilla.mozilla.org/show_bug.cgi?id=1858743 is fixed in the release of Firefox 121 .....
     *
     * @param {Object} currentTarget
     */
    makeTabActiveForFirefox: function(currentTarget) {
        // check for firefox
        if (typeof InstallTrigger === 'undefined') {
            return;
        }
        const tabEl = this.$(currentTarget).parent();

        if (!tabEl) {
            return;
        }

        const isAlreadyActive = tabEl.hasClass('active-tab');

        if (isAlreadyActive) {
            return;
        }

        tabEl.addClass('active-tab');

        tabEl.siblings().each(function(index, item) {
            $(item).removeClass('active-tab');
        });
    },

    /**
     * sets active tab in user last state
     * @param {Event} event
     */
    setActiveTab: function(event) {
        this.makeTabActiveForFirefox(event.currentTarget);

        if (this.createMode) {
            return;
        }
        var tabTarget = this.$(event.currentTarget).attr('href'),
            tabKey = app.user.lastState.key('activeTab', this),
            cidIndex = tabTarget.indexOf(this.cid);

        tabTarget = tabTarget.substring(0, cidIndex);
        app.user.lastState.set(tabKey, tabTarget);
    },
    /**
     * saves panel state in user last state
     * @param {String} panelID
     * @param {String} state
     */
    savePanelState: function(panelID, state) {
        if (this.createMode) {
            return;
        }
        var panelKey = app.user.lastState.key(panelID+':tabState', this);
        app.user.lastState.set(panelKey, state);
    },

    /**
     * Parses through an array of panels metadata and sets some of them
     * as no edit fields.
     *
     * @param {Object[]} [panels=this.meta.panels] The panels to parse.
     * @private
     */
    _setNoEditFields: function(panels) {
        var self = this;
        panels = panels || this.meta.panels;

        delete this.noEditFields;
        this.noEditFields = [];

        _.each(panels, function(panel) {
            _.each(panel.fields, function(field, index) {
                var keys = _.keys(field);
                // Make filler fields readonly
                if (keys.length === 1 && keys[0] === 'span') {
                    field.readonly = true;
                }

                /* Disable the pencil icon if the user doesn't have ACLs.
                   Collection fields may have the "fields" property, but it corresponds to fields
                   on models in the related collection, not the model itself. */
                var isCollectionField = this.model.fields[field.name] &&
                    (this.model.fields[field.name].type === 'collection');
                if (field.fields && !isCollectionField) {
                    // Some fieldsets have fields that are only for viewing, like the
                    // `copy` field on alternate addresses. Those should be filtered
                    // out of the fields list.
                    var fieldSetFields = _.filter(field.fields, function(fieldSetField) {
                        return !_.isUndefined(self.model.get(fieldSetField.name));
                    });

                    if (field.readonly || this.extraNoEditFields.indexOf(field.name) !== -1 ||
                        _.every(fieldSetFields, function(f) {
                            return !app.acl.hasAccessToModel('edit', this.model, f.name);
                        }, this)) {
                        this.noEditFields.push(field.name);
                    }
                } else if (field.readonly || !app.acl.hasAccessToModel('edit', this.model, field.name) ||
                    this.extraNoEditFields.indexOf(field.name) !== -1) {
                    this.noEditFields.push(field.name);
                }
            }, this);
        }, this);
    },

    /**
     * Returns a list of fields that are not buttons of the view.
     *
     * @private
     */
    _getNonButtonFields: function() {
        return this._filterButtonsFromFields(this.fields);
    },

    /**
     * Removes button fields from list of passed in fields
     * @param {Object} fields
     * @return {Object}
     * @private
     */
    _filterButtonsFromFields: function(fields) {
        return _.filter(fields, _.bind(function(field) {
            if (field.type === this.decoratorField) {
                return false;
            }
            if (field.name) {
                return !this.buttons[field.name];
            }

            return true;
        }, this));
    },

    /**
     * Uses {@link app.plugins.Editable} to
     * set the internal property of {@link #editableFields}.
     */
    setEditableFields: function() {
        this.editableFields = this.getEditableFields(this._getNonButtonFields(), this.noEditFields);
    },

    /**
     * Registers fields as buttons.
     *
     * @deprecated Since 7.10.
     */
    initButtons: function() {
        app.logger.warn('`BaseRecordView#initButtons` is deprecated since 7.10 and will be ' +
            'removed in a future release.');
        if (this.options.meta && this.options.meta.buttons) {
            _.each(this.options.meta.buttons, function(button) {
                this.registerFieldAsButton(button.name);
            }, this);
        }
    },

    /**
     * Registers fields as buttons.
     *
     * @protected
     */
    _initButtons: function() {
        var buttons = this.meta.buttons;
        _.each(buttons, function(button) {
            this.registerFieldAsButton(button.name);
        }, this);
    },

    showPreviousNextBtnGroup: function() {
        var listCollection = this.context.get('listCollection') || new app.data.createBeanCollection(this.module);
        var recordIndex = listCollection.indexOf(listCollection.get(this.model.id));
        if (listCollection && listCollection.models && listCollection.models.length <= 1) {
            this.showPrevNextBtnGroup = false;
        } else {
            this.showPrevNextBtnGroup = true;
        }
        if (this.collection && listCollection.length !== 0) {
            this.showPrevious = listCollection.hasPreviousModel(this.model);
            this.showNext = listCollection.hasNextModel(this.model);
        }
    },

    /**
     * Adds a button field into `this.buttons`.
     *
     * @param {string} buttonName Name of the button.
     */
    registerFieldAsButton: function(buttonName) {
        var button = this.getField(buttonName);
        if (button) {
            this.buttons[buttonName] = button;
        }
    },

    _renderHtml: function() {
        this.showPreviousNextBtnGroup();
        app.view.View.prototype._renderHtml.call(this);
        this._initButtons();
        this.setEditableFields();
        _.bind(_.debounce(this.adjustHeaderpane, 800), this)();
    },

    /**
     * Calls setEditable fields after the fields are rendered
     * @private
     */
    _renderFields: function() {
        app.view.View.prototype._renderFields.call(this);
        this.setEditableFields();
    },

    bindDataChange: function() {
        // Handle locked field changes
        this.model.on('change:locked_fields', this.handleLockedFields, this);
        this.model.on('change', function() {
            if (this.inlineEditMode) {
                this.setButtonStates(this.STATE.EDIT);
            }

            if (this.model.get('is_template')) {
                const templateEditableFields = this.getTemplateEditableFields();

                this.hidePencil(templateEditableFields);
            }
        }, this);
        this.listenTo(this, 'button:access:rendered', this._setButtonState);
    },

    /**
     * Enables or disables the action buttons that are currently shown on the
     * page. Toggles the `.disabled` class by default.
     *
     * @param {boolean} [enable=false] Whether to enable or disable the action
     *   buttons. Defaults to `false`.
     */
    toggleButtons: function(enable) {
        var state = !_.isUndefined(enable) ? !enable : false;

        _.each(this.buttons, function(button) {
            var showOn = button.def.showOn;
            if (_.isUndefined(showOn) || this.currentState === showOn) {
                button.setDisabled(state);
            }
        }, this);
    },

    duplicateClicked: function() {
        let self = this;
        let module = this.model.module || this.module;
        let prefill = app.data.createBean(module);

        prefill.copy(this.model);
        this._copyNestedCollections(this.model, prefill);
        self.model.trigger('duplicate:before', prefill);
        prefill.unset('id');
        prefill.unset('is_escalated');

        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: module,
                model: prefill,
                copiedFromModelId: this.model.get('id')
            }
        }, function(context, newModel) {
            if (newModel && newModel.id) {
                if (self.closestComponent('side-drawer')) {
                    let recordContext = {
                        layout: 'record',
                        dashboardName: newModel.get('name'),
                        context: {
                            layout: 'record',
                            name: 'record-drawer',
                            contentType: 'record',
                            module: module,
                            modelId: newModel.id,
                            dataTitle: app.sideDrawer.getDataTitle(module, 'LBL_RECORD', newModel.get('name'))
                        }
                    };
                    app.sideDrawer.open(recordContext, null, true);
                    return;
                }
                app.router.navigate(self.model.module + '/' + newModel.id, {trigger: true});
            }
        });

        prefill.trigger('duplicate:field', self.model);
    },

    /**
     * Clones the attributes that are collections by way of the
     * {@link VirtualCollection} plugin.
     *
     * This guarantees that all related models in nested collection are copied
     * instead of only the ones that have already been fetched.
     *
     * All models of the collection on the source model are fetched
     * asynchronously and then added to the same collection on the target model
     * once there are no more models to retrieve. Note that this leaves open
     * the possibility for a race condition where the user clicks the Save
     * button on the Create View before all models have been received.
     *
     * @param {Data.Bean} source
     * @param {Data.Bean} target
     * @private
     */
    _copyNestedCollections: function(source, target) {
        var collections, view;

        // only model's that utilize the VirtualCollection plugin support this
        // functionality
        if (!_.isFunction(source.getCollectionFieldNames)) {
            return;
        }

        // avoid using the ambiguous `this` since there are references to many
        // objects in this method: view, field, model, collection, source,
        // target, etc.
        view = this;

        /**
         * Removes the `_action` attribute from a model when cloning it.
         *
         * @param {Data.Bean} model
         * @return {Data.Bean}
         */
        function cloneModel(model) {
            var attributes = _.chain(model.attributes).clone().omit('_action').value();
            return app.data.createBean(model.module, attributes);
        }

        /**
         * Copies all of the models from a collection to the same collection on
         * the target model.
         *
         * @param collection
         */
        function copyCollection(collection) {
            var field, relatedFields, options;

            /**
             * Adds all of the records from the source collection to the same
             * collection on the target model.
             *
             * @param {VirtualCollection} sourceCollection
             * @param {Object} [options]
             */
            function done(sourceCollection, options) {
                var targetCollection = target.get(collection.fieldName);

                if (!targetCollection) {
                    return;
                }

                targetCollection.add(sourceCollection.map(cloneModel));
            }

            field = view.getField(collection.fieldName, source);
            relatedFields = [];

            if (field.def.fields) {
                relatedFields = _.map(field.def.fields, function(def) {
                    return _.isObject(def) ? def.name : def;
                });
            }

            options = {success: done};

            // request the related fields from the field definition if possible
            if (relatedFields.length > 0) {
                options.fields = relatedFields;
            }

            collection.fetchAll(options);
        }

        // get all attributes from the source model that are collections
        collections = _.intersection(source.getCollectionFieldNames(), _.keys(source.attributes));

        _.each(collections, function(name) {
            copyCollection(source.get(name));
        });
    },

    /**
     * Event handler for click event.
     */
    editClicked: function() {
        this.setButtonStates(this.STATE.EDIT);
        this.cancelButtonClicked = false;
        this.action = 'edit';
        this.toggleEdit(true);
        this.setRoute('edit');
    },

    saveClicked: function() {
        // Disable the action buttons.
        this.toggleButtons(false);
        var allFields = this.getFields(this.module, this.model);
        var fieldsToValidate = {};
        var erasedFields = this.model.get('_erased_fields');
        for (var fieldKey in allFields) {
            if (app.acl.hasAccessToModel('edit', this.model, fieldKey) &&
                (!_.contains(erasedFields, fieldKey) || this.model.get(fieldKey) || allFields[fieldKey].id_name)) {
                _.extend(fieldsToValidate, _.pick(allFields, fieldKey));
            }
        }
        this.model.doValidate(fieldsToValidate, _.bind(this.validationComplete, this));
    },

    /**
     * Handles when the cancel_button view event is triggered.
     *
     * FIXME: This method will be removed as part of BR-3945
     *
     * @private
     *
     * @deprecated Since 7.7. Will be removed in 7.9.
     *   Use the `MetadataEventDriven` plugin events from the
     *   `record.php` button metadata instead.
     */
    _deprecatedCancelClicked: function() {
        var cancelBtn = this.getField('cancel_button');
        if (!cancelBtn || !cancelBtn.def || !cancelBtn.def.events) {
            app.logger.warn(this.module + ': Invoking the cancel_button from `this.events` has been deprecated' +
                ' since 7.7. This handler will be removed in 7.9. Please use the `MetadataEventDriven` plugin' +
                ' events from the \'record.php\' button metadata instead.');
            this.cancelClicked.apply(this, arguments);
        }
    },

    cancelClicked: function() {
        app.alert.dismiss('cancel-dropdown-view-change');
        this.setButtonStates(this.STATE.VIEW);
        this.cancelButtonClicked = true;
        this.action = 'detail';
        this.handleCancel();
        this.clearValidationErrors(this.editableFields);
        this.setRoute();
        this.unsetContextAction();

        if (typeof this.cancelCallback === 'function') {
            this.cancelCallback();
        }
    },

    deleteClicked: function(model) {
        this.warnDelete(model);
    },

    /**
     * Render fields into either edit or view mode.
     *
     * @param {Boolean} isEdit `true` to set the field in edit mode, `false`
     *   otherwise.
     */
    toggleEdit: function(isEdit) {
        if (this.editOnly) {
            isEdit = true;
        }

        var self = this;
        this.$('.record-lock-link').toggleClass('record-lock-link-on', isEdit);
        if (this.hasLockedFields()) {
            this.warnLockedFields();
        }
        this.toggleFields(this.editableFields, isEdit, function() {
            self.toggleViewButtons(isEdit);
            self.adjustHeaderpaneFields();
        });

        this._getCurrentSugarLiveContact();
        this.createSugarLiveLinkButton();
        this.handleSugarLiveLinkButtonState(isEdit);
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
        var target = this.$(event.target);
        var cell = target.parents('.record-cell');
        var fields = cell.find('[title]');
        return fields;
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
     * Handles mousemove event.
     *
     * @param {Event} event Event object
     */
    handleMouseMove: function(event) {
        var fields = this._getMouseTargetFields(event);
        _.each(fields, function(field) {
            var rect = field.getBoundingClientRect();
            var tooltipOn = this._isTooltipOn(field);
            if (event.clientX >= rect.left && event.clientX < (rect.left + rect.width) &&
                event.clientY >= rect.top && event.clientY < (rect.top + rect.height)) {
                if (!tooltipOn) {
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
        var fields = this._getMouseTargetFields(event);
        _.each(fields, function(field) {
            var tooltipOn = this._isTooltipOn(field);
            if (tooltipOn) {
                $(field).tooltip('hide');
            }
        }, this);
    },

    /**
     * Handles mouseup event.
     *
     * @param {Event} event Event object
     */
    handleLinkWrapperMouseUp: function(event) {
        // Checks if this field is editable
        var isEF = this.$(event.target).parents('.record-cell').find('.record-edit-link-wrapper:not(.hide)').get(0);
        var isLink = this.$(event.target).attr('href');
        var isEditMode = this.action === 'edit' || this.$(event.target).parents('.record-cell').hasClass('edit');
        // This handles the case where we click on a button within a field and we want that listener to fire
        // not the one one for the record edit link wrapper.
        var hasClickableAction = this.hasClickableAction(event.target);
        var selection = window.getSelection ? window.getSelection().toString() : document.selection.createRange().text;

        if (!this.createMode && isEF && !isLink && !isEditMode && !hasClickableAction && !selection) {
            this.handleEdit(event);
        }
    },

    /**
     * Determine if the click target has an action that should stop edit mode from triggering
     * @param {HTMLElement} element
     * @return {boolean}
     */
    hasClickableAction: function(element) {
        return _.some(['data-action', 'data-clipboard'], attr => {
            return element.getAttribute(attr) || element.parentElement.getAttribute(attr);
        });
    },

    /**
     * Handler for intent to edit. This handler is called both as a callback
     * from click events, and also triggered as part of tab focus event.
     *
     * @param {Event} e Event object (should be click event).
     * @param {jQuery} cell A jQuery node cell of the target node to edit.
     */
    handleEdit: function(e, cell) {
        var target,
            cellData,
            field;

        if (e) { // If result of click event, extract target and cell.
            target = this.$(e.target);
            cell = target.parents('.record-cell');
            // hide tooltip
            this.handleMouseLeave(e);
        }

        cellData = cell.data();
        field = this.getField(cellData.name);

        const templateEditableFields = this.getTemplateEditableFields();
        const isTemplateEditable = templateEditableFields.indexOf(field.name) > -1 || !field.name;

        if (this.model.get('is_template') && !isTemplateEditable) {
            return;
        }

        // If the focus drawer icon was clicked, open the focus drawer instead
        // of entering edit mode
        if (target && target.hasClass('focus-icon') && field && field.focusIconEnabled) {
            field.handleFocusClick();
            return;
        }

        // Set Editing mode to on.
        this.inlineEditMode = true;
        this.inlineEditModeFields.push(field.name);
        this.cancelButtonClicked = false;

        this.setButtonStates(this.STATE.EDIT);

        this.toggleField(field);

        if (this.$('.headerpane').length > 0) {
            this.toggleViewButtons(true);
            this.adjustHeaderpaneFields();
        }

        this.handleSugarLiveLinkButtonState(true);
    },

    /**
     * Hide view specific button during edit.
     *
     * @param {Boolean} isEdit `true` to hide some specific buttons, `false`
     *   otherwise.
     *
     * FIXME this should be done in a more generic way (field or metadata
     * property).
     */
    toggleViewButtons: function(isEdit) {
        this.$('.headerpane .btn-group-previous-next').toggleClass('hide', isEdit);
    },

    handleSave: function() {
        if (this.disposed) {
            return;
        }
        app.alert.dismiss('cancel-dropdown-view-change');
        this._saveModel();
        this.$('.record-save-prompt').hide();

        if (!this.disposed) {
            if (this.editOnly) {
                // If we are in edit-only mode, prevent multiple saves at a time.
                // Buttons will be re-enabled after save call is complete
                this.toggleButtons(false);
            } else {
                this.setButtonStates(this.STATE.VIEW);
                this.action = 'detail';
                this.setRoute();
                this.unsetContextAction();
                this.toggleEdit(false);
                this.inlineEditMode = false;
                this.inlineEditModeFields = [];
            }
        }
    },

    /**
     * Checks if the given field, represents a temporary file type.
     *
     * @param {string} key A field name.
     * @return {boolean} True if the field is of a temporary file type.
     */
    isTemporaryFileType: function(key) {
        return this.model.fields[key] && this.model.fields[key].type === 'file_temp';
    },

    /**
     * Clears a model of any temporary file type field values in order to
     * avoid sending the same value again with another, successive update.
     */
    resetTemporaryFileFields: function() {
        _.each(Object.keys(this.model.attributes), function(key) {
            if (this.isTemporaryFileType(key)) {
                delete this.model.attributes[key];
            }
        }, this);
    },

    /**
     * Callback function for save complete
     */
    _saveModelCompleteCallback: function() {
        if (this.editOnly) {
            this.toggleButtons(true);
        }
    },

    _saveModel: function() {
        let cjFormBatch;
        var options,
            successCallback = _.bind(function() {
                this.resetTemporaryFileFields();
                // Loop through the visible subpanels and previews and have them sync. This is to update any related
                // fields to the record that may have been changed on the server on save.
                _.each(this.context.children, function(child) {
                    // This will catch the preview panel since it's loaded as a record view
                    if ((child.get('isSubpanel') && !child.get('hidden')) || child.get('isPreview')) {
                        if (child.get('collapsed')) {
                            child.resetLoadFlag({recursive: false});
                        } else {
                            child.reloadData({recursive: false});
                        }
                    }
                });
                if (this.createMode && !this.skipRouting) {
                    app.navigate(this.context, this.model);
                } else if (!this.disposed && !app.acl.hasAccessToModel('edit', this.model)) {
                    //re-render the view if the user does not have edit access after save.
                    this.render();
                }

                // tell the focus drawer that this record has been updated
                if (this.closestComponent('side-drawer')) {
                    app.sideDrawer.addUpdatedModel(this.model);
                }

                if (typeof this.saveCallback === 'function') {
                    this.saveCallback(true);
                }

                const saveRecordCallback = (view) => {
                    cjFormBatch = view;

                    if (!_.isUndefined(cjFormBatch)) {
                        const params = {
                            record: this.model.get('id'),
                            module: this.module,
                        };

                        cjFormBatch.startBatchingProcess(params);
                    }
                };

                app.CJBaseHelper.fetchActiveSmartGuideCount(this.context, this.layout, this.module,
                    this.model.get('id'),
                    saveRecordCallback
                );

            }, this);

        //Call editable to turn off key and mouse events before fields are disposed (SP-1873)
        this.turnOffEvents(this.fields);

        options = {
            showAlerts: true,
            success: successCallback,
            error: _.bind(function(model, error) {
                if (error.status === 412 && !error.request.metadataRetry) {
                    this.handleMetadataSyncError(error);
                } else if (error.status === 409) {
                    app.utils.resolve409Conflict(error, this.model, _.bind(function(model, isDatabaseData) {
                        if (model) {
                            if (isDatabaseData) {
                                successCallback();
                            } else {
                                this._saveModel();
                            }
                        }
                    }, this));
                } else if (error.status === 403 || error.status === 404) {
                    this.alerts.showNoAccessError.call(this, error);
                } else if (error.status === 422 && _.isFunction(this._handleNoEventsGenerated)) {
                    this._handleNoEventsGenerated();
                } else {
                    this.editClicked();
                }

                if (typeof this.saveCallback === 'function') {
                    this.saveCallback(false);
                }

                if (!_.isUndefined(cjFormBatch)) {
                    cjFormBatch.endBatchingProcess(false, false);
                }
            }, this),
            complete: _.bind(this._saveModelCompleteCallback, this),
            lastModified: this.model.get('date_modified'),
            viewed: true
        };

        // ensure view and field are sent as params so collection-type fields come back in the response to PUT requests
        // (they're not sent unless specifically requested)
        options.params = options.params || {};
        if (this.context.has('dataView') && _.isString(this.context.get('dataView'))) {
            // Ensure the default fetch view is also used when we want fields returned in PUT requests
            options.params.view = this.model.getOption('view') || this.context.get('dataView');
        }

        // set a flag to ensure that model is saved from front-end
        options.params.allowBatching = true;
        options = _.extend({}, options, this.getCustomSaveOptions(options));

        this.model.save({}, options);
    },

    handleMetadataSyncError: function(error) {
        var self = this;
        //On a metadata sync error, retry the save after the app is synced
        self.resavingAfterMetadataSync = true;
        app.once('app:sync:complete', function() {
            error.request.metadataRetry = true;
            self.model.once('sync', function() {
                self.resavingAfterMetadataSync = false;
                //self.model.changed = {};
                app.router.refresh();
            });
            //add a new success callback to refresh the page after the save completes
            error.request.execute(null, app.api.getMetadataHash());
        });
    },

    getCustomSaveOptions: function(options) {
        return {};
    },

    handleCancel: function() {
        this.inlineEditMode = false;
        this.inlineEditModeFields = [];
        this.model.revertAttributes({
            hideDbvWarning: true
        });
        this.toggleEdit(false);
        this._dismissAllAlerts();
        this.trigger('record:edit:cancel');
    },

    /**
     * Pre-event handler before current router is changed.
     *
     * @return {Boolean} `true` to continue routing, `false` otherwise.
     */
    beforeRouteDelete: function() {
        if (this._modelToDelete) {
            this.warnDelete(this._modelToDelete);
            return false;
        }
        return true;
    },

    /**
     * Formats the messages to display in the alerts when deleting a record.
     *
     * @return {Object} The list of messages.
     * @return {string} return.confirmation Confirmation message.
     * @return {string} return.success Success message.
     */
    getDeleteMessages: function() {
        var messages = {};
        var model = this.model;
        var name = Handlebars.Utils.escapeExpression(this._getNameForMessage(model)).trim();
        var context = app.lang.getModuleName(model.module).toLowerCase() + ' "' + name + '"';

        messages.confirmation = app.utils.formatString(
            app.lang.get('NTC_DELETE_CONFIRMATION_FORMATTED', this.module),
            [context]
        );
        messages.success = app.utils.formatString(app.lang.get('NTC_DELETE_SUCCESS'), [context]);
        return messages;
    },

    /**
     * Retrieves the name of a record
     *
     * @param {Data.Bean} model The model concerned.
     * @return {string} name of the record.
     */
    _getNameForMessage: function(model) {
        return app.utils.getRecordName(model);
    },

    /**
     * Popup dialog message to confirm delete action
     */
    warnDelete: function(model) {
        var self = this;
        this._modelToDelete = model;

        self._targetUrl = Backbone.history.getFragment();
        //Replace the url hash back to the current staying page
        if (self._targetUrl !== self._currentUrl) {
            app.router.navigate(self._currentUrl, {trigger: false, replace: true});
        }

        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: self.getDeleteMessages().confirmation,
            onConfirm: _.bind(self.deleteModel, self),
            onCancel: function() {
                self._modelToDelete = false;
            }
        });
    },

    /**
     * Popup browser dialog message to confirm delete action
     *
     * @return {String} The message to be displayed in the browser dialog.
     */
    warnDeleteOnRefresh: function() {
        if (this._modelToDelete) {
            return this.getDeleteMessages().confirmation;
        }
    },

    /**
     * Callback function for successful deletion on model
     */
    deleteModelSuccessCallback: function() {
        if (this.closestComponent('side-drawer')) {
            app.sideDrawer.addUpdatedModel(this.model);
            app.sideDrawer.closeTab(app.sideDrawer.activeTabIndex);
            return;
        }
        let redirect = this._targetUrl !== this._currentUrl;

        this.context.trigger('record:deleted', this._modelToDelete);

        this._modelToDelete = false;

        if (redirect) {
            this.unbindBeforeRouteDelete();
            //Replace the url hash back to the current staying page
            app.router.navigate(this._targetUrl, {trigger: true});
            return;
        }

        if (this.context.get('navigateBack')) {
            app.router.navigate(app.router.getFragment(), {trigger: true});
            return;
        }

        app.router.navigate(this.module, {trigger: true});
    },

    /**
     * Delete the model once the user confirms the action
     */
    deleteModel: function() {
        var self = this;

        self.model.destroy({
            //Show alerts for this request
            showAlerts: {
                'process': true,
                'success': {
                    messages: self.getDeleteMessages().success
                }
            },
            success: _.bind(this.deleteModelSuccessCallback, this)
        });

    },

    /**
     * Verify if the current target is the last one from an address block field.
     *
     * @param {View.Field} field Current focused field (field in inline-edit mode).
     * @param {String} currentTargetName attribute of the current target.
     *
     * @return {Boolean} `true` if field is the address block last field, `false` otherwise.
     **/
    isLastAddressBlockFieldSetField: function(field, currentTargetName) {
        var isFieldSet = field.type === 'fieldset';
        var lastField = _.last(field.fields);

        if (isFieldSet) {
            if (!lastField) {
                return false;
            }
            // Alternate and shipping address has no name attribute on their last field
            if (!currentTargetName) {
                return true;
            }
            return lastField.name === currentTargetName;
        } else {
            return false;
        }
    },

    /**
     * Key handlers for inline edit mode.
     *
     * Jump into the next or prev target field if `tab` key is pressed.
     * Calls {@link app.plugins.Editable#nextField} to go to next/prev field.
     *
     * @param {Event} e Event object.
     * @param {View.Field} field Current focused field (field in inline-edit mode).
     */
    handleKeyDown: function(e, field) {
        var whichField = e.shiftKey ? 'prevField' : 'nextField';

        if (e.which === 9) { // If tab
            var isFieldSet = field.type === 'fieldset';
            var isLastAddressBlockFieldSetField = this.isLastAddressBlockFieldSetField(field, e.currentTarget.name);
            // If the current field is not an address block
            // or it's the last field of an address block
            // then jumping to other fields.
            if (!isFieldSet || isLastAddressBlockFieldSetField) {
                e.preventDefault();
                this.nextField(field, whichField);
            }
            if (field.$el.closest('.headerpane').length > 0) {
                this.toggleViewButtons(false);
                this.adjustHeaderpaneFields();
            }
            if (field[whichField] && field[whichField].$el.closest('.headerpane').length > 0) {
                this.toggleViewButtons(true);
                this.adjustHeaderpaneFields();
            }
        }
    },

    /**
     * Adjust headerpane fields when they change to view mode
     */
    handleMouseDown: function() {
        this.toggleViewButtons(false);
        this.adjustHeaderpaneFields();
    },

    /**
     * Handles a field validation error for record views.
     * @param field
     * @param {Boolean} hasError True if a field has an error, false if the field is clearing it's error state
     */
    handleFieldError: function(field, hasError) {
        if(!hasError) {
            return;
        }

        var tabLink,
            fieldTab   = field.$el.closest('.tab-pane'),
            fieldPanel = field.$el.closest('.record-panel-content');

        if (field.view.meta && field.view.meta.useTabsAndPanels) {
            // If field's panel is a tab, switch to the tab that contains the field with the error
            if (fieldTab.length > 0) {
                // Make sure all previous active tab content is hidden
                this.$('.tab-pane').removeClass('active in');

                // Switch to the tab with the error
                tabLink = this.$('[href="#' + fieldTab.attr('id') + '"][data-bs-toggle="tab"]');
                tabLink.tab('show');

                // Put a ! next to the tab if one doesn't already exist
                if (tabLink.find('.sicon-error').length === 0) {
                    tabLink.append(' <i class="sicon sicon-error tab-warning"></i>');
                }

                // Make sure the new current active tab is shown
                this.$('.tab-content [id="' + fieldTab.attr('id') + '"]').addClass('active in');
            }

            // If field's panel is a panel that is closed, open it and change arrow
            if (fieldPanel && fieldPanel.is(':hidden')) {
                fieldPanel.toggle();
                var fieldPanelArrow = fieldPanel.prev().find('i');
                fieldPanelArrow.toggleClass('sicon-chevron-down sicon-chevron-right');
            }
        } else if (field.$el.is(':hidden')) {
            this.$('.more[data-moreless]').trigger('click');
            app.user.lastState.set(this.SHOW_MORE_KEY, this.$('.less[data-moreless]'));
        }
        else if (field.$el.closest('.panel_hidden.hide').length > 0) {
            this.toggleMoreLess(this.MORE_LESS_STATUS.MORE, true);
        }
    },

    /**
     * Show/hide buttons depending on the state defined for each buttons in the
     * metadata.
     *
     * @param {String} state The {@link #STATE} of the current view.
     */
    setButtonStates: function(state) {
        if (this.editOnly) {
            state = this.STATE.EDIT;
        }

        this.currentState = state;

        _.each(this.buttons, function(field) {
            this._setButtonState(field);
        }, this);

        this.toggleButtons(true);
    },

    /**
     * Show/hide a single button field depending on the current view state
     *
     * @param {Object} field the field object for the button
     * @private
     */
    _setButtonState: function(field) {
        let showOn = field.def.showOn;
        if (_.isUndefined(showOn) || (showOn === this.currentState)) {
            field.show();
        } else {
            field.hide();
        }
    },

    /**
     * Get the current button state.
     * @return {string} The current button state
     */
    getCurrentButtonState: function() {
        return this.currentState;
    },

    /**
     * Set the title in the header pane.
     *
     * @param {String} title The new title to set on the headerpane.
     *
     * FIXME this should be done with the header pane view + re-render it.
     */
    setTitle: function(title) {
        var $title = this.$('.headerpane .module-title');
        if ($title.length > 0) {
            $title.text(title);
        } else {
            this.$('.headerpane h1').prepend('<div class="record-cell"><span class="module-title">' + title + '</span></div>');
        }
    },

    /**
     * Initialize the SugarLive link button with the starting details, and listen for any changes
     * @private
     */
    _initializeSugarLiveLink: function() {
        this._getCurrentSugarLiveContact();
        app.events.on('omniconsole:contact:changed', this.handleSugarLiveContactChange, this);
    },

    /**
     * Directly get the current contact in SugarLive. This is used for getting the initial data on first load,
     * and also for refreshing when switching tabs in record dashlet
     * @private
     */
    _getCurrentSugarLiveContact: function() {
        if (app.omniConsole) {
            let ccp = app.omniConsole.getComponent('omnichannel-ccp');
            let activeContact = ccp.getActiveContact();
            let activeModel = ccp.getActiveModel();

            if (activeContact && activeModel) {
                this.handleSugarLiveContactChange(activeContact, activeModel);
            }
        }
    },

    /**
     * Listen for changes to the current SugarLive contact
     * @param contact
     * @param contactModel
     */
    handleSugarLiveContactChange: function(contact, contactModel) {
        this.sugarLiveContact = contact;
        this.sugarLiveContactModel = contactModel;
        this.showSugarLiveLinkButton = this.sugarLiveContact !== null;

        if (this.disposed || !this.sugarLiveLinkButton) {
            return;
        }

        this.handleSugarLiveLinkButtonState(this.action === 'edit' || this.inlineEditMode);
    },

    /**
     * Handle the link button state depending on if we're in edit mode or not
     * @param isEdit
     */
    handleSugarLiveLinkButtonState: function(isEdit) {
        if (this.sugarLiveLinkButton && this.sugarLiveContact) {
            let contactModule = this.sugarLiveContact.getType() === 'voice' ? 'Calls' : 'Messages';

            // Determine the correct tooltip based on the state of the link
            let tooltip;
            if (this._isLinkedToActiveContact()) {
                tooltip = app.lang.get('LBL_OMNICHANNEL_LINKED', this.module);
            } else {
                tooltip = app.lang.get('LBL_OMNICHANNEL_LINK_RECORD', this.module, {
                    module: new Handlebars.SafeString(app.lang.get('LBL_MODULE_NAME_SINGULAR', contactModule))
                });
            }

            this.sugarLiveLinkButton.setOptions({
                tooltip: tooltip,
                className: this._isLinkedToActiveContact() ? 'linked' : 'unlinked'
            });
            this.sugarLiveLinkButton.render();
        }

        this._toggleSugarLiveButtonVisibility(isEdit);
    },

    /**
     * Check if this record is already linked in some way to the current active contact
     * @return {boolean}
     * @private
     */
    _isLinkedToActiveContact: function() {
        let detail = app.omniConsole.getComponent('omnichannel-detail');
        if (!detail || !this.sugarLiveContactModel) {
            return false;
        }

        let isLinkedAsGuest = false;
        // Check this model against current linked models from omnichannel detail
        // panel to see if this model is linked to the active record
        _.each(['Contacts', 'Leads'], function(module) {
            let model = detail.getModel(null, module);
            if (model && model.get('id') === this.model.get('id')) {
                isLinkedAsGuest = true;
            }
        }, this);

        let isLinkedAsParent = this.module === this.sugarLiveContactModel.get('parent_type') &&
            this.model.get('id') === this.sugarLiveContactModel.get('parent_id');

        return isLinkedAsGuest || isLinkedAsParent;
    },

    /**
     * Hide or show the link button
     * @param isEdit
     */
    _toggleSugarLiveButtonVisibility: function(isEdit) {
        if (this.showSugarLiveLinkButton && !isEdit && this._isValidLinkableModule()) {
            this.$('.headerpane .omni-record-link').removeClass('hide');
        } else {
            this.$('.headerpane .omni-record-link').addClass('hide');
        }
    },

    /**
     * Inserts the link button
     * @param linkButton
     * @private
     */
    _insertSugarLiveButton: function(linkButton) {
        if (this.$('.headerpane .omni-record-link').length) {
            this.$('.headerpane .omni-record-link').remove();
        }

        let actionButtons = this.$('.headerpane .btn-toolbar .fieldset.actions').first();
        actionButtons.before(linkButton.$el);
    },

    /**
     * Checks if the current record is allowed to be linked to the SugarLive contact
     * @return {boolean}
     * @private
     */
    _isValidLinkableModule: function() {
        let contactModule = this.sugarLiveContact.getType() === 'voice' ? 'Calls' : 'Messages';
        let contactModuleMetadata = app.metadata.getModule(contactModule, 'fields');
        let linkableModules = app.lang.getAppListKeys(contactModuleMetadata.parent_name.options);
        return linkableModules.includes(this.module);
    },

    /**
     * Creates the SugarLive record link button
     * @private
     */
    createSugarLiveLinkButton: function() {
        if (this.sugarLiveLinkButton) {
            this._destroySugarLiveLinkButton();
        }

        let linkButton = app.view.createView({
            type: 'omnichannel-record-link',
            model: this.model
        });
        linkButton.render();
        this._insertSugarLiveButton(linkButton);

        this.sugarLiveLinkButton = linkButton;

        this.handleSugarLiveLinkButtonState(this.action === 'edit' || this.inlineEditMode);
    },

    /**
     * Cleans up the SugarLive record link button
     * @private
     */
    _destroySugarLiveLinkButton: function() {
        this.sugarLiveLinkButton.dispose();
        this.sugarLiveLinkButton = null;
    },

    /**
     * Detach the event handlers for warning delete
     */
    unbindBeforeRouteDelete: function() {
        app.routing.offBefore('route', this.beforeRouteDelete, this);
        $(window).off('beforeunload.delete' + this.cid);
    },

    _buildGridsFromPanelsMetadata: function(panels) {
        var lastTabIndex = 0;
        this.noEditFields = [];

        _.each(panels, function(panel) {
            // get user preference for labelsOnTop before iterating through
            // fields
            panel.labelsOnTop = this.getLabelPlacement();
            // it is assumed that a field is an object but it can also be a string
            // while working with the fields, might as well take the opportunity to check the user's ACLs for the field
            _.each(panel.fields, function(field, index) {
                if (_.isString(field)) {
                    panel.fields[index] = field = {name: field};
                }

                var keys = _.keys(field);

                // Make filler fields readonly
                if (keys.length === 1 && keys[0] === 'span') {
                    field.readonly = true;
                }

                // disable the pencil icon if the user doesn't have ACLs
                if (field.fields && _.isArray(field.fields)) {
                    if ((field.readonly && this.checkReadonlyFormula(field.name)) ||
                        _.contains(this.extraNoEditFields, field.name) ||
                        _.every(field.fields, function(field) {
                            return !app.acl.hasAccessToModel('edit', this.model, field.name);
                        }, this)) {
                        this.noEditFields.push(field.name);
                    }
                } else if ((field.readonly && this.checkReadonlyFormula(field.name)) ||
                    !app.acl.hasAccessToModel('edit', this.model, field.name) ||
                    _.contains(this.extraNoEditFields, field.name)) {
                    this.noEditFields.push(field.name);
                }

                // set field labelsOnTop value for use in rendering
                field.labelsOnTop = panel.labelsOnTop;
            }, this);

            // Set flag so that show more link can be displayed to show hidden panel.
            if (panel.hide) {
                this.hiddenPanelExists = true;
            }

            // labels: visibility for the label
            if (_.isUndefined(panel.labels)) {
                panel.labels = true;
            }

            if (_.isFunction(this.getGridBuilder)) {
                var options = {
                        fields: panel.fields,
                        columns: panel.columns,
                        labels: panel.labels,
                        labelsOnTop: panel.labelsOnTop,
                        tabIndex: lastTabIndex
                    },
                    gridResults = this.getGridBuilder(options).build();

                panel.grid = gridResults.grid;
                lastTabIndex = gridResults.lastTabIndex;
            }
        }, this);
    },

    /**
     * To check if readonly_formula is empty
     * @param fieldName
     * @return {*|boolean}
     */
    checkReadonlyFormula: function(fieldName) {
        return (this.model.fields[fieldName] && _.isUndefined(this.model.fields[fieldName].readonly_formula));
    },

    /**
     * Used to set labelsOnTop in views. Returns true if user preference is
     * 'field_on_top', else false.
     *
     * @return {boolean} True if user prefers 'field_on_top' otherwise false
     */
    getLabelPlacement: function() {
        return app.user.getPreference('field_name_placement') === 'field_on_top';
    },

    /**
     * Handles click event on next/previous button of record.
     * @param {Event} evt
     */
    paginateRecord: function(evt) {
        var el = $(evt.currentTarget),
            data = el.data();
        if (data.id) {
            var list = this.context.get('listCollection'),
                model = list.get(data.id);
            this._doPaginate(model, data.actionType);
        }
    },
    /**
     * paginates record view depeding on direction and model
     * @param {Object} model
     * @param {String} actionType
     * @private
     */
    _doPaginate: function(model, actionType) {
        var list = this.context.get('listCollection');
        switch (actionType) {
            case 'next':
                list.getNext(model, this.navigateModel);
                break;
            case 'prev':
                list.getPrev(model, this.navigateModel);
                break;
            default:
                this._disablePagination(el);
        }
    },
    /**
     * Callback for navigate to new model.
     *
     * @param {Data.Bean} model model New model to navigate.
     * @param {String} actionType actionType Side of navigation (prev/next).
     */
    navigateModel: function(model, actionType) {
        if (model && model.id) {
            if (app.acl.hasAccessToModel('view', model)) {
                app.router.navigate(app.router.buildRoute(this.module, model.id), {trigger: true});
            } else {
                this._doPaginate(model, actionType);
            }
        } else {
            var el = this.$el.find('[data-action=scroll][data-action-type=' + actionType + ']');
            this._disablePagination(el);
        }
    },

    /**
     * Updates url without triggering the router.
     *
     * @param {string} action Action to pass when building the route
     *   with {@link Core.Router#buildRoute}.
     */
    setRoute: function(action) {
        if (!this.meta.hashSync || this.skipRouting) {
            return;
        }
        app.router.navigate(app.router.buildRoute(this.module, this.model.id, action), {trigger: false});
    },

    /**
     * Unsets the `action` attribute from the current context.
     *
     * Once 'action' is unset, the action is 'detail' and the view will render
     * next in detail mode.
     */
    unsetContextAction: function() {
        this.context.unset('action');
    },

    /**
     * Disabling pagination if we can't paginate.
     * @param {Object} el Element to disable pagination on.
     */
    _disablePagination: function(el) {
        app.logger.error('Wrong data for record pagination. Pagination is disabled.');
        el.addClass('disabled');
        el.data('id', '');
    },

    /**
     * Adjust headerpane such that certain fields can be shown with ellipsis
     */
    adjustHeaderpane: function() {
        if (this.disposed) {
            return;
        }
        this.setContainerWidth();
        this.adjustHeaderpaneFields();
    },

    /**
     * Get the width of the layout container
     */
    getContainerWidth: function() {
        return this._containerWidth;
    },

    /**
     * Set the width of the layout container
     */
    setContainerWidth: function() {
        this._containerWidth = this._getParentLayoutWidth(this.layout);
    },

    /**
     * Get the width of the parent layout that contains `getPaneWidth()`
     * method.
     *
     * @param {View.Layout} layout The parent layout.
     * @return {Number} The parent layout width.
     * @private
     */
    _getParentLayoutWidth: function(layout) {
        if (!layout) {
            return 0;
        } else if (_.isFunction(layout.getPaneWidth)) {
            return layout.getPaneWidth(this);
        }

        return this._getParentLayoutWidth(layout.layout);
    },

    /**
     * Adjust headerpane fields such that the first field is ellipsified and the last field
     * is set to 100% on view.  On edit, the first field is set to 100%.
     */
    adjustHeaderpaneFields: function() {
        var $ellipsisCell;
        var ellipsisCellWidth;

        if (this.disposed) {
            return;
        }

        var $recordCells = this._getRecordCells();

        if ($recordCells && ($recordCells.length > 0) && (this.getContainerWidth() > 0)) {
            if ($recordCells.length === 1 && $($recordCells[0]).hasClass('btn-toolbar')) {
                this.$('.headerpane h1').css('display', 'block');
            } else {
                $ellipsisCell = $(this._getCellToEllipsify($recordCells));

                if ($ellipsisCell.length > 0) {
                    if ($ellipsisCell.hasClass('edit')) {
                        // make the ellipsis cell widen to 100% on edit
                        $ellipsisCell.css({'width': '100%'});
                    } else {
                        ellipsisCellWidth = this._calculateEllipsifiedCellWidth($recordCells, $ellipsisCell);
                        this._setMaxWidthForEllipsifiedCell($ellipsisCell, ellipsisCellWidth);
                    }
                }
            }
        }

        if (this.layout) {
            this.layout.trigger('headerpane:adjust_fields');
        }
    },

    /**
     * Get the collection of headerpane record-cell and btn-toolbar elements.
     *
     * @return {jQuery} The collection of headerpane record-cell and
     *   btn-toolbar elements.
     * @protected
     */
    _getRecordCells: function() {
        return this.$('.headerpane h1').children('.record-cell, .btn-toolbar');
    },

    /**
     * Get the first cell for the field that can be ellipsified.
     * @param {jQuery} $cells
     * @return {jQuery}
     * @private
     */
    _getCellToEllipsify: function($cells) {
        let fieldTypesToEllipsify = [
            'fullname',
            'name',
            'text',
            'base',
            'enum',
            'url',
            'dashboardtitle',
            'hint-accounts-search-dropdown',
            undefined
        ];

        return _.find($cells, function(cell) {
            return (_.indexOf(fieldTypesToEllipsify, $(cell).data('type')) !== -1);
        });
    },

    /**
     * Calculate the width for the cell that needs to be ellipsified.
     * @param {jQuery} $cells
     * @param {jQuery} $ellipsisCell
     * @return {Number}
     * @private
     */
    _calculateEllipsifiedCellWidth: function($cells, $ellipsisCell) {
        var width = this.getContainerWidth();

        _.each($cells, function(cell) {
            var $cell = $(cell);

            if ($cell.is($ellipsisCell)) {
                width -= (parseInt($ellipsisCell.css('padding-left'), 10) +
                    parseInt($ellipsisCell.css('padding-right'), 10));
            } else if ($cell.is(':visible')) {
                $cell.css({'width': 'auto'});
                width -= $cell.outerWidth();
            }
            $cell.css({'width': ''});
        });

        return width;
    },

    /**
     * Set the max-width for the specified cell.
     * @param {jQuery} $ellipsisCell
     * @param {number} width
     * @private
     */
    _setMaxWidthForEllipsifiedCell: function($ellipsisCell, width) {
        var ellipsifiedCell,
            fieldType = $ellipsisCell.data('type');
        if (fieldType === 'fullname' || fieldType === 'dashboardtitle') {
            ellipsifiedCell = this.getField($ellipsisCell.data('name'));
            if (ellipsifiedCell) {
                width -= ellipsifiedCell.getCellPadding();
                ellipsifiedCell.setMaxWidth(width);
            }
        } else {
            $ellipsisCell.css({'width': width}).children().css({'max-width': (width - 2) + 'px'});
        }
    },

    /**
     * Returns some fields to be used with app.view.View.getFieldNames() if their corresponding
     * meta attribute is true
     *
     * @private
     */
    _getDataFields: function() {
        var fields = [];

        var favorite = _.find(this.meta.panels, function(panel) {
            return _.find(panel.fields, function(field) {
                return field.type === 'favorite';
            });
        });

        var follow = _.find(this.meta.panels, function(panel) {
            return _.find(panel.fields, function(field) {
                return field.type === 'follow';
            });
        });

        if (favorite) {
            fields.push('my_favorite');
        }

        if (follow) {
            fields.push('following');
        }

        return fields;
    },

    /**
     * Extracts the field names from the metadata for directly related views/panels.
     * @param {string} [module] Module name.
     */
    getFieldNames: function(module) {
        return _.union(this._super('getFieldNames', arguments), this._getDataFields());
    },

    /**
     * Hide or show panel based on click to the panel header
     * @param {Event} e
     */
    togglePanel: function(e) {
        var $panelHeader = this.$(e.currentTarget);
        if ($panelHeader && $panelHeader.next()) {
            $panelHeader.next().toggle();
            $panelHeader.toggleClass('panel-inactive panel-active');
        }
        if ($panelHeader && $panelHeader.find('i')) {
            var $panelArrow = $panelHeader.find('i');
            $panelArrow.toggleClass('sicon-chevron-down sicon-chevron-right');
        }
        var panelName = this.$(e.currentTarget).parent().data('panelname');
        var state = 'collapsed';
        if (this.$(e.currentTarget).next().is(":visible")) {
            state = 'expanded';
        }
        this.savePanelState(panelName, state);
    },

    /**
     * Returns true if the first non-header panel has useTabs set to true
     */
    checkFirstPanel: function() {
        if (this.meta && this.meta.panels) {
            if (this.meta.panels[0] && this.meta.panels[0].newTab && !this.meta.panels[0].header) {
                return true;
            }
            if (this.meta.panels[1] && this.meta.panels[1].newTab) {
                return true;
            }
        }
        return false;
    },

    /**
     * Moves overflowing tabs into a dropdown
     */
    overflowTabs: function() {
        if (this.disposed) {
            return;
        }
        var $tabs = this.$('#recordTab > .tab:not(.dropdown)'),
            $dropdownList = this.$('#recordTab .dropdown'),
            $dropdownTabs = this.$('#recordTab .dropdown-menu li'),
            navWidth = this.$('#recordTab').width(),
            activeTabHref = this.getActiveTab(),
            $activeTab = this.$('#recordTab > .tab > a[href="'+activeTabHref+'"]').parent(),
            // Calculate available width for items in navbar
            // Includes the activetab to ensure it is displayed
            width = $activeTab.outerWidth() + $dropdownList.outerWidth();

        $tabs.each(_.bind(function (index, elem) {
            var $tab = $(elem),
                overflow;

            // Always include the active tab
            if ($tab.hasClass('active')) {
                overflow = false;
            }
            else {
                width += $tab.outerWidth();
                // Check if the tab fits in the navbar
                overflow = width >= navWidth;
            }

            // Toggle tabs in the navbar
            $tab.toggleClass('hidden', overflow);
            // Toggle items in the dropdown
            this.$($dropdownTabs[index]).toggleClass('hidden', !overflow);
        }, this));
        // Toggle the dropdown arrow
        $dropdownList.toggleClass('hidden', !$tabs.is(':hidden'));
    },

    /**
     * Takes a tab dropdown link and triggers the corresponding tab
     * @param {Event} e
     */
    triggerNavTab: function(e) {
        var tabTarget = e.currentTarget.hash,
            activeTab = this.$('#recordTab > .tab > a[href="'+tabTarget+'"]');

        e.preventDefault();
        activeTab.trigger('click');
        this.overflowTabs();
    },

    /**
     * Register keyboard shortcuts.
     */
    registerShortcuts: function() {
        app.shortcuts.register({
            id: 'Record:Edit',
            keys: ['e','mod+alt+i'],
            component: this,
            description: 'LBL_SHORTCUT_RECORD_EDIT',
            handler: function() {
                var $editButton = this.$('.headerpane [name=edit_button]');
                if ($editButton.is(':visible') && !$editButton.hasClass('disabled')) {
                    $editButton.click();
                }
            }
        });

        app.shortcuts.register({
            id: 'Record:Delete',
            keys: ['d','mod+alt+d'],
            component: this,
            description: 'LBL_SHORTCUT_RECORD_DELETE',
            handler: function() {
                this.$('.headerpane [data-bs-toggle=dropdown]:visible').click().blur();
                this.$('.headerpane [name=delete_button]:visible').click();
            }
        });

        app.shortcuts.register({
            id: 'Record:Save',
            keys: ['mod+s','mod+alt+a'],
            component: this,
            description: 'LBL_SHORTCUT_RECORD_SAVE',
            callOnFocus: true,
            handler: function() {
                var $saveButton = this.$('a[name=save_button]');
                if ($saveButton.is(':visible') && !$saveButton.hasClass('disabled')) {
                    $saveButton.click();
                }
            }
        });

        app.shortcuts.register({
            id: 'Record:Cancel',
            keys: ['esc','mod+alt+l'],
            component: this,
            description: 'LBL_SHORTCUT_RECORD_CANCEL',
            callOnFocus: true,
            handler: function() {
                var $cancelButton = this.$('a[name=cancel_button]');
                if ($cancelButton.is(':visible') && !$cancelButton.hasClass('disabled')) {
                    $cancelButton.click();
                }
                let sideDrawer = this.closestComponent('side-drawer');
                if (sideDrawer.isOpen()) {
                    sideDrawer.close();
                }
            }
        });

        app.shortcuts.register({
            id: 'Record:Previous',
            keys: 'h',
            component: this,
            description: 'LBL_SHORTCUT_RECORD_PREVIOUS',
            handler: function() {
                var $previous = this.$('.btn.previous-row');
                if ($previous.is(':visible') && !$previous.hasClass('disabled')) {
                    $previous.click();
                }
            }
        });

        app.shortcuts.register({
            id: 'Record:Next',
            keys: 'l',
            component: this,
            description: 'LBL_SHORTCUT_RECORD_NEXT',
            handler: function() {
                var $next = this.$('.btn.next-row');
                if ($next.is(':visible') && !$next.hasClass('disabled')) {
                    $next.click();
                }
            }
        });

        app.shortcuts.register({
            id: 'Record:Favorite',
            keys: 'f a',
            component: this,
            description: 'LBL_SHORTCUT_FAVORITE_RECORD',
            handler: function() {
                this.$('.headerpane .sicon-star-outline:visible').click();
            }
        });

        app.shortcuts.register({
            id: 'Record:Follow',
            keys: 'f o',
            component: this,
            description: 'LBL_SHORTCUT_FOLLOW_RECORD',
            handler: function() {
                this.$('.headerpane [name=follow]:visible').click();
            }
        });

        app.shortcuts.register({
            id: 'Record:Copy',
            keys: ['shift+c','mod+alt+u'],
            component: this,
            description: 'LBL_SHORTCUT_COPY_RECORD',
            handler: function() {
                this.$('.headerpane [data-bs-toggle=dropdown]:visible').click().blur();
                this.$('.headerpane [name=duplicate_button]:visible').click();
            }
        });

        app.shortcuts.register({
            id: 'Record:Action:More',
            keys: 'm',
            component: this,
            description: 'LBL_SHORTCUT_OPEN_MORE_ACTION',
            handler: function() {
                var $primaryDropdown = this.$('.headerpane .btn-primary[data-bs-toggle=dropdown]:visible');
                if (($primaryDropdown.length > 0) && !$primaryDropdown.hasClass('disabled')) {
                    $primaryDropdown.click();
                }
            }
        });
    },

    /**
     * Dismisses all {@link #_viewAlerts alerts} defined in this view.
     *
     * @protected
     */
    _dismissAllAlerts: function() {
        if (_.isEmpty(this._viewAlerts)) {
            return;
        }
        _.each(_.uniq(this._viewAlerts), function(alert) {
            app.alert.dismiss(alert);
        });
        this._viewAlerts = [];
    },

    /**
     * Focus the first text input available when toggling to edit mode
     */
    focusFirstInput: function(fields, viewName) {
        if (viewName === 'edit') {
            var $firstInput;
            _.find(fields, function(field) {
                var $input = field.$('input[type="text"]');
                if ($input.length > 0) {
                    $firstInput = $input;
                    return true;
                }
                return false;
            });

            if ($firstInput) {
                var $el = $firstInput.first();
                if ($el.is(':visible')) {
                    $el.focus();
                    this.setCaretToEnd($el);
                }
            }
        }
    },

    /**
     * Move the input cursor to the end
     *
     * @param {jQuery} $element
     */
    setCaretToEnd: function($element) {
        if ($element.val().length > 0) {
            var elementVal = $element.val();
            $element.val('').val(elementVal);
        }
    },

    /**
     * Add the Customer Journey Meta to module meta
     *
     * @param {Object} options
     */
    initCustomerJourneyMeta: function(options) {

        if (
            _.isEmpty(options) ||
            _.isEmpty(options.meta) ||
            _.isEmpty(options.meta.panels) ||
            _.isEmpty(options.module) ||
            _.isEmpty(app.config.customer_journey)
        ) {
            return;
        }

        let cjEnabledModules = app.CJBaseHelper.getCJEnabledModules();

        if (_.isEmpty(cjEnabledModules) || !_.contains(cjEnabledModules, options.module)) {
            return;
        }

        this.plugins = _.union([], this.plugins, ['CJAsPanelOrTab']);
        let moduleRecordView = app.metadata.getView(this.options.module, 'record');
        let currentModuleDisplaySetting = app.CJBaseHelper.getCJRecordViewSettings(options.module);
        let loadView = 'cj-panel';

        if (currentModuleDisplaySetting.includes('tab')) {
            // set loadView to tab if recordview uses tabs else set settings to panel_bottom
            if (!_.isEmpty(moduleRecordView.templateMeta) && moduleRecordView.templateMeta.useTabs) {
                loadView = 'cj-tab';
            } else {
                currentModuleDisplaySetting = 'panel_bottom';
            }
        }

        let cjPanelMeta = app.metadata.getView(null, loadView);

        /**
         * Add CJ panel at index # 1 if display settings is panel_top / tab_first
         * Else push at last index if display settings is panel_bottom / tab_last
         */
        if (app.user.hasAutomateLicense()) {
            if ((_.include(['panel_top', 'tab_first'], currentModuleDisplaySetting)) &&
                (!((_.include(['panel_top'], currentModuleDisplaySetting) &&
                    !_.isEmpty(moduleRecordView.templateMeta) &&
                    moduleRecordView.templateMeta.useTabs)))) {
                options.meta.panels.splice(1, 0, _.first(cjPanelMeta.panels));
            } else if (_.include(['tab_last'], currentModuleDisplaySetting)) {
                options.meta.panels.push(_.first(cjPanelMeta.panels));
            }
        }
    },


    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.sugarLiveLinkButton) {
            this._destroySugarLiveLinkButton();
        }

        this.unbindBeforeRouteDelete();
        _.each(this.editableFields, function(field) {
            field.nextField = null;
            field.prevField = null;
        });
        this.buttons = null;
        this.editableFields = null;
        this.inlineEditModeFields = [];
        this.stopListening();
        this.off('editable:keydown', this.handleKeyDown, this);
        $(window).off('resize.' + this.cid);
        app.events.off('focusdrawer:close', this.handleFocusDrawerClose, this);
        app.view.View.prototype._dispose.call(this);
    }
})
