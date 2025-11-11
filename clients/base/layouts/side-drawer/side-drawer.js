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
 * @class View.Layouts.Base.SideDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseSideDrawerLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritdoc
     */
    className: 'side-drawer',

    /**
     * Function to be called once drawer is closed.
     * @property {Function}
     */
    onCloseCallback: null,

    /**
     * Current drawer state: 'opening', 'idle', 'closing', ''.
     * @property {string}
     */
    currentState: '',

    /**
     * Drawer configs.
     * @property {Object}
     */
    drawerConfigs: {
        // drawer's top in pixel or percentage
        top: 0,
        // drawer's bottom in pixel or percentage
        bottom: 0,
        // drawer's right in pixel or percentage
        right: 0,
        // drawer's left in pixel or percentage
        left: '25%',
    },

    /**
     * Main content of the App.
     * @property {Object}
     */
    $main: null,

    /**
     * Store tabs for the side-drawers being opened
     * @property {Array}
     */
    _tabs: [],

    /**
     * Index of active tab
     * @property {number}
     */
    activeTabIndex: 0,

    /**
     * @inheritdoc
     */
    events: {
        'click [data-action=drawerClose]': 'close',
        'click [data-action=scroll]': 'switchRecord',
    },

    /**
     * Shortcuts.
     * @property {Array}
     */
    shortcuts: ['SideDrawer:Close'],

    /**
     * Flag indicating if close and edit actions may be performed or not at the moment.
     * @property {boolean}
     */
    areActionsEnabled: true,

    /**
     * Stores the context loaded in the drawer
     */
    currentContextDef: null,

    /**
     * Stores the context when the drawer was first opened
     */
    parentContextDef: null,

    /**
     * Flag indicating if the drawer is hidden or not
     * @property {boolean}
     */
    drawerHidden: false,

    /**
     * Models updated in this drawer
     */
    updatedModels: [],

    /**
     * Flag indicating if context layout is 'pipeline-records'.
     * @property {boolean}
     */
    isPipelineLayout: null,

    /**
     * Side drawer opening animation duration
     * @property {integer}
     */
    openingDuration: 300,

    /**
     * Side drawer closing animation duration
     * @property {integer}
     */
    closingDuration: 300,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.defaultDrawerConfigs = app.utils.deepCopy(this.drawerConfigs);
        this.$main = app.$contentEl.children().first();
        this.addDrawerHandler = _.bind(this.toggle, this);
        this.removeDrawerHandler = _.bind(this.toggle, this);
        this.$main.on('drawer:add.sidedrawer', this.addDrawerHandler);
        this.$main.on('drawer:remove.sidedrawer', this.removeDrawerHandler);

        this.before('tabbed-dashboard:switch-tab', function(params) {
            var callback = _.bind(function() {
                this._close();
                if (params.callback && _.isFunction(params.callback)) {
                    params.callback.call(this);
                }
            }, this);

            if (this.hasUnsavedChanges(callback)) {
                return false;
            }
            this._close();
            return true;
        }, this);

        // Update the prev/next buttons when the list is paginated.
        this.on('sidedrawer:collection:change', function(newCollection) {
            let context = this.getParentContextDef('parentContext');
            if (!context) {
                return;
            }
            let collection = context.get('collection');
            if (!_.isEqual(collection, newCollection)) {
                return;
            }

            this.showPreviousNextBtnGroup();
            this.focusRow();
        }, this);

        if (!app.sideDrawer) {
            app.sideDrawer = this;
        }

        // Close the drawer when navigating to a new view
        app.before('app:view:load', function() {
            return this.close();
        }, this);

        // If the side drawer is on a tile view (not the global side drawer), make sure it is not visible
        if (this.layout && (this.layout.name === 'pipeline-filterpanel' || this.layout.name === 'filterpanel')) {
            this.hide();
        }
        $(window).on('resize.' + this.cid, _.bind(_.debounce(this.resetTabs, 100), this));
    },

    /**
     * Config the drawer.
     * @param {Object} [configs={}] Drawer configs.
     */
    config: function(configs) {
        configs = configs || {};
        this.drawerConfigs = _.extend({}, this.drawerConfigs, configs);

        const imprsn = $('.impersonation-banner');
        let top = imprsn.length ? imprsn.height() : 0;
        top += this.drawerConfigs.top;

        this.$el.css('top', top);
        this.$el.css('bottom', this.drawerConfigs.bottom);
        this.$el.css('left', this.drawerConfigs.left);
        this.$el.css('right', this.drawerConfigs.right);
    },

    /**
     * Render tabs.
     */
    renderTabs: function() {
        let tabsTemplate = app.template.getLayout(this.name + '.drawer-tabs');
        this.$('.drawer-tabs').html(tabsTemplate(this));
        this.$('.drawer-tabs').find('li.tab:not(.active) a').on('click', _.bind(this.tabClicked, this));
        this.$('.drawer-tabs').find('.sicon-close').on('click', _.bind(this.closeTabClicked, this));

        this.setTabWidths();
    },

    /**
     * Calculates and sets equal width for all the tabs.
     */
    setTabWidths: function() {
        let totalWidth = this.$('.drawer-tabs').width();

        if (totalWidth && this._tabs.length) {
            this.$('.drawer-tabs .nav-tabs .tab').width(totalWidth / this._tabs.length);
        }
    },

    /**
     * Re-render tabs when window size changes.
     */
    resetTabs: function() {
        if (this.disposed || app.drawer.isOpening() || app.drawer.isClosing()) {
            return;
        }
        this.renderTabs();
    },

    /**
     * Handle tab click.
     * @param {Event} event
     */
    tabClicked: function(event) {
        let index = parseInt(event.currentTarget.getAttribute('data-index'));
        if (this.hasUnsavedChanges(_.bind(this.switchTab, this, index))) {
            return;
        }
        this.switchTab(index);
    },

    /**
     * Switch tab.
     * @param {number} index Tab index to switch to
     */
    switchTab: function(index) {
        if (index >= 0 && index < this._tabs.length) {
            this.activeTabIndex = index;
            this.resetTabs();
            this.currentContextDef = this._tabs[this.activeTabIndex];
            this.showComponent(this.currentContextDef);
        }
    },

    /**
     * Refresh active tab.
     */
    refreshTab: function() {
        let self = this;
        if (this.hasUnsavedChanges(function() {
            self.showComponent(self.currentContextDef);
        }, app.lang.get('LBL_WARN_UNSAVED_CHANGES'))) {
            return;
        }
        this.showComponent(this.currentContextDef);
    },

    /**
     * Close tab handler.
     * @param {Event} event
     */
    closeTabClicked: function(event) {
        let index = parseInt(event.currentTarget.getAttribute('data-index'));
        this.closeTab(index);
    },

    /**
     * Close tab.
     * @param {number} index Tab index to close
     */
    closeTab: function(index) {
        if (this._tabs.length > 1 && index >= 0 && index < this._tabs.length) {
            this._tabs.splice(index, 1);
            if (index < this.activeTabIndex || this.activeTabIndex === this._tabs.length) {
                this.activeTabIndex--;
            }
            this.resetTabs();
            if (this.currentContextDef != this._tabs[this.activeTabIndex]) {
                this.currentContextDef = this._tabs[this.activeTabIndex];
                this.showComponent(this.currentContextDef);
            }
        } else if (this._tabs.length === 1 && index === 0) {
            this.close();
        }
    },

    /**
     * Get active tab.
     * @return {Object}
     */
    getActiveTab: function() {
        return this.currentContextDef;
    },

    /**
     * Get tab index if it already exists.
     * @param {Object} def The component definition.
     * @return {number|null} The tab index
     */
    getTabIndex: function(def) {
        let index = null;
        _.find(this._tabs, function(tab, i) {
            if (tab.context.contentType === def.context.contentType &&
                tab.context.module === def.context.module &&
                tab.context.modelId === def.context.modelId) {
                index = i;
                return true;
            }
        });
        return index;
    },

    /**
     * Returns data to generate a html attribute 'title' and icon of FD tabs
     * @param {string} module
     * @param {string} tabType type name [RECORD | FOCUS_DRAWER]
     * @param {string} tabName
     * @return array
     */
    getDataTitle: function(module, tabType, tabName) {
        let moduleNameSingular = app.lang.get('LBL_MODULE_NAME_SINGULAR', module) || module;
        let moduleMeta = app.metadata.getModule(module);
        let labelColor = (moduleMeta) ? `label-module-color-${moduleMeta.color}` : `label-${module}`;

        return {
            module: moduleNameSingular,
            view: app.lang.get(tabType),
            name: tabName,
            labelColor: labelColor
        };
    },

    /**
     * Call _open() to open the drawer or
     * check if it's ok to open a new tab if the drawer is already open.
     *
     * @param {Object} def The component definition.
     * @param {Function} onClose Callback method when the drawer closes.
     * @param {boolean} sideDrawerClick True if the click originated from a side-drawer
     * @param {Object} $el Focus Drawer icon been clicked
     */
    open: function(def, onClose, sideDrawerClick, $el) {
        this.context.trigger('side-drawer:before:open', $el);
        if (this.isOpen() && this.hasUnsavedChanges(
            _.bind(this._open, this, def, onClose, sideDrawerClick))) {
            return;
        }
        this._open(def, onClose, sideDrawerClick);
        this.context.trigger('side-drawer:start:open', $el);
    },

    /**
     * Open the specified layout or view in this drawer.
     *
     * You can pass the current context if you want the context created to be a
     * child of that current context. If you don't pass a `scope`, it will
     * create a child of the main context (`app.controller.context`).
     *
     * @param {Object} def The component definition.
     * @param {Function} onClose Callback method when the drawer closes.
     * @param {boolean} sideDrawerClick True if the click originated from a side-drawer
     * @private
     */
    _open: function(def, onClose, sideDrawerClick) {
        this.isPipelineLayout = ('pipeline-records' === this.context.get('layout'));

        // store the callback function to be called later
        this.onCloseCallback = onClose;

        // check if it already exists
        if (sideDrawerClick) {
            let index = this.getTabIndex(def);
            if (index !== null) {
                this.switchTab(index);
                return;
            }
        }

        // set tab title
        if (def) {
            def.isFocusDashboard = true;
            if (def.context.model) {
                def.recordName = def.context.model.get('name');
            }

            def.hasTitle = !_.isUndefined(def.dashboardName);

            if (def.context.layout === 'record') {
                def.context.skipRouting = true;
            }
        }

        if (sideDrawerClick) {
            this._tabs.push(def);
        } else {
            this._tabs = [def];
        }

        this.activeTabIndex = this._tabs.length - 1;
        this.resetTabs();

        // open the drawer if not yet
        if (!this.isOpen()) {
            // Save the previous session to prevent overwriting it
            app.shortcuts.saveSession();
            app.shortcuts.createSession(this.shortcuts, this);
            this.registerShortcuts();

            this.currentState = 'opening';
            this.config();
            this.$el.show('slide', {direction: 'right'}, this.openingDuration, _.bind(this.showComponent, this, def));
            this.currentState = 'idle';
        } else {
            let _showComponent = _.bind(this.showComponent, this, def);
            if (!app.sideDrawer.triggerBefore('side-drawer:content-changed', {callback: _showComponent})) {
                return;
            }
            _showComponent();
            if (this.isHidden()) {
                this.slideIn();
            }
        }

        let oldContext = this.getParentContextDef('parentContext');
        if (oldContext && !_.isEqual(oldContext, def.context.parentContext)) {
            oldContext.trigger('focusRow');
        }
        this.currentContextDef = def;
        if (!sideDrawerClick) {
            this.parentContextDef = def;
        }
        _.defer(function() {
            this.focusRow();
        }.bind(this));
        this.showPreviousNextBtnGroup();
    },

    /**
     * Focus (highlight) a list row
     * @param id (optional) the ID of the row to focus, or the current model ID if not specified
     */
    focusRow: function(id) {
        let parentContext = this.getParentContextDef('parentContext');
        if (parentContext) {
            id = id || this.getParentContextDef('baseModelId');
            if (id) {
                parentContext.trigger('focusRow', id);
            }
        }
    },

    /**
     * Unfocus (unhighlight) any highlighted list rows
     */
    unfocusRow: function() {
        let parentContext = this.getParentContextDef('parentContext');
        if (parentContext) {
            parentContext.trigger('unfocusRow');
        }
    },

    /**
     * Enable/disable the next/prev buttons
     */
    showPreviousNextBtnGroup: function() {
        let prevButton = this.$('[data-action-type="prev"]');
        let nextButton = this.$('[data-action-type="next"]');

        let context = this.getParentContextDef('parentContext');
        if (!context || app.utils.isTruthy(this.getParentContextDef('disableRecordSwitching'))) {
            this._setButtonState(prevButton, false);
            this._setButtonState(nextButton, false);
            return;
        }

        if (this.isPipelineLayout) {
            let module = this.getParentContextDef('module') || this.context.get('module');
            let currLi = this.getParentContextDef('evtSource').closest('li');
            if (!currLi.length) {
                currLi = $('.blue-border').closest('li');
            }

            let liTags = currLi.closest('ul.column').find(`.${module}`).closest('li');

            var currLiInd = 0;
            _.each(liTags, (item, index) => {
                if (currLi.is($(item))) {
                    currLiInd = index;
                    return;
                }
            }, this);

            this._setButtonState(prevButton, currLiInd > 0);
            this._setButtonState(nextButton, currLiInd < liTags.length - 1);
            return;
        }

        let listCollection = context.get('collection') || new app.data.createBeanCollection(this.module);
        if (listCollection.length !== 0) {
            let model = listCollection.get(this.getParentContextDef('baseModelId'));
            if (!model) {
                return;
            }

            this._findNextValidRow(listCollection, model, (hasNextModel, nextModel) => {
                this._setButtonState(nextButton, hasNextModel);
            });
            this._findPrevValidRow(listCollection, model, (hasPrevModel, prevModel) => {
                this._setButtonState(prevButton, hasPrevModel);
            });
        }
    },

    /**
     * Finds the previous valid row in the collection. A row is considered valid to switch to if:
     * 1. It exists at all
     * 2. The model ID for the linked record is set (catches empty relates)
     * 3. The module name for the linked record is set (catches empty flex relates)
     * 4. The user has access to the linked record
     * @param list
     * @param model
     * @param callback the callback is passed one parameter (false) if no valid previous row exists, and
     *                 two parameters (true, model) if one exists
     * @private
     */
    _findPrevValidRow: function(list, model, callback) {
        if (!list.hasPreviousModel(model)) {
            callback(false);
            return;
        }

        list.getPrev(model, prevModel => {
            if (!this._isValidModel(prevModel)) {
                this._findPrevValidRow(list, prevModel, callback);
            } else {
                callback(true, prevModel);
            }
        });
    },

    /**
     * Finds the next valid row in the collection. A row is considered valid to switch to if:
     * 1. It exists at all
     * 2. The current model is not the last in the currently loaded collection
     * 3. The model ID for the linked record is set (catches empty relates)
     * 4. The module name for the linked record is set (catches empty flex relates)
     * 5. The user has access to the linked record
     * @param list
     * @param model
     * @param callback the callback is passed one parameter (false) if no valid next row exists, and
     *                 two parameters (true, model) if one exists
     * @private
     */
    _findNextValidRow: function(list, model, callback) {
        if (!list.hasNextModel(model) || _.isEqual(list.at(-1), model)) {
            callback(false);
            return;
        }

        list.getNext(model, nextModel => {
            if (!this._isValidModel(nextModel)) {
                this._findNextValidRow(list, nextModel, callback);
            } else {
                callback(true, nextModel);
            }
        });
    },

    /**
     * Checks if the model can be shown in the side drawer
     * @param model
     * @return {boolean}
     * @private
     */
    _isValidModel: function(model) {
        let _isInvalidModel = this.context.get('_isInvalidModel');
        if (_.isFunction(_isInvalidModel) && _isInvalidModel(model)) {
            return false;
        }
        let modelId = this._getRelatedModelId(model);
        let moduleName = this._getRelatedModuleName(model);

        return !_.isEmpty(modelId) && !_.isEmpty(moduleName) && app.acl.hasAccessToModel('view', model);
    },

    /**
     * Sets the state of the prev or next buttons.
     * @param button
     * @param state true for enabled, false for disabled
     * @private
     */
    _setButtonState: function(button, state) {
        button.prop('disabled', !state);
        button.toggleClass('disabled', !state);

        if (this.getParentContextDef('hideRecordSwitching') && !state) {
            button.hide();
        }
    },

    /**
     * Set up shortcuts for the side drawer
     */
    registerShortcuts: function() {
        // register shortcuts to close drawer
        app.shortcuts.register({
            id: 'SideDrawer:Close',
            keys: ['esc', 'mod+alt+l'],
            component: this,
            description: 'LBL_SHORTCUT_CLOSE_DRAWER',
            callOnFocus: true,
            handler: function() {
                if (this.isOpen()) {
                    this._close();
                }
            }
        });
    },

    /**
     * Show component in this drawer.
     *
     * @param {Object} def The component definition.
     */
    showComponent: function(def) {
        // remove old content
        if (this._components.length) {
            _.each(this._components, function(component) {
                component.dispose();
            }, this);
            this._components = [];
        }

        // initialize content definition components
        this._initializeComponentsFromDefinition(def);

        var component = _.last(this._components);
        if (component) {
            // load and render new content in drawer
            component.loadData();
            component.render();
        }
    },

    /**
     * Get drawer width.
     *
     * @param {View.Component} component The component.
     * @return {number} The component width.
     */
    getPaneWidth: function(component) {
        if (!this.$el) {
            return 0;
        }

        return this.$el.width() || 0;
    },

    /**
     * Tell if the drawer is opened.
     * @return {boolean} True if open, false if not.
     */
    isOpen: function() {
        return this.currentState !== '';
    },

    /**
     * Show/hide the drawer.
     */
    toggle: function() {
        if (this.isOpen()) {
            this.$el.toggle();
        }
    },

    /**
     * Determines if there are any unsaved changes
     *
     * @param {requestCallback} callback the callback
     * @param {string} message the message to show if there are unsaved changes
     * @return boolean true if has unsaved changes, false otherwise
     */
    hasUnsavedChanges: function(callback, message) {
        let params = {callback: callback};
        if (message) {
            params.message = message;
        }
        return !this.triggerBefore('side-drawer:close', params);
    },

    /**
     * Check if it's okay to close the drawer before doing so.
     *
     * @param el
     */
    close: function(el) {
        if (this.areActionsEnabled) {
            var _close = _.bind(this._close, this);
            if (this.hasUnsavedChanges(_close)) {
                return;
            }
            _close();

            let closeButtonClicked = !_.isUndefined(el) &&
                this.$(el.currentTarget).closest('.close-drawer').length > 0;

            this.currentContextDef = null;
            this.parentContextDef = null;
            if (this.context && closeButtonClicked) {
                this.context.trigger('side-drawer:start:close');
            }
        }
    },

    /**
     * Close the drawer.
     *
     * @private
     */
    _close: function() {
        if (!this.$el) {
            return;
        }

        this.currentState = 'closing';
        this.$el.hide('slide', {direction: 'right'}, this.closingDuration);
        this.currentState = '';
        this._tabs = [];
        this.drawerHidden = false;

        this.unfocusRow();

        // remove drawer content
        _.each(this._components, function(component) {
            component.dispose();
        }, this);
        this._components = [];

        // execute callback
        var callbackArgs = Array.prototype.slice.call(arguments, 0);
        if (this.onCloseCallback) {
            this.onCloseCallback.apply(window, callbackArgs);
        }

        app.events.trigger('focusdrawer:close', this.updatedModels || []);
        this.updatedModels = [];

        app.shortcuts.restoreSession();
    },

    /**
     * Add a model to the list of updated models.
     */
    addUpdatedModel: function(model) {
        if (!_.find(this.updatedModels, function(updatedModel) {
            return updatedModel.get('id') === model.get('id') &&
                updatedModel.get('_module') === model.get('_module');
        })) {
            this.updatedModels.push(model);
        }
    },

    /**
     * Slide the drawer out of view without clearing the contents
     */
    slideOut: function() {
        if (this.disposed || this.isHidden()) {
            return;
        }
        this.drawerHidden = true;
        this.$el.hide('slide', {direction: 'right'}, 300);
    },

    /**
     * Slide the drawer into view without clearing the contents
     */
    slideIn: function() {
        if (this.disposed || !this.isHidden()) {
            return;
        }
        this.drawerHidden = false;
        this.$el.show('slide', {direction: 'right'}, 300);
    },

    /**
     * Returns if the drawer has been hidden
     * @return {boolean}
     */
    isHidden: function() {
        return app.utils.isTruthy(this.drawerHidden);
    },

    /**
     * Force to create a new context and create components from the layout/view
     * definition. If the parent context is defined, make the new context as a
     * child of the parent context.
     *
     * NOTE: this function is copied from drawer.js to have consistent behavior
     *
     * @param {Object} def The layout or view definition.
     * @private
     */
    _initializeComponentsFromDefinition: function(def) {
        var parentContext;
        def = def || {};

        if (_.isUndefined(def.context)) {
            def.context = {};
        }

        if (_.isUndefined(def.context.forceNew)) {
            def.context.forceNew = true;
        }

        if (!(def.context instanceof app.Context) && def.context.parent instanceof app.Context) {
            parentContext = def.context.parent;
            // remove the `parent` property to not mess up with the context attributes.
            delete def.context.parent;
        }

        this.initComponents([def], parentContext);
    },

    /**
     * @override
     */
    _placeComponent: function(component) {
        if (this.disposed) {
            return;
        }
        this.$el.find('.drawer-content').append(component.el);
    },

    /**
     * Handles click event on next/previous button of record.
     * @param {Event} evt
     */
    switchRecord: function(evt) {
        let context = this.getParentContextDef('parentContext');
        let list = context.get('collection');
        let baseModelId = this.getParentContextDef('baseModelId');
        let model = list.get(baseModelId);
        if (!model) {
            model = list.models[0];
        }
        let actionType = evt.currentTarget.dataset.actionType;
        if (this.hasUnsavedChanges(_.bind(this._doSwitchRecord, this, model, actionType))) {
            return;
        }

        if (this.isPipelineLayout) {
            let module = this.getParentContextDef('module') || this.context.get('module');
            let currLi = this.getParentContextDef('evtSource').closest('li');
            if (!currLi.length) {
                currLi = $('.blue-border').closest('li');
            }
            let liTags = currLi.closest('ul.column').find(`.${module}`).closest('li');

            var currLiInd = 0;
            _.each(liTags, (item, index) => {
                if (currLi.is($(item))) {
                    currLiInd = index;
                    return;
                }
            }, this);

            let targetIndex = (actionType === 'prev') ? currLiInd - 1 : currLiInd + 1;
            let focusIconContainer = $(liTags[targetIndex]).find(`.${module}`).closest('.relate-field-container');
            let focusIcon = focusIconContainer.find('.focus-icon');
            focusIcon.trigger('click');
            return;
        }

        this._doSwitchRecord(model, actionType);
    },

    /**
     * Switches the record displayed in the side drawer depending on direction and model
     * @param {Object} model
     * @param {string} actionType
     * @private
     */
    _doSwitchRecord: function(model, actionType) {
        let context = this.getParentContextDef('parentContext');
        let list = context.get('collection');

        if (actionType === 'next') {
            this._findNextValidRow(list, model, (hasNextModel, nextModel) => {
                if (hasNextModel) {
                    this.switchToAnotherModel(nextModel);
                }
            });
        } else if (actionType === 'prev') {
            this._findPrevValidRow(list, model, (hasPrevModel, prevModel) => {
                if (hasPrevModel) {
                    this.switchToAnotherModel(prevModel);
                }
            });
        }
    },

    /**
     * Changes the side drawer to a new model
     * @param model
     */
    switchToAnotherModel: function(model) {
        let def = this.getParentContextDef();
        let fieldDefs = this.getParentContextDef('fieldDefs');
        let fieldValue = model.get(fieldDefs.name);
        let tabType = def.context.contentType === 'record' ?
            'LBL_RECORD' : 'LBL_FOCUS_DRAWER_DASHBOARD';
        def.dashboardName = fieldValue;
        def.context.baseModelId = model.get('id');

        // Get the correct module name and ID for the next record. The module name can vary, for
        // example with flex relate fields.
        def.context.module = this._getRelatedModuleName(model);
        def.context.modelId = this._getRelatedModelId(model);
        def.context.dataTitle = this.getDataTitle(def.context.module, tabType, fieldValue);
        this.open(def);
    },

    /**
     * Gets the module name of the related record
     * @param model
     * @return {string}
     * @private
     */
    _getRelatedModuleName: function(model) {
        let fieldDefs = this.getParentContextDef('fieldDefs');
        switch (fieldDefs.type) {
            case 'name':
            case 'fullname':
            case 'int':
                return model.get('_module');
            case 'relate':
                if (fieldDefs.module) {
                    return fieldDefs.module === 'Users' ? 'Employees' : fieldDefs.module;
                }
                let link = fieldDefs.link && model.fields && model.fields[fieldDefs.link] || {};
                if (link.module) {
                    return link.module;
                }
                return app.data.getRelatedModule(model.module, fieldDefs.link);
            case 'parent':
                return model.get('parent_type');
            default:
                return '';
        }
    },

    /**
     * Gets the ID of the related record
     * @param model
     * @return {string}
     * @private
     */
    _getRelatedModelId: function(model) {
        let fieldDefs = this.getParentContextDef('fieldDefs');
        switch (fieldDefs.type) {
            case 'name':
            case 'fullname':
            case 'int':
                return model.get('id');
            case 'relate':
                return model.get(fieldDefs.id_name);
            case 'parent':
                return model.get('parent_id');
            default:
                return '';
        }
    },

    /**
     * Getter for this.parentContextDef. Supports passing a key to return value from context
     * @param {string} key
     * @return {null|Object|*}
     */
    getParentContextDef: function(key) {
        if (key) {
            if (this.parentContextDef && this.parentContextDef.context) {
                return this.parentContextDef.context[key];
            }
        }
        return this.parentContextDef;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.renderTabs();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.$main.off('drawer:add.sidedrawer', this.addDrawerHandler);
        this.$main.off('drawer:remove.sidedrawer', this.removeDrawerHandler);
        $(window).off('resize.' + this.cid);
        this._super('_dispose');
    },
})
