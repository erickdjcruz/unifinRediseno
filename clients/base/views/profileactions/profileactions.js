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
 * @class View.Views.Base.ProfileactionsView
 * @alias SUGAR.App.view.views.BaseProfileactionsView
 * @extends View.View
 */
({
    plugins: ['Dropdown'],

    events: {
        'click .profileactions-reset-mfa': 'resetMultifactorAuthentication',
        'click .profileactions-shortcuts': 'openShortcutsDrawer',
        'click .profileactions-mobile': 'navigateToMobile',
    },

    initialize: function(options) {
        app.view.View.prototype.initialize.call(this, options);
        app.events.on("app:sync:complete", this.render, this);
        app.events.on("bwc:avatar:removed", this.bwcAvatarRemoved, this);
        app.user.on("change:picture", this.setCurrentUserData, this);
        app.shortcuts.registerGlobal({
            id: 'Shortcut:Help',
            keys: '?',
            component: this,
            description: 'LBL_SHORTCUT_HELP',
            handler: this.openShortcutsDrawer
        });
    },

    /**
     * Open shortcut help drawer.
     */
    openShortcutsDrawer: function() {
        let activeDrawerLayout = app.drawer.getActive();
        if (!activeDrawerLayout || activeDrawerLayout.type !== 'shortcuts') {
            app.drawer.open({
                layout: 'shortcuts'
            });
        } else {
            app.drawer.close();
        }
    },

    /**
     * Function to navigate to mobile view
     */
    navigateToMobile: function() {
        if (document.cookie.indexOf('sugar_mobile=') !== -1) {
            // kill sugar_mobile=0 cookie
            document.cookie = 'sugar_mobile=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
        // navigate to the same route of mobile site
        window.location = app.utils.buildUrl('mobile/') + window.location.hash;
    },

    /**
     * Render profile actions dropdown menu
     * @private
     */
    _renderHtml: function() {
        // FIXME check why the router is not loaded before all the other components are rendered
        if (!app.router || !app.api.isAuthenticated() || app.config.appStatus === 'offline') {
            return;
        }

        if (!_.isEmpty(this.meta)) {
            this.menulist = this.filterAvailableMenu(app.utils.deepCopy(this.meta));
        }
        this._super('_renderHtml');
        if (!app.shortcuts.isEnabled()) {
            this.$('.profileactions-shortcuts').addClass('hide');
        }
    },

    /**
     * Filters menu metadata
     * @param Array menuMeta
     * @return {Array}
     */
    filterAvailableMenu: function(menuMeta){
        var result = [];
        var impersonationMode = !_.isEmpty(app.cache.get('ImpersonationFor'));
        _.each(menuMeta,function(item) {
            item = this.filterMenuProperties(item);
            if (!_.isEmpty(item.acl_module) &&
                !app.acl.hasAccess(item.acl_action, item.acl_module)) {
                return;
            }

            // if current user is neither a developer nor allowed to access admin actions,
            // but the action is reserved for admin, skip this action
            if (!app.acl.hasAccessToAny('developer') &&
                !app.acl.hasAccess('admin', 'Administration') &&
                item.acl_action === 'admin') {
                return;
            }


            // in impersonation mode default logout replaced by finish impersonation strategy
            // and also disabled possibility change password or reset multi-factor auth
            if (impersonationMode) {
                if (['LBL_LOGOUT', 'LBL_CHANGE_PASSWORD', 'LBL_RESET_MFA'].includes(item.label)) {
                    return;
                }
            } else {
                if (item.label === 'LBL_FINISH_IMPERSONATING') {
                    return;
                }
            }

            result.push(item);
        },this);
        return result;
    },

    /**
     * Filters single menu data
     * @param Array menu data
     * @return {Array}
     */
    filterMenuProperties:function(singleItem){
        if(singleItem['label'] === 'LBL_PROFILE'){
            singleItem['img_url'] = this.pictureUrl;
        } else if (singleItem.label === 'LBL_CHANGE_PASSWORD' && app.config.idmModeEnabled) {
            singleItem.route += '?user_hint=' + encodeURIComponent(app.utils.createUserSrn(app.user.id));
        }
        return singleItem;
    },

    //TODO: Remove once bwc is completely pruned out of the product
    bwcProfileEntered: function() {
        //Refetch latest user data (since bwc updated avatar); reset
        var self = this;
        app.user.load(function() {
            self.setCurrentUserData();
        });
    },
    //This will get called when avatar is removed from bwc User profile edit (SP-1949)
    //TODO: Remove once bwc is completely pruned out of the product
    bwcAvatarRemoved: function() {
        app.user.set("picture", '');//so `this.pictureUrl` is falsy and default avatar kicks in on .hbs template
        this.setCurrentUserData();
    },
    /**
     * Sets the current user's information like full name, user name, avatar, etc.
     * @protected
     */
    setCurrentUserData: function() {
        this.fullName = app.user.get("full_name");
        this.userName = app.user.get("user_name");
        this.userId = app.user.get('id');
        var picture = app.user.get("picture");

        this.pictureUrl = picture ? app.api.buildFileURL({
            module: "Users",
            id: this.userId,
            field: "picture"
        }, {
            cleanCache: true
        }) : '';

        this.render();
    },
    _dispose: function() {
        if (app.user) app.user.off(null, null, this);
        app.view.View.prototype._dispose.call(this);
    },
    resetMultifactorAuthentication: function() {
        let mfaBlockContent = {
            linkToDocumentation: app.help.getDocumentationUrl('ResettingMFA')
        };
        app.alert.show('mfa_reset_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_MFA_RESET_CONFIRMATION', null, mfaBlockContent),
            confirm: {
                label: app.lang.get('LBL_MFA_RESET_CONFIRMATION_BTN'),
            },
            onConfirm: function() {
                app.api.call('update', app.api.buildURL('mfa/reset'), null, {
                    success: function(data) {
                        app.router.navigate('#logout/?clear=1', {trigger: true});
                    },
                    error: function(err) {
                        app.alert.show('mfa_reset_error', {
                            level: 'error',
                            messages: app.lang.get('EXCEPTION_FATAL_ERROR_DESC'),
                        });
                    }
                });
            }
        });
        return false;
    }
})
