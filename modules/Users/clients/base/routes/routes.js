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
(function(app) {
    app.events.on('router:init', function(router) {
        var routes = [
            {
                name: 'list',
                route: 'Users',
                callback: function() {
                    let acls = app.user.getAcls();

                    if (app.user.get('type') === 'admin' || acls.Users.developer !== 'no') {
                        app.router.list('Users');
                    } else {
                        app.controller.loadView({layout: 'access-denied'});
                    }
                }
            },
            {
                name: 'user-utilities-copy-content',
                route: ':Users/copy-content',
                callback: function(module) {
                    app.controller.loadView({
                        layout: 'copy-content',
                        module: module
                    });
                }
            },
            {
                name: 'user-utilities-update-locale',
                route: ':Users/update-locale',
                callback: function(module) {
                    app.controller.loadView({
                        layout: 'copy-user-settings',
                        module: module
                    });
                }
            },
            {
                name: 'create-group',
                route: 'Users/create/group',
                callback: function() {
                    if (!app.controller.context.get('layout')) {
                        app.controller.loadView({
                            module: 'Users',
                            layout: 'records'
                        });
                    }

                    let newUserModel = app.data.createBean('Users', {
                        is_admin: false,
                        is_group: true,
                        portal_only: false
                    });

                    app.drawer.open({
                        layout: 'create',
                        context: {
                            module: 'Users',
                            create: true,
                            fromRouter: true,
                            userType: 'group',
                            model: newUserModel
                        }
                    }, function(context, model) {
                        if (model && model.module === app.controller.context.get('module')) {
                            app.controller.context.reloadData();
                        }
                    });
                }
            },
            {
                name: 'create-portalapi',
                route: 'Users/create/portalapi',
                callback: function() {
                    if (!app.controller.context.get('layout')) {
                        app.controller.loadView({
                            module: 'Users',
                            layout: 'records'
                        });
                    }

                    let newUserModel = app.data.createBean('Users', {
                        is_admin: false,
                        is_group: false,
                        portal_only: true
                    });

                    app.drawer.open({
                        layout: 'create',
                        context: {
                            module: 'Users',
                            create: true,
                            fromRouter: true,
                            userType: 'portalapi',
                            model: newUserModel
                        }
                    }, function(context, model) {
                        if (model && model.module === app.controller.context.get('module')) {
                            app.controller.context.reloadData();
                        }
                    });
                }
            }
        ];
        app.router.addRoutes(routes);
    });
})(SUGAR.App);
