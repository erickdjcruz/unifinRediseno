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
 * @class View.Views.Base.OmnichannelSearchModulelistView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchModulelistView
 * @extends View.Views.Base.QuicksearchModuleListView
 */
({
    extendsFrom: 'QuicksearchModuleListView',
    className: 'omnichannel-search-modulelist-wrapper cursor-pointer pl-2.5 relative table-cell w-[30px] z-[1030]',

    events: {
        'click [data-action=select-all]': 'selectAllModules',
        'click [data-action=select-module]': 'selectModule',
        'click [data-bs-toggle=dropdown]': 'moduleDropdownClick'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.collection = this.layout.collection || app.data.createMixedBeanCollection();

        /**
         * A collection of the available modules.
         *
         * @type {Backbone.Collection}
         */
        this.searchModuleFilter = new Backbone.Collection(null, {
            //adds models in alphabetical order of model's id's module name translation
            comparator: function(model) {
                return app.lang.getModuleName(model.id, {plural: true});
            }
        });

        /**
         * The lastState key for local storage.
         *
         */
        this.stateKey = app.user.lastState.buildKey('omnisearch', 'modulelist', this.name);

        /**
         * Template for the module icons in the search bar.
         * @type {Handlebars.Template}
         *
         * @private
         */
        this._moduleIconTemplate = app.template.getView(this.name + '.module-avatar');

        app.events.on('omnichannel:modulelist:open', function() {
            this.populateModules();
            // If there is a module preference stored in local storage,
            // default selection to those modules.
            var previousModuleSelection = this.collection.selectedModules ||
                app.user.lastState.get(this.stateKey);
            if (_.isEmpty(previousModuleSelection) || _.isUndefined(previousModuleSelection[0])) {
                this.searchModuleFilter.allSelected = true;
            } else {
                _.each(previousModuleSelection, function(module) {
                    this.searchModuleFilter.get(module).set('selected', true);
                }, this);
            }
            this._setSelectedModules();
            // Prepare the module icons for display
            var moduleIconObj = this._buildModuleIconList();
            this.moduleIcons = {icon: moduleIconObj};
            this.render();
        }, this);
    },

    /**
     * @override
     */
    bindDataChange: function() {
        // If the layout has `omnichannel:modulelist:close` or `omnichannel:results:close` called on it,
        // we need to hide modulelist
        this.layout.on('quicksearch:close omnichannel:modulelist:close omnichannel:results:close', this.close);
    },

    /**
     * Populate `this.searchModuleFilter` with the searchable modules, using
     * acls and the metadata attribute `checkGlobalSearchEnabled`.
     */
    populateModules: function() {
        if (this.disposed) {
            return;
        }

        this.searchModuleFilter.reset();
        var modules = app.metadata.getModules() || {};

        var metaTabs = this._getMetaTabs();
        if (metaTabs) {
            // filter the module names out based on omnichannel modules,
            // global search enabled, has access to acl, and is not a blacklisted module
            _.each(modules, function(meta, module) {
                var validModule = _.find(metaTabs, function(tab) {
                    if (tab.icon && tab.icon.module) {
                        return tab.icon.module === module;
                    }
                    return false;
                });
                if (meta.globalSearchEnabled &&
                    app.acl.hasAccess.call(app.acl, 'view', module) &&
                    !_.contains(this.blacklistModules, module) &&
                    validModule
                ) {
                    var moduleModel = new Backbone.Model({id: module, selected: false});
                    this.searchModuleFilter.add(moduleModel);
                }
            }, this);
        }
    },

    /**
     * Gets metadata tabs.
     */
    _getMetaTabs: function() {
        var metadata = this.model.get('metadata');
        return metadata.tabs || {};
    },

    /**
     * Handle module 'select/unselect' event.
     *
     * @param {Event} event
     */
    selectModule: function(event) {
        // We need to stop propagation for two reasons:
        // 1) Stop scrolling when using the spacebar.
        // 2) Prevent collapse of the `omnichannel-search` layout. The module list is
        // considered inside the dropdown plugin, and not in the layout. Clicks
        // outside the layout normally collapse the layout.
        event.stopImmediatePropagation();
        var $li = $(event.currentTarget);
        var module = $li.data('module');
        var moduleModel = this.searchModuleFilter.get(module);

        // If all the modules were selected, we unselect all of them first.
        if (this.searchModuleFilter.allSelected) {
            this.$('[data-action=select-all]').removeClass('selected', false);
            this.searchModuleFilter.allSelected = false;
        }

        // Then we select the clicked module.
        var checkModule = !moduleModel.get('selected');
        moduleModel.set('selected', checkModule);
        $li.toggleClass('selected', checkModule);

        // Check to see if all the modules are now all selected or unselected.
        var selectedLength = this.searchModuleFilter.where({'selected': true}).length;

        // All modules are selected, set them all to unselected.
        if (selectedLength === this.searchModuleFilter.length) {
            this.searchModuleFilter.invoke('set', {selected: false});
            selectedLength = 0;
        }

        // If all modules are now unselected, update checkboxes and set the
        // `allSelected` property of the filter.
        if (selectedLength === 0) {
            this.searchModuleFilter.allSelected = true;
            this.$('[data-action=select-all]').addClass('selected');
            this.$('[data-action=select-module]').removeClass('selected');
        }

        this._setSelectedModules();
        this._updateModuleIcons();

        // Trigger full search
        this.layout.trigger('omnichannelsearch:quicksearch:viewallresults');
    },

    /**
     * Handle clicks on the "Search all" list item.
     *
     * @param {event} event
     */
    selectAllModules: function(event) {
        // We need to stop propagation for two reasons:
        // 1) Stop scrolling when using the spacebar.
        // 2) Prevent collapse of the `omnichannel-search` layout. The module list is
        // considered inside the dropdown plugin, and not in the layout. Clicks
        // outside the layout normally collapse the layout.
        event.stopImmediatePropagation();

        // Selects all modules.
        this.$('[data-action=select-all]').addClass('selected');
        this.$('[data-action=select-module]').removeClass('selected');
        this.searchModuleFilter.invoke('set', {selected: false});
        this.searchModuleFilter.allSelected = true;

        this._setSelectedModules();
        this._updateModuleIcons();

        // Trigger full search
        this.layout.trigger('omnichannelsearch:quicksearch:viewallresults');
    },

    /**
     * Open it when the module dropdown is clicked.
     */
    moduleDropdownClick: function() {
        this.open();
    },

    /**
     * Show the modulelist dropdown
     */
    open: function() {
        this.$('.dropdown-menu').show();
    },

    /**
     * Hide the modulelist dropdown
     */
    close: function() {
        this.$('.dropdown-menu').hide();
    },

    /**
     * Updates the modules icons in the search bar, based on the currently
     * selected modules.
     *
     * @private
     */
    _updateModuleIcons: function() {
        // Update the module icons in the search bar.
        var $moduleIconContainer = this.$('[data-label=module-icons]');
        $moduleIconContainer.empty();
        var moduleIconObj = this._buildModuleIconList();

        $moduleIconContainer.append(this._moduleIconTemplate({icon: moduleIconObj}));
    },

    /**
     * Builds an array of objects for displaying the module icons.
     * @return {Array}
     *
     * @private
     */
    _buildModuleIconList: function() {
        var moduleIconObj = [];
        // If all modules are selected, display "all" icon.
        if (this.collection.selectedModules.length === 0) {
            moduleIconObj.push({});
            // If 3 or fewer selected, display the module icons that are selected.
        } else if (this.collection.selectedModules.length <= 3) {
            _.each(this.collection.selectedModules, function(module) {
                moduleIconObj.push({module: module});
            }, this);
            // If there are more than 3 modules selected, display the
            // "Multiple Modules" icon
        } else {
            moduleIconObj.push({
                multipleLabel: app.lang.get('LBL_COUNT_MODULES', null, {count: this.collection.selectedModules.length}),
            });
        }
        return moduleIconObj;
    },

    /**
     * Store the selected modules on the collection and in local storage.
     *
     * @private
     */
    _setSelectedModules: function() {
        var selectedModules = [];
        if (!this.searchModuleFilter.allSelected) {
            this.searchModuleFilter.each(function(model) {
                if (model.get('selected')) {
                    selectedModules.push(model.id);
                }
            });
        }

        this.collection.selectedModules = selectedModules;
        app.user.lastState.set(this.stateKey, this.collection.selectedModules);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.layout.off('quicksearch:close omnichannel:modulelist:close omnichannel:results:close', this.close);
        this._super('_dispose');
    }
})
