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
 * @class View.Layouts.Base.TabbedLayoutLayout
 * @alias SUGAR.App.view.layouts.BaseTabbedLayoutLayout
 * @extends View.Layout
 */
({
    maxTabs: undefined,
    $moreTabs: undefined,
    $overflowTabs: undefined,

    /**
     * Current active tab
     */
    activeTab: 0,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.firstIsActive = false;

        if (options.meta) {
            // default to no tabs
            options.meta.notabs = true;
            if (options.meta.components) {
                // update metadata notabs setting before parent view initialize
                options.meta.notabs = options.meta.components.length <= 1;
            }
        }

        this.updateLayoutConfig();

        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.context.on('tabbed-layout:tab:change', this.toggleTabVisibility, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        var isPreview = this.name === 'preview-pane';

        if (isPreview) {
            this.$('li[data-bs-toggle="tab"]').off('shown.bs.tab');
        }

        this._super('render');

        if (isPreview) {
            this.$('li[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var tabName = $(e.target.parentElement).data('tab-name');
                var $navTabs = $(e.target).parents('.nav-tabs');
                var $tabbable = $(e.target).parents('.tabbable');

                $navTabs.toggleClass('preview-pane-tabs', tabName === 'preview');
                $tabbable.toggleClass('preview-active', tabName === 'preview');
            });
        }
    },

    /**
     * Toggles the visibility of multiple tabbed-layout layouts
     *
     * @param {string} tabName The name of the tab being toggled
     * @param {boolean} isVisible True if the tab is visible now or not
     */
    toggleTabVisibility: function(tabName, isVisible) {
        var method = isVisible ? 'hide' : 'show';

        if (this.name.indexOf(tabName) === -1) {
            this[method]();
        }
    },

    /**
     * Extensible function that updates any local config vars that need to be set.
     */
    updateLayoutConfig: function() {
        this.maxTabs = 3;
    },

    /**
     * Triggers that a sugar app needs to be loaded
     * @private
     */
    _triggerSugarAppLoad: function(compDef) {
        this.context.trigger('sugarApp:' + this.cid + ':load:' + compDef.srn);
    },

    /**
     * @inheritdoc
     */
    _placeComponent: function(comp, def) {
        var id = _.uniqueId('record-bottom');
        var compDef = def.layout || def.view || {};
        var lblKey = compDef.label || compDef.name || compDef.type;
        var lblName = compDef.name || compDef.title || compDef;

        if (!lblKey) {
            // handles the 'preview' case returning the label
            // Business Card for translations
            lblKey = 'LBL_RECORD_BODY';
        }

        let label = lblKey;
        if (!_.isUndefined(compDef.context) && compDef.context.loadModuleLabel) {
            let module = compDef.context.listViewModule || compDef.context.module;
            label = app.lang.get(
                lblKey,
                module,
                {module: app.lang.getModuleName(module, {plural: false})}
            ) || lblKey;
        } else {
            label = app.lang.get(lblKey, this.module) || lblKey;
        }
        const anchorTag = document.createElement('a');
        anchorTag.classList = 'font-bold p-0 text-center';
        anchorTag.onclick = 'return false;';
        anchorTag.textContent = label;

        let $nav = $('<li />', {
            'data-bs-toggle': 'tab',
            'data-bs-target': `#${id}`,
        }).append(anchorTag);

        // we have a sugar app and want to lazy load it
        if (compDef.type === 'external-app') {
            $nav = $nav.on('click', this._triggerSugarAppLoad.bind(this, compDef));
        }

        var $content = $('<div/>')
            .addClass('tab-pane')
            .attr('id', id)
            .html(comp.el);

        this.$mainTabs = this.$('.nav');
        var tabIndex = this.$mainTabs.children().length;

        this.$mainTabs.addClass(this.name + '-tabs');
        $nav.addClass('nav-item')
            .attr('data-tab-name', label)
            .attr('data-tab-index', tabIndex);
        $nav.data('tab-name', lblName);

        if ((!this.firstIsActive && !this.activeTab) ||
            (!!this.activeTab && this.activeTab === tabIndex)) {
            $nav.addClass('active');
            $content.addClass('active');

            if (lblName === 'preview') {
                this.$('.tabbable').addClass('preview-active');
            }
        }

        // use existing or get new reference
        this.$moreTabs = this.$moreTabs || this.$('.more-tabs');

        this.firstIsActive = true;
        this.$('.tab-content').append($content);
        // append new nav tab to the tab list
        this.$mainTabs.append($nav);
        this.$mainTabs.append(this.$moreTabs);

        if (this._components.length > this.maxTabs) {
            // get a reference to the bound event for removal
            if (!this.onMoreTabItemClickedHandler) {
                this.onMoreTabItemClickedHandler = _.bind(this.onMoreTabItemClicked, this);
            }
            // more than maxTabs so hide the new tab on the tab list
            $nav.addClass('hidden');

            // use existing or get new reference
            this.$overflowTabs = this.$overflowTabs || this.$('[data-container="overflow"]');
            // show moreTabs
            this.$moreTabs.removeClass('hidden');
            // make sure moreTabs is the last item in the list
            this.$mainTabs.append(this.$moreTabs);
            // append the new tab to the overflow tabs dropdown
            this.$overflowTabs.append($nav);
            // -1 because the $moreTabs button counts as a child
            tabIndex = this.$mainTabs.children().length - 1 + this.$overflowTabs.children().length;
            $nav.removeClass('hidden')
                .attr('data-tab-index', tabIndex)
                .on('click', this.onMoreTabItemClickedHandler);
        }

        var $navItems = this.$mainTabs.find('.nav-item');
        $navItems.removeClass('border-right')
            .last()
            .addClass('border-right');
    },

    /**
     * When a component is removed, the tab layout needs to remove the tabs as well,
     * including possibly setting a new active tab
     *
     * @param {Object} component The component to remove from the layout
     */
    removeComponent: function(component) {
        var i = _.isNumber(component) ? component : this._components.indexOf(component);

        this._super('removeComponent', [i]);

        var $tabNavEl = $(this.$('.nav-tabs li')[i]);
        var $tabContentEl = $(this.$('.tab-pane')[i]);
        var resetActive = $tabNavEl.hasClass('active');

        $tabNavEl.remove();
        $tabContentEl.remove();

        if (resetActive) {
            this.$(this.$('li')[0]).addClass('active');
            this.$(this.$('.tab-pane')[0]).addClass('active');
        }
    },

    /**
     * When there are more than `maxTabs` and one of those overflow tabs gets clicked,
     * this function switches the tabs from the overflow dropdown to the main tab area
     *
     * @param {Event} evt The Click Event
     */
    onMoreTabItemClicked: function(evt) {
        var $overflowTabToMove = $(evt.currentTarget);
        var $lastMainTab = $(this.$mainTabs.children()[2]);

        // add the main tab to the overflow tabs
        this.$overflowTabs.prepend($lastMainTab);
        // remove the active class from the tab moved back to overflow
        $lastMainTab.removeClass('active');
        // add the dropdown tab to the main tabs list
        this.$mainTabs.append($overflowTabToMove);
        // remove the extra click handler from the new overflow tab thats now on the main list
        $overflowTabToMove.off('click', 'click', this.onMoreTabItemClickedHandler);
        // add the new click handler to the main tab moved to overflow tabs
        $lastMainTab.on('click', this.onMoreTabItemClickedHandler);

        // make sure moreTabs is the last item in the list
        this.$mainTabs.append(this.$moreTabs);
        // update the moved tab index to this.maxTabs
        $overflowTabToMove.attr('data-tab-index', this.maxTabs);
        // update the overflow tabs tab-index values
        var $navItems = this.$overflowTabs.find('.nav-item');
        _.each($navItems, function(navItem, index) {
            $(navItem).attr('data-tab-index', this.maxTabs + 1 + index);
        }, this);

    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        if (this.name === 'preview-pane') {
            this.$('li[data-bs-toggle="tab"]').off('shown.bs.tab');
        }

        this._super('dispose');
    }
})
