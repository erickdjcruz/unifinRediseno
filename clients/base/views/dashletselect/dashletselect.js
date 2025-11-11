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
 * "Add a dashlet" view.
 * Displays a list of dashlets filtered according to current module and layout.
 *
 * @class View.Views.Base.DashletselectView
 * @alias SUGAR.App.view.views.DashletselectView
 * @extends View.Views.Base.FilteredListView
 */
({
    extendsFrom: 'FilteredListView',

    /**
     * @inheritdoc
     *
     * Displays a filtered list of dashlets.
     * Uses client-generated collection with disabled load state, custom
     * preview and select actions.
     */
    initialize: function(options) {
        var meta = app.metadata.getView(null, 'dashletselect') || {};
        options.meta = _.extend({}, meta, options.meta || {});

        this._super('initialize', [options]);

        // To avoid reset while sorting.
        this.context = _.extend(_.clone(this.context), {
            resetLoadFlag: function() {
                return;
            }
        });

        this.context.on('dashletlist:select-and-edit', function(model) {
            this.selectDashlet(model.get('metadata'));
        }, this);

        this.context.on('dashletlist:preview:fire', function(model) {
            let currentElem = this.$('tr[data-type=' + model.get('type') + '] a.rowaction.btn');

            this.$('a.rowaction.btn').removeClass('active');
            currentElem.addClass('active');

            this.previewDashlet(model.get('metadata'));
        }, this);
    },

    /**
     * Load dashlet preview by passing preview metadata
     *
     * @param {Object} metadata Preview metadata.
     */
    previewDashlet: function(metadata) {
        var layout = this.layout,
            previewLayout;
        while (layout) {
            if (layout.getComponent('preview-pane')) {
                previewLayout = layout.getComponent('preview-pane').getComponent('dashlet-preview');
                previewLayout.showPreviewPanel();
                break;
            }
            layout = layout.layout;
        }

        if (previewLayout) {
            // If there is no preview property, use the config property
            if (!metadata.preview) {
                metadata.preview = metadata.config;
            }
            var previousComponent = _.last(previewLayout._components);
            if (previousComponent.name !== 'dashlet-preview') {
                var index = previewLayout._components.length - 1;
                previewLayout._components[index].dispose();
                previewLayout.removeComponent(index);
            }

            if (app.controller.context.get('layout') == 'portaltheme-config' && app.metadata.portalModules) {
                metadata.preview.module = app.metadata.portalModules[0];
            }

            var contextDef,
                component = {
                    label: app.lang.get(metadata.label, metadata.preview.module),
                    type: metadata.type,
                    preview: true
                };
            if (metadata.preview.module || metadata.preview.link) {
                contextDef = {
                    skipFetch: false,
                    forceNew: true,
                    module: metadata.preview.module,
                    link: metadata.preview.link
                };
            } else if (metadata.module) {
                contextDef = {
                    module: metadata.module
                };
            }

            component.view = _.extend({module: metadata.module}, metadata.preview, component);
            if (contextDef) {
                component.context = contextDef;
            }

            previewLayout.initComponents([
                {
                    layout: {
                        type: 'dashlet-grid-wrapper',
                        label: app.lang.get(metadata.preview.label || metadata.label, metadata.preview.module),
                        preview: true,
                        components: [
                            component
                        ]
                    }
                }
            ], this.context);
            previewLayout.loadData();
            previewLayout.render();
        }
    },

    /**
     * Load dashlet configuration view by passing configuration metadata
     *
     * @param {Object} metadata Configuration metadata.
     */
    selectDashlet: function(metadata) {
        var model = new app.Bean();

        // On the multi-line list and focus view, side drawer/focus drawer the dashlets need
        // the correct module context, which is set here.
        var contextModule;
        var contextModel = this.context.get('model');
        if (contextModel && _.contains(['multi-line', 'focus'], contextModel.get('view_name'))) {
            contextModule = contextModel.get('dashboard_module');
        }
        // Set module for SugarLive console
        if (contextModel && contextModel.get('view_name') === 'omnichannel' && this.context.parent) {
            contextModule = this.context.parent.get('module');
        }
        var dashletConfigModule = metadata.config.module || metadata.module || contextModule;
        let context = {moduleSingular: app.lang.getModuleName(dashletConfigModule)};

        app.drawer.load({
            layout: {
                type: 'dashletconfiguration',
                components: [
                    {
                        view: _.extend({}, metadata.config, {
                            label: app.lang.get(metadata.label, metadata.config.module, context),
                            type: metadata.type,
                            config: true,
                            module: dashletConfigModule
                        })
                    }
                ]
            },
            context: {
                module: dashletConfigModule,
                model: model,
                forceNew: true,
                skipFetch: true
            }
        });
    },

    /**
     * Filtering the available dashlets with the current page's module and
     * layout view.
     *
     * @param {Array} dashlets A list of dashlet components.
     * @return {Array} A list of filtered dashlet set.
     */
    getFilteredList: function(dashlets) {
        var alwaysRecordViewDashboards = ['multi-line', 'focus', 'omnichannel'];
        var useRecordViewDashlets = this.context && this.context.parent &&
            _.contains(alwaysRecordViewDashboards, this.context.parent.get('layout'));

        var parentContext = useRecordViewDashlets ? this.context.parent : app.controller.context;
        var parentModule = parentContext.get('module');

        // always show record view dashlets for certain dashboards
        var parentView = useRecordViewDashlets ? 'record' : parentContext.get('layout');
        var externalAppDashlet;
        var filteredDashlets = _.chain(dashlets)
            .filter(function(dashlet) {
                var filter = dashlet.filter;
                // if there is no filter for this dashlet, include it
                if (_.isUndefined(filter)) {
                    return true;
                }

                if (dashlet.type === 'external-app-dashlet') {
                    // save a reference to the external-app-dashlet def
                    // since we're already looping over them
                    externalAppDashlet = dashlet;
                    // don't include external-app-dashlet in the list of dashlets yet
                    return false;
                }

                var filterModules = filter.module || [parentModule];
                var filterViews = filter.view || [parentView];
                let filterLicenses = filter.licenseType ? filter.licenseType : app.user.get('licenses');

                if (_.isString(filterModules)) {
                    filterModules = [filterModules];
                }
                if (_.isString(filterViews)) {
                    filterViews = [filterViews];
                }

                if (_.isString(filterLicenses)) {
                    filterLicenses = [filterLicenses];
                }

                // if the filter is matched, then this will be true
                var inModuleAndView = _.contains(filterModules, parentModule) && _.contains(filterViews, parentView);

                // check if we got the right license
                let hasValidLicense = filterLicenses ? app.user.hasLicense(filterLicenses) : true;

                // also allow blacklisting in addition to whitelisting
                var blacklisted = false;
                if (filter.blacklist) {
                    filterModules = filter.blacklist.module || [];
                    if (_.isString(filterModules)) {
                        filterModules = [filterModules];
                    }
                    filterViews = filter.blacklist.view || [];
                    if (_.isString(filterViews)) {
                        filterViews = [filterViews];
                    }

                    blacklisted = _.contains(filterModules, parentModule) || _.contains(filterViews, parentView);
                }

                return inModuleAndView && hasValidLicense && !blacklisted;
            })
            .value();

        if (app.config.catalogEnabled) {
            // only do this check if Catalog is enabled
            var dashletView = parentView === 'records' ? 'list-dashlet' : 'record-dashlet';
            var dashletMeta = app.metadata.getLayout(parentModule, dashletView);
            if (dashletMeta) {
                // loop through components to see if any of them call for
                // customConfig true -- convert those to dashlet view metadata
                _.each(dashletMeta.components, function(comp) {
                    if (comp.view.customConfig) {
                        filteredDashlets = filteredDashlets.concat(app.utils.convertCompToDashletView(comp));
                    }
                }, this);
                // if there is a Sugar App already set to load in this module's list or record dashlet spot
                // add the external-app-dashlet def to the filteredDashlets
                filteredDashlets.push(externalAppDashlet);
            }
        }
        return filteredDashlets;
    },

    /**
     * Iterates dashlets metadata and extract the dashlet components among them.
     *
     * @param {String} type The component type (layout|view).
     * @param {String} name The component name.
     * @param {String} module The module name.
     * @param {Object} meta The metadata.
     * @return {Array} list of available dashlets.
     * @private
     */
    _getDashlets: function(type, name, module, meta) {
        var dashlets = [],
            hadDashlet = meta && meta.dashlets &&
                app.view.componentHasPlugin({
                    type: type,
                    name: name,
                    module: module,
                    plugin: 'Dashlet'
                });
        if (!hadDashlet) {
            return dashlets;
        }
        _.each(meta.dashlets, function(dashlet) {
            if (!dashlet.config) {
                return;
            }
            let context = {moduleSingular: app.lang.getModuleName(dashlet.config.module)};
            var description = app.lang.get(dashlet.description, dashlet.config.module);
            if (!app.acl.hasAccess('access', module || dashlet.config.module)) {
                return;
            }
            dashlets.push({
                type: name,
                filter: dashlet.filter,
                metadata: _.extend({
                    component: name,
                    module: module,
                    type: name
                }, dashlet),
                title: app.lang.get(dashlet.label, dashlet.config.module, context),
                description: description
            });
        }, this);
        return dashlets;
    },

    /**
     * Retrieves all base view's metadata.
     *
     * @return {Array} All base view's metadata.
     * @private
     */
    _addBaseViews: function() {
        var components = [];
        _.each(app.metadata.getView(), function(view, name) {
            var dashlets = this._getDashlets('view', name, null, view.meta);
            if (!_.isEmpty(dashlets)) {
                components = _.union(components, dashlets);
            }
        }, this);
        return components;
    },

    /**
     * Retrieves all module view's metadata.
     *
     * @return {Array} The module view's metadata.
     * @private
     */
    _addModuleViews: function() {
        var components = [];
        _.each(app.metadata.getModuleNames({filter: 'visible'}), function(module) {
            _.each(app.metadata.getView(module), function(view, name) {
                var dashlets = this._getDashlets('view', name, module, view.meta);
                if (!_.isEmpty(dashlets)) {
                    components = _.union(components, dashlets);
                }
            }, this);
        }, this);
        return components;
    },

    /**
     * @inheritdoc
     *
     * Instead of fetching context, it will retrieve all dashable components
     * based on metadata. Sorts the components by `title` alphabetically.
     */
    loadData: function() {
        if (this.collection.length) {
            this.filteredCollection = this.collection.models;
            return;
        }

        var dashletCollection = _.union(this._addBaseViews(), this._addModuleViews()),
            filteredDashletCollection = this.getFilteredList(dashletCollection);

        this.collection.comparator = function(model) {
            return model.get('title');
        };

        this.collection.add(filteredDashletCollection);
        this.collection.dataFetched = true;
        this._renderData();
    },

    /**
     * @inheritdoc
     *
     * DashletSelect isn't a read module, no need to compare fields with defs.
     */
    getFields: function() {
        return _.flatten(_.pluck(this.meta.panels, 'fields'));
    }

})
