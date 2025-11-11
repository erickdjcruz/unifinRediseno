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
 * @class View.Layouts.Base.DashletLayout
 * @alias SUGAR.App.view.layouts.BaseDashletLayout
 * @extends View.Layout
 */
({
    /**
     * A reference to the main dashboard
     */
    dashboard: undefined,

    /**
     * Reference to the component to be reloaded when the refresh button is clicked.
     * If not set, the first component will be used.
     */
    reloadableComponent: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.index = options.meta.index;
        this._super('initialize', [options]);

        if (!(this.meta.preview || this.meta.empty)) {
            // grab a reference to the dashboard to pass down
            this.dashboard = this.findLayout('dashboard', options.layout);
        }

        //set current model draggable
        this.on('render', function() {
            // If the user does not have write access, don't display drag/drop cursor
            if (!app.acl.hasAccessToModel('edit', this.model)) {
                this.$('[data-toggle=dashlet]').css('cursor', 'default');
            }
        }, this);
        this.context.on('dashboard:collapse:fire', this.collapse, this);
        app.events.on('focusdrawer:close', this.handleFocusDrawerClose, this);

        let targetLayout = (this.context && this.context.parent) ? this.context.parent.get('layout') : null;
        if (app.controller.context.get('targetLayout') !== targetLayout) {
            app.controller.context.set('targetLayout', targetLayout);
        }
    },

    /**
     * Refresh dashlet if any records have been updated
     * after the focus drawer is closed.
     * @param {Array} updatedModels
     */
    handleFocusDrawerClose: function(updatedModels) {
        if (!_.isEmpty(updatedModels)) {
            this.reloadDashlet();
        }
    },

    /**
     * Search recursively through the <pre><code>layout.layout</code></pre> list
     * until the <pre><code>layout.name == name</code></pre>
     *
     * @param {String} name the name of the layout you're looking for
     * @param {Object} layout the layout object to look through
     * @return {Mixed}
     */
    findLayout: function(name, layout) {
        return (layout.name === name || layout.type === name) ?
            layout :
            layout.layout ?
                this.findLayout(name, layout.layout) :
                null;
    },

    /**
     * @inheritdoc
     * Append dashlet toolbar view based on custom_toolbar definition
     *
     * @param {Array} list of component metadata
     */
    _addComponentsFromDef: function(components) {
        if (!(this.meta.preview || this.meta.empty)) {
            var dashletDef = _.first(components),
                dashletMeta,
                dashletModule,
                toolbar = {},
                pattern = /^(LBL|TPL|NTC|MSG)_(_|[a-zA-Z0-9])*$/,
                label = this.meta.label;
            //try to get the dashlet dashlet metadata
            if(dashletDef.view) {
                toolbar = dashletDef.view['custom_toolbar'] || {};
                dashletMeta = app.metadata.getView(dashletDef.view.module, dashletDef.view.name || dashletDef.view.type);
                dashletModule = dashletDef.view.module ? dashletDef.view.module : null;
            } else if (dashletDef.layout) {
                toolbar = dashletDef.layout.custom_toolbar || {};
                dashletMeta = app.metadata.getLayout(dashletDef.layout.module, dashletDef.layout.name || dashletDef.layout.type);
                dashletModule = dashletDef.layout.module ? dashletDef.layout.module : null;
            }
            if (!dashletMeta) {
                var dashletViews = app.utils.getMfeDashletViews(this.context);
                var customComp = _.find(dashletViews, function(dashlet) {
                    return dashlet.metadata.dashletType === dashletDef.view.type;
                });
                if (customComp) {
                    dashletDef.view.type = 'external-app-dashlet';
                    dashletDef.view.src = customComp.metadata.config.src;
                    dashletDef.view.customConfig = true;
                }
            }
            if (!dashletModule && dashletDef.context && dashletDef.context.module) {
                dashletModule = dashletDef.context.module;
            }
            if (pattern.test(this.meta.label)) {
                label = app.lang.get(label, dashletModule, dashletDef.view || dashletDef.layout);
            }
            //determine whether it contains custom_toolbar or not
            if (_.isEmpty(toolbar) && dashletMeta && dashletMeta['custom_toolbar']) {
                toolbar = dashletMeta['custom_toolbar'];
            }
            if(toolbar !== "no") {
                components.push({
                    view: {
                        name: 'dashlet-toolbar',
                        label: label,
                        toolbar: toolbar
                    },
                    context: {
                        module: 'Home',
                        skipFetch: true
                    }
                });
            } else {
                this.hasToolbar = false;
            }
        }

        this._setDashletContents();

        var context = this.context.parent || this.context;
        this._super('_addComponentsFromDef', [components, context, context.get("module")]);
    },

    /**
     * Set contents of `.dashlet-container` element. If we're initializing an
     * empty dashlet, set the innerHTML of `this.$el`. If we're updating an
     * existing dashlet, update with `replaceWith` to not overwrite neighbors or
     * their event listeners
     * @private
     */
    _setDashletContents: function() {
        var htmlContent = this.meta.empty ? app.template.empty(this) : this.template(this);
        var currentContent = this.$el.children('.dashlet-container').first();
        if (!_.isEmpty(currentContent)) {
            currentContent.replaceWith(htmlContent);
        } else {
            this.$el.html(htmlContent);
        }
    },

    /**
     * @inheritdoc
     * Set default skipFetch as false.
     * Able to get the custom title label from the dashlet component.
     */
    createComponentFromDef: function(def, context, module) {
        //pass the parent context only to the main dashlet component
        if (def.view && !_.isUndefined(def.view.toolbar)) {
            var dashlet = _.first(this._components);
            if (dashlet) {
                if (_.isFunction(dashlet.getLabel)) {
                    def.view.label = dashlet.getLabel();
                }
                context = dashlet.context;
            }
        }
        //set default skipFetch as false
        var skipFetch = def.view ? def.view.skipFetch : def.layout.skipFetch;
        if (def.context && skipFetch !== false) {
            def.context.skipFetch = true;
        }
        return this._super('createComponentFromDef', [def, context, module]);
    },

    /**
     * Set current dashlet as invisible
     */
    setInvisible: function() {
        if (this._invisible === true) {
            return;
        }
        var comp = _.first(this._components);
        this.model.on("setMode", this.setMode, this);
        this._invisible = true;
        this.$el.addClass('hide');
        this.listenTo(comp, "render", this.unsetInvisible, this);
    },

    /**
     * Set current dashlet back as visible
     */
    unsetInvisible: function() {
        if (this._invisible !== true) {
            return;
        }
        var comp = _.first(this._components);
        comp.trigger("show");
        this._invisible = false;
        this.model.off("setMode", null, this);
        this.$el.removeClass('hide');
        this.stopListening(comp, "render");
    },

    /**
     * @inheritdoc
     * Place the each component to the right location
     *
     * @param comp
     * @param def
     */
    _placeComponent: function(comp, def) {
        if(this.meta.empty) {
            //add-a-dashlet component
            this.$el.append(comp.el);
        } else if(this.meta.preview) {
            //preview mode
            this.$el.addClass('dashlet-preview pt-4 px-2');
            this.$('[data-dashlet=dashlet]').append(comp.el);
        } else if (_.isUndefined(def)) {
            this.$('[data-dashlet=dashlet]').after(comp.el);
        } else if(def.view && !_.isUndefined(def.view.toolbar)) {
            //toolbar view
            this.$('[data-dashlet=toolbar]').append(comp.el);
        } else if (def.prepend) {
            this.$('[data-dashlet=dashlet]').before(comp.el);
        } else {
            //main dashlet component
            this.$('[data-dashlet=dashlet]').append(comp.el);
        }
    },

    /**
     * Convert the dashlet setting metadata into the dashboard model data
     *
     * @param {Object} setting metadata
     * @return {Object} component metadata
     */
    setDashletMetadata: function(meta) {
        var metadata = this.model.get("metadata"),
            component = this.getCurrentComponent(metadata, this.index);

        _.each(meta, function(value, key){
            this[key] = value;
        }, component);

        this.model.set("metadata", app.utils.deepCopy(metadata), {silent: true});
        this.model.trigger("change:layout");
        //auto save
        if(this.model.mode === 'view') {
            this.model.save(null, {
                silent: true,
                //Show alerts for this request
                showAlerts: true,
                success: _.bind(function() {
                    this.model.unset('updated');
                }, this)
            });
        }
        return component;
    },

    /**
     * Gets component from metadata.
     *
     * @param {Object} metadata for all dashboard components
     * @return {Object} dashboard component
     */
    getComponentsFromMetadata: function(metadata) {
        var component;
        // this is a tabbed dashboard
        if (metadata.tabs) {
            var tabIndex = this.context.get('activeTab') || 0;
            component = metadata.tabs[tabIndex].components;
        } else {
            component = metadata.components;
        }
        return component;
    },

    /**
     * Retrives the seperate component metadata from the whole dashboard components
     *
     * @param {Object} metadata for all dashboard componenets
     * @param {String} tree based trace key (each digit represents the index number of the each level)
     * @return {Object} component metadata
     */
    getCurrentComponent: function(metadata, tracekey) {
        var position = tracekey.split('');
        var component = this.getComponentsFromMetadata(metadata);

        _.each(position, function(index){
            component = component.rows ? component.rows[index] : component[index];
        }, this);

        return component;
    },

    /**
     * Append the dashlet component from the setting metadata
     *
     * @param {Object} setting metadata
     */
    addDashlet: function(meta) {
        const previouslyHadComponents = this._components.length !== 0;
        var component = this.setDashletMetadata(meta),
            def = component.view || component.layout || component;

        this.meta.empty = false;
        this.meta.label = def.label || def.name || "";
        this.meta.type = def.type;
        this.meta.moduleName = def.module;
        //clear previous dashlet
        _.each(this._components, function(component) {
            component.layout = null;
            component.dispose();
        }, this);
        this._components = [];

        if(component.context) {
            _.extend(component.context, {
                forceNew: true
            })
        }
        this.meta.components = [component];
        this.initComponents(this.meta.components);
        this.model.set('updated', true);
        this.loadData();
        this.render();

        // If user edits a collapsed dashlet
        // reinitialize the layout state to trigger all styles updates
        if (previouslyHadComponents && this.isDashletCollapsed()) {
            this.collapse(false);
            this.collapse(true);
        }
    },

    /**
     * Remove the current attached dashlet component
     */
    removeDashlet: function() {
        var cellLayout = this.layout,
            rowLayout = cellLayout.layout;
        if (this.model.mode === 'view' && cellLayout._components.length === 1) {
            var dashletRow = this.closestComponent('dashlet-row');
            // this.layout needs to have method to return all the components
            dashletRow.removeRow(this.layout.index.split('').pop());
            dashletRow.model.save(null, {showAlerts: true});
            return;
        }
        var metadata = this.model.get("metadata"),
            component = this.getCurrentComponent(metadata, this.index);
        _.each(component, function(value, key){
            if(key!=='width') {
                delete component[key];
            }
        }, this);
        this.model.set("metadata", app.utils.deepCopy(metadata), {silent: true});
        this.model.trigger("change:layout");
        //auto save
        if(this.model.mode === 'view') {
            this.model.save(null, {
                //Show alerts for this request
                showAlerts: true
            });
        } else {
            this.model.set('updated', true);
        }
        this.meta.empty = true;
        //clear previous dashlet
        _.each(this._components, function(component) {
            component.layout = null;
            component.dispose();
        }, this);
        this._components = [];
        this.initComponents([
            {
                view: 'dashlet-cell-empty',
                context: {
                    module: 'Home',
                    skipFetch: true
                }
            }
        ]);
        this.render();
    },

    /**
     * Calls the layout's addRow function to add another row
     *
     * @param {Number} columns the number of columns to add
     */
    addRow: function(columns) {
        this.layout.addRow(columns);
    },

    /**
     * Refresh the dashlet
     *
     * Call dashlet's loadData to refetch the remote data
     *
     * @param {Object} options
     */
    reloadDashlet: function(options) {
        var component = this.reloadableComponent ||  _.first(this._components);
        var context = component.context;
        context.resetLoadFlag();
        component.loadData(options);
    },

    /**
     * Edit current dashlet's settings
     *
     * Convert the current componenet's metadata into setting metadata
     * and then it loads its dashlet's configuration view
     *
     * @param {Window.Event}
     */
    editDashlet: function(evt) {
        var self = this,
            meta = app.utils.deepCopy(_.first(this.meta.components)),
            type = meta.layout ? "layout" : "view";
        if(_.isString(meta[type])) {
            meta[type] = {name:meta[type], config:true};
        } else {
            meta[type].config = true;
        }
        meta[type] = _.extend({}, meta[type], meta.context);

        if(meta.context) {
            meta.context.skipFetch = true;
            delete meta.context.link;
        }

        let targetLayout = (this.context && this.context.parent) ? this.context.parent.get('layout') : null;
        app.controller.context.set('targetLayout', targetLayout);

        app.drawer.open({
            layout: {
                name: 'dashletconfiguration',
                components: [meta]
            },
            context: {
                model: new app.Bean(),
                forceNew: true
            }
        }, function(model) {
            if(!model) return;

            var conf = model.toJSON(),
                dash = {
                    context: {
                        module: model.get("module") || (meta.context ? meta.context.module : null),
                        link: model.get("link") || null
                    }
                };
            delete conf.config;
            if(_.isEmpty(dash.context.module) && _.isEmpty(dash.context.link)) {
                delete dash.context;
            }
            dash[type] = conf;
            self.addDashlet(dash);
        });
    },

    /**
     * Open Report detail view on a new tab
     */
    viewReport: function() {
        var meta = app.utils.deepCopy(_.first(this.meta.components));

        if (meta.view && meta.view.saved_report_id) {
            var link = '#' + app.router.buildRoute('Reports', meta.view.saved_report_id);
            window.open(link, '_blank');
        }
    },

    /**
     * Fold/Unfold the dashlet
     *
     * @param {Boolean} true if it needs to be collapsed
     */
    collapse: function(collapsed) {
        if (this.hasToolbar === false) {
            return;
        }

        // If user attempts to collapse a dashlet that is already collapsed via the "collapse all" fab button,
        // dont execute any logic
        if (!_.isUndefined(this.isCollapsed) && this.isCollapsed === collapsed) {
            return;
        }
        this.isCollapsed = collapsed;

        // Set the proper dashlet styling based on the state (collapsed or expanded)
        this.$('.dashlet-toggle > i').toggleClass('sicon-chevron-down', collapsed);
        this.$('.dashlet-toggle > i').toggleClass('sicon-chevron-up', !collapsed);
        this.$('[data-action=droppable]').toggleClass('collapsed', collapsed);
        this.$('[data-dashlet=dashlet]').toggleClass('invisible', collapsed);
        this.$el.toggleClass('collapsed', collapsed);

        // Notify the parent layout that the dashlet was collapsed or expanded
        this.layout.toggleCollapseDashlet(this, collapsed);
    },

    /**
     * Determines whether the dashlet is currently collapsed or expanded
     *
     * @return {bool} true if the dashlet is collapsed; false otherwise
     */
    isDashletCollapsed() {
        return this.isCollapsed || false;
    },

    /**
     * Displays current invisible dashlet when current mode is on edit/drag
     *
     * @param {String} (edit|drag|view)
     */
    setMode: function(type) {
        if (!this._invisible) {
            return;
        }
        if (type === 'edit' || type === 'drag') {
            this.show();
        } else {
            this.hide();
        }
    },

    /**
     * Sets the Dashlet layout Title
     * @param title
     */
    setTitle: function(title) {
        // make sure we've got an $el before using it
        if (this.$el) {
            var $titleEl = this.$('h4.dashlet-title');
            if($titleEl.length) {
                $titleEl.text(title);
            }
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.model.off("setMode", null, this);
        this.off("render");
        this.context.off("dashboard:collapse:fire", null, this);
        app.events.off('focusdrawer:close', this.handleFocusDrawerClose, this);
        this._super('_dispose');
    }
})
