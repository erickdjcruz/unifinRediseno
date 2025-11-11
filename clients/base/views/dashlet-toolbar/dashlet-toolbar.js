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
 * @class View.Views.Base.DashletToolbarView
 * @alias SUGAR.App.view.views.BaseDashletToolbarView
 * @extends View.View
 */
({
    className: 'dashlet-header flex flex-row items-center m-0.75',
    cssIconDefault: 'sicon sicon-settings',
    cssIconRefresh: 'sicon sicon-refresh sicon-is-spinning',
    defaultActions: {
        'dashlet:edit:clicked' : 'editClicked',
        'dashlet:viewReport:clicked': 'viewReportClicked',
        'dashlet:refresh:clicked' : 'refreshClicked',
        'dashlet:delete:clicked' : 'removeClicked',
        'dashlet:toggle:clicked' : 'toggleMinify'
    },
    events: {
        'shown.bs.dropdown': '_toggleAria',
        'hidden.bs.dropdown': '_toggleAria'
    },

    plugins: [
        'SugarLogic'
    ],

    /**
     * Button states.
     */
    _STATE: {
        EDIT: 'edit',
        VIEW: 'view'
    },

    /**
     * List of fields to display in the header.
     *
     * @property {Object[]|null}
     */
    headerFields: null,

    /**
     * The total number of search results for all modules.
     *
     * @property {number|null}
     */
    modulesNumber: null,

    initialize: function(options) {
        _.extend(options.meta, app.metadata.getView(null, 'dashlet-toolbar'), options.meta.toolbar);
        app.view.View.prototype.initialize.call(this, options);
        var model = this.closestComponent('dashboard') ?
            this.closestComponent('dashboard').model : this.model;

        /**
         * A flag to indicate if the dashlet is editable.
         *
         * @type {boolean}
         */
        this.canEdit = app.acl.hasAccessToModel('edit', model) || false;

        this.buttons = this.meta.buttons;

        // filter buttons depending on if the dashboard is a template or not
        const templateRestrictedActions = ['editClicked', 'removeClicked'];
        _.each(this.buttons, (buttons, buttonsIdx) => {
            if (buttons.dropdown_buttons) {
                const dButtons = buttons.dropdown_buttons;

                for (let idx = dButtons.length - 1; idx >= 0; idx--) {
                    const dButton = dButtons[idx];

                    if (dButton &&
                        this.layout &&
                        this.layout.model &&
                        templateRestrictedActions.indexOf(dButton.action) > -1 &&
                        this.layout.model.get('is_template')) {
                        this.buttons[buttonsIdx].dropdown_buttons.splice(idx, 1);
                    }
                }
            }
        });

        this.adjustHeaderPaneTitle = _.bind(_.debounce(this.adjustHeaderPaneTitle, 50), this);
        $(window).on('resize.' + this.cid, this.adjustHeaderPaneTitle);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.listenTo(this.context, 'dashlet:toolbar:change', this._handleToolbarChange);
        this.listenTo(this.context.parent, 'search:modules:number:change', (modulesNumber) => {
            this.modulesNumber = modulesNumber;
            this.render();
        });
    },

    /**
     * Handles when the toolbar needs to change (new fields, buttons, model, etc.)
     *
     * @param {Array} headerFields the new header field definitions
     * @param {Array} headerButtons the new header button definitions
     * @param {Bean} dashletModel the model used in the dashlet if applicable
     * @param {Object} dashlet the dashlet view
     * @private
     */
    _handleToolbarChange: function(headerFields, headerButtons, dashletModel, dashlet) {
        this.headerFields = headerFields;
        this.buttons = _.union(headerButtons, this.meta.buttons);
        if (dashletModel) {
            this.dashletModel = dashletModel;
        }
        if (dashlet) {
            this.dashlet = dashlet;
        }
        this.render();

        // Restart SugarLogic to initialize dependencies for any changed module context
        this.context.set('module', dashletModel ? dashletModel.module : 'Home');
        this.collection.reset(dashletModel);
        this.stopSugarLogic();
        this.startSugarLogic();
    },

    /**
     * Adjust header pane dashlet title such that the field is ellipsified.
     */
    adjustHeaderPaneTitle: function() {
        // this is required due to _.debounce adding a setTimeout.
        if (this.disposed) {
            return;
        }

        var isDataTypeFullName = _.contains(_.pluck(this.headerFields, 'type'), 'fullname');
        if (isDataTypeFullName) {
            // Left side sibling record-cells width.
            var recordCellsWidth = 0;
            this.$('.record-cell').each(function() {
                recordCellsWidth += $(this).outerWidth(true);
            });
            // Right side buttons width.
            var btnGroupWidth = this.$('.pull-right').outerWidth(true);
            // Parent header panel width.
            var headerPaneWidth = this.$el.closest('.dashlet-header').width();
            // Dashlet record title is positioned as the child element of the second record-cell.
            // Calculate title width by subtracting the record-cell and btn-group width from parent headerPane width.
            var titleWidth = headerPaneWidth - btnGroupWidth - recordCellsWidth;

            this.$('.dashlet-open-container').css({'max-width': titleWidth + 'px'});
        }
    },

    /**
     * @inheritdoc
     *
     * Handle the record state if this is a toolbar for a dashablerecord.
     */
    _render: function() {
        this._super('_render');
        this.adjustHeaderPaneTitle();
        if (this.dashlet) {
            this._handleRecordState(this.dashlet && this.dashlet.action);
        }
    },

    /**
     * Handle changes between edit/detail mode (for record view dashlets).
     *
     * @param {string} action Action name.
     * @private
     */
    _handleRecordState: function(action) {
        if (action === 'edit' && _.isFunction(this.toggleEdit)) {
            this.setButtonStates(this._STATE.EDIT);
            this.toggleEdit(true);
        } else {
            this.setButtonStates(this._STATE.VIEW);
        }
    },

    /**
     * Show/hide buttons depending on the state defined for each buttons in the
     * metadata.
     *
     * @param {string} state The {@link #_STATE} of the current view.
     */
    setButtonStates: function(state) {
        this.currentState = state;

        _.each(this.buttons, function(field) {
            field = this.getField(field.name);
            if (!field) {
                return;
            }
            var showOn = field.def && field.def.showOn;
            if (_.isUndefined(showOn) || (showOn === state)) {
                field.show();
            } else {
                field.hide();
            }
        }, this);

        this.toggleButtons(true);
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
            let buttonMeta = button;
            button = this.getField(button.name);
            if (!button) {
                return;
            }

            var showOn = button.def && button.def.showOn;
            if (_.isUndefined(showOn) || this.currentState === showOn) {
                button.setDisabled(state);
            }

            //disable edit button for system currency only
            const dropdownButtonsKey = 'dropdown_buttons';
            if (buttonMeta[dropdownButtonsKey]) {
                _.each(buttonMeta[dropdownButtonsKey], function(dropdownButton) {
                    if (dropdownButton.name && dropdownButton.name === 'edit_button' && this.dashletModel &&
                        this.dashletModel.id === app.currency.getBaseCurrencyId()) {
                        let $dropdownButton = this.getField(dropdownButton.name);
                        $dropdownButton.setDisabled(true);
                        this.dashletModel.set('name', app.lang.get('LBL_CURRENCY_DEFAULT', 'Currencies'));
                    }
                }, this);
            }
        }, this);
        this.adjustHeaderPaneTitle();
    },

    /**
     * Change to the spinning icon to indicate that loading process is triggered
     */
    refreshClicked: function() {
        var $el = this.$('[data-action=loading]');
        var options = {};
        if ($el.length > 0) {
            $el.removeClass(this.cssIconDefault).addClass(this.cssIconRefresh);
            options.complete = _.bind(function() {
                if (this.disposed) {
                    return;
                }
                $el.removeClass(this.cssIconRefresh).addClass(this.cssIconDefault);

                // If the user refreshes a collapsed dashlet, set the right toggle icon
                if (this.layout.isDashletCollapsed()) {
                    this.$('.dashlet-toggle > i').toggleClass('sicon-chevron-down', true);
                    this.$('.dashlet-toggle > i').toggleClass('sicon-chevron-up', false);
                }
            }, this);
        }
        this.layout.reloadDashlet(options);
    },

    /**
     * Remove a dashlet.
     *
     * @param {Event} evt Mouse event.
     */
    removeClicked: function(evt) {
        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_REMOVE_DASHLET_CONFIRM', this.module),
            onConfirm: _.bind(function() {
                this.layout.removeDashlet();
            }, this)
        });
    },

    /**
     * View report.
     *
     * @param {Event} evt Mouse event.
     */
    viewReportClicked: function(evt) {
        this.layout.viewReport();
    },

    /**
     * Edit the dashlet.
     *
     * @param {Event} evt The click event.
     */
    editClicked: function(evt) {
        this.layout.editDashlet();
    },

    /**
     * Toggle current dashlet frame when user clicks the toolbar action
     *
     * @param {Event} mouse event.
     */
    toggleClicked: function(evt) {
        var $btn = $(evt.currentTarget);
        var expanded = _.isUndefined($btn.data('expanded')) ? true : $btn.data('expanded');
        var label = expanded ? 'LBL_DASHLET_MAXIMIZE' : 'LBL_DASHLET_MINIMIZE';

        $btn.html(app.lang.get(label, this.module));
        this.layout.collapse(expanded);
        $btn.data('expanded', !expanded);
    },

    /**
     * Toggle current dashlet frame when user clicks chevron icon
     *
     * @param {Window.Event} mouse event.
     */
    toggleMinify: function(evt) {
        var $el = this.$('.dashlet-toggle > i');
        var collapsed = $el.is('.sicon-chevron-up');
        this.layout.collapse(collapsed);
        //firing an event to notify dashlet expand / collapse
        this.layout.trigger('dashlet:collapse', collapsed);
    },

    /**
     * Sets a button accessibility class 'aria-expanded' to true or false
     * depending on if the dropdown menu is open or closed.
     *
     * @private
     */
    _toggleAria: function() {
        var $button = this.$('[data-bs-toggle=dropdown]');
        var $group = $button.parent();
        $button.attr('aria-expanded', $group.hasClass('open'));
    },

    /**
     * Remove event listeners on dispose
     * @private
     */
    _dispose: function() {
        $(window).off('resize.' + this.cid);
        this.stopListening();
        this._super('_dispose');
    }

})
