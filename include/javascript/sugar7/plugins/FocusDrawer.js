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
 * Plugin provide way for fields to handle events specified in metadata.
 * You can specify those events in metadata as:
 *
 */
(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('FocusDrawer', ['view', 'field'], {
            /**
             * Stores whether focus icon should be enabled
             */
            focusIconEnabled: false,

            /**
             * When attaching the plugin, listen for the render event and add the icon if the option is set.
             */
            onAttach: function(component) {
                component.off('render');
                component.on('render', function() {
                    this.focusIconEnabled = this.checkFocusAvailability();

                    // init if this is a field
                    if (this.view) {
                        if (this.focusIconEnabled) {
                            this.initFocusIcon();
                        }

                        this.initFocusLink();
                        this.handleRecordTitleDrag();
                    }
                });
            },

            /**
             * Determines whether focus link is enabled
             * @return {boolean} true if enabled; false otherwise
             */
            checkFocusLinkAvailability: function() {
                // enabled for consoles (service and renewals) and focus drawer
                return this.$el.closest('.agent_workbench_dashboard, .console_dashboard, #side-drawer').length > 0;
            },

            /**
             * Determines whether the focus drawer is currently available to use
             * in this field
             * @return {boolean} true if the focus drawer is available; false otherwise
             */
            checkFocusAvailability: function() {
                // Check that the focus drawer has not been globally disabled in config
                var focusDrawerIsEnabled = app.utils.isTruthy(app.config.enableLinkToDrawer);

                // Check that this field type is set up for the Focus Drawer
                var fieldIsValid = this.view && _.isFunction(this.getFocusContextModule) &&
                    _.isFunction(this.getFocusContextModelId) &&
                    (!this.def || _.isUndefined(this.def.enableFocusIcon) || this.def.enableFocusIcon === true);

                // Check that Focus Drawer icons are allowed on the layout we are on
                var layoutIsValid = this._checkLayoutFocusDrawerAccess();

                return focusDrawerIsEnabled && (!this.view || fieldIsValid) &&
                    !_.isEmpty(app.sideDrawer) && layoutIsValid && !this.isBWCLink();
            },

            /**
             * Check if current element is bwc link
             *
             * @return {boolean}
             */
            isBWCLink: function() {
                const link = this.$el.find('a[data-link-target="focus"]');

                if (link.length && link.attr('href')) {
                    return link.attr('href').includes('#bwc/');
                }
                return false;
            },

            /**
             * Enable focus drawer for view.
             */
            enableFocusDrawer: function() {
                if (this.focusIconEnabled) {
                    this.initFocusIcon();
                } else {
                    this.$('.sicon-focus-drawer').hide();
                }
                this.initFocusLink();
            },

            /**
             * Adds click handler to record links.
             */
            initFocusLink: function() {
                let links = this.$el.find('a[data-link-target="focus"]');
                links.off('click.focus').on('click.focus', _.bind(this.handleRecordLink, this));
            },

            /**
             * Shows the focus icon and attaches a click listener to it to open
             * the focus drawer
             */
            initFocusIcon: function() {
                var relateFieldContainer = this.$el.find('.relate-field-container');
                var focusIconContainer = relateFieldContainer.find('.focus-icon-container');
                var isContainerHidden = focusIconContainer.hasClass('hide');

                if (isContainerHidden && this.value) {
                    focusIconContainer.toggleClass('hide');
                    var focusIcon = focusIconContainer.find('.focus-icon');
                    focusIcon.click(_.bind(this.handleFocusIcon, this));
                    // Handle opening the focus drawer with the keyboard 'Enter' key
                    focusIcon.keyup(_.bind(this.handleKeyboardClick, this));
                }
            },

            /**
             * Handle focus icon being clicked
             */
            handleFocusIcon: function(evt) {
                evt.preventDefault();
                evt.stopPropagation();
                this.handleFocusClick('dashboard', $(evt.target));
            },

            handleRecordLink: function(evt) {
                // disable click if it's being dragged
                if (!this.checkFocusLinkAvailability() || this.isDragging) {
                    return;
                }
                evt.preventDefault();
                evt.stopPropagation();
                this.handleFocusClick('record', $(evt.target));
            },

            /**
             * Checks if a record dashlet is being dragged on its title link.
             */
            handleRecordTitleDrag: function() {
                let self = this;
                self.isDragging = false;

                if (self.$el.closest('.dashlet-header')) {
                    let $draggable = self.$el.closest('.grid-stack-item.ui-draggable');
                    if ($draggable) {
                        $draggable.off('mousedown.link');
                        $draggable.on('mousedown.link', function(e) {
                            self.isDragging = false;
                        });
                        $draggable.off('dragstart.link');
                        $draggable.on('dragstart.link', function(e) {
                            self.isDragging = true;
                        });
                    }
                }
            },

            /**
             * Handle the focus icon/link being clicked
             */
            handleFocusClick: function(contentType, $el) {
                if ((contentType === 'dashboard' && this.focusIconEnabled) ||
                    contentType === 'record') {
                    var focusContext = this.getFocusContext(contentType, $el);
                    if (!focusContext.context.module) {
                        focusContext.context.module = this.module;
                    }
                    this.openFocusDrawer(focusContext, $el);
                }
            },

            /**
             * Handle the focus icon being clicked via keyboard 'Enter' key
             */
            handleKeyboardClick: function(e) {
                if (e.type === 'keyup' && e.originalEvent.key === 'Enter') {
                    this.handleFocusClick('dashboard', e);
                }
            },

            /**
             * Builds the context object to pass in to the focus drawer. Fields
             * with this plugin can implement a getFocusContextModule function
             * to specify how the context's module should be obtained, and a
             * getFocusContextModelId to specify how a context's model ID should
             * be obtained
             *
             * @return {Object} the focus context object
             */
            getFocusContext: function(contentType, $el) {
                if (contentType === 'record') {
                    return {
                        layout: 'record',
                        dashboardName: this.getFocusContextTitle($el),
                        context: {
                            layout: 'record',
                            name: 'record-drawer',
                            contentType: contentType,
                            module: $el.data('module'),
                            modelId: $el.data('model-id'),
                            dataTitle: this.getDataTitle('LBL_RECORD'),
                            parentContext: this.context,
                            baseModelId: this.model.get('id'),
                            fieldDefs: _.isFunction(this.getFocusContextFieldDefs) ?
                                this.getFocusContextFieldDefs() : this.fieldDefs
                        }
                    };
                } else {
                    return {
                        layout: 'row-model-data',
                        dashboardName: this.value,
                        context: {
                            layout: 'focus',
                            contentType: contentType,
                            module: _.isFunction(this.getFocusContextModule) ? this.getFocusContextModule() : '',
                            modelId: _.isFunction(this.getFocusContextModelId) ? this.getFocusContextModelId() : '',
                            dataTitle: this.getDataTitle('LBL_FOCUS_DRAWER_DASHBOARD'),
                            parentContext: this.context,
                            fieldDefs: this.fieldDefs,
                            baseModelId: this.model.get('id'),
                            evtSource: $el,
                            disableRecordSwitching: this._getDisableRecordSwitching()
                        }
                    };
                }
            },

            /**
             * Returns data to generate a html attribute 'title' and icon of FD tabs
             * @param {string} view type name [RECORD | FOCUS_DRAWER]
             * @return array
             */
            getDataTitle: function(term) {
                let moduleNameSingular = app.lang.get('LBL_MODULE_NAME_SINGULAR', this.module) || this.module;
                let moduleMeta = app.metadata.getModule(this.module);
                let labelColor = (moduleMeta) ? `label-module-color-${moduleMeta.color}` : `label-${this.module}`;

                return {
                    module: moduleNameSingular,
                    view: app.lang.get(term),
                    name: this.value,
                    labelColor: labelColor
                };
            },

            /**
             * Determines whether or not to disable focus drawer record switching for this field
             * @return {boolean}
             * @private
             */
            _getDisableRecordSwitching: function() {
                return this.disableFocusDrawerRecordSwitching || this.def.disableFocusDrawerRecordSwitching || false;
            },

            /**
             * Open the focus drawer with the given context
             *
             * @param context
             * @param {Object} $el
             */
            openFocusDrawer: function(context, $el) {
                if (app.sideDrawer) {
                    // If the drawer is already open to the same context, don't
                    // reload it unnecessarily
                    var drawerIsOpen = app.sideDrawer.isOpen();
                    var drawerContext = app.sideDrawer.currentContextDef;
                    if (drawerIsOpen && _.isEqual(context, drawerContext)) {
                        return;
                    }
                    var sideDrawerClick = !!this.$el && (this.$el.closest('#side-drawer').length > 0);
                    app.sideDrawer.open(context, null, sideDrawerClick, $el);
                }
            },

            /**
             * Checks whether access to the Focus Drawer is forbidden by an
             * ancestor layout
             *
             * @return {boolean} false if the Focus Drawer is disabled; true otherwise
             * @private
             */
            _checkLayoutFocusDrawerAccess: function() {
                var currLayout = this.view ? this.view.layout : this.layout;
                while (currLayout) {
                    if (currLayout.disableFocusDrawer) {
                        return false;
                    }
                    currLayout = currLayout.layout;
                }
                return true;
            }
        });
    });
})(SUGAR.App);
