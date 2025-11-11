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
 * @class View.Fields.Base.ButtonField
 * @alias SUGAR.App.view.fields.BaseButtonField
 * @extends View.Fields.Base.BaseField
 */
({
    tagName: "span",
    fieldTag: "a",

    plugins: ['MetadataEventDriven'],

    initialize: function(options) {
        var self = this;
        this.tabIndex = options.def.tabindex || 0;

        this.events = _.extend({}, {
            'click *' : 'preventClick'
        }, this.events, options.def.events);

        this._super('initialize', [options]);

        // take advantage of this hook to do the acl check
        // we use this wrapper because our spec
        // requires us to set the button.isHidden = true
        // if we don't render it.
        this.before('render', function() {
            if (self.hasAccess() && !self.isOnForbiddenLayout()) {
                this._accessPrevented = false;
                this._show();
                return true;
            }
            else {
                this._accessPrevented = true;
                this.hide();
                return false;
            }
        });

        // A user may not have general access to a module, but may have access
        // to a specific record of the module. Because of that, we need to
        // re-check for access after the model and its ACL data is loaded
        this.listenTo(this.model, 'sync', function() {
            if (this._accessPrevented && this.hasAccess()) {
                this.render();
                this.view.trigger('button:access:rendered', this);
            }
        });
    },
    _render:function() {
        this.fullRoute = _.isString(this.def.route) ? this.def.route : null;
        this.openWindow = this.def.openWindow === true;
        this.externalRoute = this.def.externalRoute === true;
        this.ariaLabel = null;
        if (!this.label || this.label.trim() === '') {
            if (this.def.tooltip) {
                this.ariaLabel = app.lang.get(this.def.tooltip, this.module);
            } else {
                this.ariaLabel = _.isString(this.def.icon) ?
                    this.def.icon.replace(/^sicon-(.*)/, '$1').replace('-', ' ') :
                    null;
            }
        }

        this.href = 'javascript:void(0);';
        if (this.fullRoute) {
            this.href = '#' + this.fullRoute;
        } else if (this.def.route) {
            var module = this.module || this.model.module ||
                ((this.context && this.context.get('module')) ? this.context.get('module') : null);
            var id = this.id || this.id;
            var action = this.def.route.action;
            this.href = app.router.buildRoute(module, id, action);
        }

        this.track = this.name ? 'click:' + this.name : 'click:' + this.label;

        app.view.Field.prototype._render.call(this);
    },

    getFieldElement: function() {
        return this.$(this.fieldTag);
    },

    /**
     * Enable or disable this button.
     * @param {boolean} [disable=true] If true, disable. If false, enable.
     * @inheritdoc
     */
    setDisabled: function(disable) {
        disable = _.isUndefined(disable) ? true : disable;
        this.def.css_class = this.def.css_class || '';
        var css_class = this.def.css_class.split(' ');
        if (disable) {
            css_class.push('disabled');
        } else {
            css_class = _.without(css_class, 'disabled');
        }
        this.tabIndex = disable ? -1 : 0;
        this.def.css_class = _.unique(_.compact(css_class)).join(' ');
        app.view.Field.prototype.setDisabled.call(this, disable);
    },

    /**
     * Prevents the `click` event from propagating further if the button is
     * in a disabled state.
     *
     * @param {Event} evt The `click` event.
     * @return {boolean} Returns `false` if the button is disabled.
     */
    preventClick: function(evt) {
        // FIXME: isDisabled should not check against `this.action`, and should
        // should eliminate the need here to check for the `disabled` class.
        // Should be fixed with SC-3418.
        if (this.isDisabled() || (!this.disposed && this.$(this.fieldTag).hasClass('disabled'))) {
            evt.preventDefault();
            evt.stopImmediatePropagation();
            return false;
        }

        const alertData = this.def.alertOnClick;

        // show alert if we have alert config setup
        if (alertData) {
            app.alert.show(alertData.id || 'button_alert_on_click', {
                level: alertData.level || 'info',
                messages: alertData.messages || ''
            });
        }
    },

    /**
     * Handles the jquery showing and event throwing
     * of the button. does no access checks.
     * @protected
     */
    _show: function() {
        if (this.isHidden !== false) {
            if (!this.triggerBefore("show")) {
                return false;
            }

            this.getFieldElement().removeClass("hide").show();
            this.isHidden = false;
            this.trigger('show');
        }
    },

    /**
     * Show this button if permissible, otherwise mark it as hidden.
     */
    show: function() {
        if(this.hasAccess()) {
            this._show();
        } else {
            this.isHidden = true;
        }
    },

    /**
     * Hide this button.
     *
     * @return {boolean} `false` if hiding was prevented by a before-event.
     *   `undefined` otherwise.
     */
    hide: function() {
        if (this.isHidden !== true) {
            if (!this.triggerBefore("hide")) {
                return false;
            }

            this.getFieldElement().addClass("hide").hide();
            this.isHidden = true;
            this.trigger('hide');
        }
    },

    /**
     * Track using the flag that is set on the hide and show from above.
     *
     * It should check the visibility by isHidden instead of DOM visibility testing
     * since actiondropdown renders its dropdown lazy
     *
     * @return {boolean}
     */
    isVisible: function() {
        return !this.isHidden;
    },

    /**
     * @inheritdoc
     *
     * No data changes to bind.
     */
    bindDomChange: function () {
    },
    /**
     * @inheritdoc
     *
     * No need to bind DOM changes to a model.
     */
    bindDataChange: function () {
    },
    /**
     * Determine if ACLs or other properties (for example, "allow_bwc") allow for the button to show
     * @return {Boolean} true if allow access, false otherwise
     */
    hasAccess: function() {
        // buttons use the acl_action and acl_module properties in metadata to denote their action for acls
        const aclModule = this.def.acl_module;
        const aclAction = this.def.acl_action;
        const isTemplate = this.model ? this.model.get('is_template') : false;

        const targetModule = this.model ? (this.model.get('_module') || this.module) : this.module;
        const moduleData = app.metadata.getModule(targetModule) || {};
        const templateSpecificActions = moduleData.templateSpecificActions || [];

        // Need to test BWC status
        if (_.isBoolean(this.def.allow_bwc) && !this.def.allow_bwc) {
            app.logger.warn('The "allow_bwc" property has been deprecated since 7.9, and will be removed in 7.10.');

            var isBwc = app.metadata.getModule(aclModule || targetModule).isBwcEnabled;
            if (isBwc) {
                // Action not allowed for BWC module
                return false;
            }
        }

        if (!isTemplate && templateSpecificActions.indexOf(aclAction) > -1) {
            return false;
        }

        if (isTemplate) {
            const restrictedActions = moduleData.templateRestrictedActions || [];

            if (restrictedActions.indexOf(aclAction) > -1 &&
                (_.isUndefined(aclModule) || aclModule === targetModule)) {
                return false;
            }
        }

        // Finally check ACLs
        if (!aclModule) {
            return app.acl.hasAccessToModel(aclAction, this.model, this);
        } else {
            return app.acl.hasAccess(aclAction, aclModule);
        }
    },

    /**
     * Check if this button is on a layout.
     *
     * @param {Object} layout the layout def
     * @return {boolean} `true` if this button is on the layout. `false` otherwise.
     * @private
     */
    _isOnLayout: function(layout) {
        var comp = this.closestComponent(layout.name);
        return comp && (layout.name !== 'dashboard' ||
             // for dashboard, either dashboard id or type should match
             // dashboard id is used by service console. type is used by new consoles
             (!_.isUndefined(layout.id) && comp.model.get('id') === layout.id) ||
             (!_.isUndefined(layout.type) && comp.model.get('metadata').type === layout.type));
    },

    /**
     * Check if this button is on a forbidden layout.
     *
     * @return {boolean} `true` if this button is on a forbidden layout, or if
     *  it is a descendant of a forbidden layout. `false` otherwise.
     */
    isOnForbiddenLayout: function() {
        if (!this.def || (!this.def.disallowed_layouts && !this.def.allowed_layouts)) {
            return false;
        }

        if (this.def.disallowed_layouts) {
            // ban this button if it has any ancestor component in the list of disallowed layouts
            if (_.any(this.def.disallowed_layouts, function(layout) {
                return this._isOnLayout(layout);
            }, this)
            ) {
                return true;
            }
        }

        if (this.def.allowed_layouts) {
            // don't ban this button if it has any ancestor component in the list of allowed layouts
            if (_.any(this.def.allowed_layouts, function(layout) {
                return this._isOnLayout(layout);
            }, this)
            ) {
                return false;
            }
            // ban this button if it doesn't have any ancestor component in the list of allowed layouts
            return true;
        }

        if (this.view && this.view.name === 'dashlet-toolbar' && app.config.platform === 'portal') {
            // hide all dashlet toolbar buttons in portal
            return true;
        }

        return false;
    },

    /**
     * Function to be overridden by any button in an action dropdown field that needs to be filtered out
     * @return {boolean} default return true for all views except on a dashlet where filtering is necessry
     */
    isAllowedDropdownButton: function() {
        // If we are in a dashlet context we need to filter things by default using these rules.
        if (this.view.name === 'dashlet-toolbar') {
            return _.contains(['edit_button'], this.name) || _.contains(['divider', 'actionbutton'], this.type);
        }
        // Return true for record view, where we do not filter anything out
        return true;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    }
})
