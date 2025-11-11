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
 * @class View.Fields.Base.Home.DashboardtitleField
 * @alias SUGAR.App.view.fields.BaseHomeDashboardtitleField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click .dropdown-toggle': 'toggleClicked',
        'click a[data-id]': 'navigateClicked',
        'click a[data-action=manager]': 'managerClicked',
        'click a[data-type=dashboardtitle]': 'editClicked',
    },
    dashboards: null,

    hasEditAccess: true,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (!app.acl.hasAccessToModel('edit', this.model, this.name)) {
            this.hasEditAccess = false;
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this.$('input[name=name]').on('keydown', _.bind(function(evt) {
            this.fieldInput(evt);
        }, this));

        this.$('input[name=name]').on('blur', _.bind(function() {
            this.fieldBlur();
        }, this));
    },

    /**
     * Handle the click by dashboard title
     * @param {Object} evt The jQuery Event Object
     */
    editClicked: function(evt) {
        if (this.model.get('is_template')) {
            return;
        }

        // Reinitialize hasChanges function to provide opportunity
        // to collapse the field with changes
        this.hasChanged = function() {
            return false;
        };

        this.view.editClicked(evt);
        this.view.toggleField(this, true);
    },

    /**
     * Handle input in title field
     * @param {Object} evt The jQuery Event Object
     */
    fieldInput: function(evt) {
        this.model.set('name', evt.currentTarget.value, {
            silent: true
        });

        if (!this.model.dataFetched) {
            return;
        }

        if ($.inArray(evt.keyCode, [13, 27]) != -1) {
            this.fieldBlur();
        }
    },

    /**
     * Handle blur of title field
     */
    fieldBlur: function() {
        if (!this.model.dataFetched) {
            return;
        }

        const reloadCollection = () => {
            this.context.off('record:set:state', reloadCollection);
            if (this.context.parent) {
                const collection = this.getCollection();
                collection.fetch();
            }
        };

        this.context.on('record:set:state', reloadCollection);

        this.view.saveHandle();
        this.view.toggleField(this);
    },

    /**
     * Return collection
     * @return {Object} Sugar collection
     */
    getCollection: function() {
        const contextBro = this.context.parent &&
        _.contains(['multi-line', 'focus'], this.context.parent.get('layout')) ?
            this.context.parent : this.context.parent.getChildContext({module: 'Home'});
        return contextBro.get('collection');
    },

    toggleClicked: function(evt) {
        var self = this;
        if (!_.isEmpty(this.dashboards)) {
            if (_.isFunction(self.view.adjustDropdownMenu)) {
                self.view.adjustDropdownMenu();
            }
            return;
        }

        let collection = this.getCollection().clone();
        var pattern = /^(LBL|TPL|NTC|MSG)_(_|[a-zA-Z0-9])*$/;
        collection.remove(self.model, {silent: true});
        _.each(collection.models, function(model) {
            if (pattern.test(model.get('name'))) {
                model.set('name',
                    app.lang.get(model.get('name'), model.get('dashboard_module'))
                );
            }
        });
        self.dashboards = collection;
        var optionTemplate = app.template.getField(self.type, 'options', self.module);
        self.$('.dropdown-menu').html(optionTemplate(collection));
        if (_.isFunction(self.view.adjustDropdownMenu)) {
            self.view.adjustDropdownMenu();
        }
    },

    /**
     * Handle the click from the UI
     * @param {Object} evt The jQuery Event Object
     */
    navigateClicked: function(evt) {
        var id = $(evt.currentTarget).data('id');
        this.navigate(id);
    },
    /**
     * Navigate the user to the manage dashboards view
     */
    managerClicked: function() {
        var ctx = this.context && this.context.parent &&
        (_.contains(['multi-line', 'focus'], this.context.parent.get('layout'))) ?
            this.context.parent : app.controller.context;
        var dashboardModule = ctx.get('module');
        var dashboardLayout = ctx.get('layout');
        app.router.navigate('#Dashboards?moduleName=' + dashboardModule +
            '&viewName=' + dashboardLayout, {trigger: true});
    },
    /**
     * Change the Dashboard
     * @param {string} id The ID of the Dashboard to load
     * @param {string} [type] (Deprecated) The type of dashboard being loaded, default is undefined
     */
    navigate: function(id, type) {
        if (!_.isUndefined(type)) {
            // TODO: Remove the `type` parameter. This is to be done in TY-654
            app.logger.warn('The `type` parameter to `View.Fields.Base.Home.DashboardtitleField`' +
            'has been deprecated since 7.9.0.0. Please update your code to stop using it.');
        }
        this.view.layout.navigateLayout(id);
    },
    /**
     * Inspect the dashlet's label and convert i18n string only if it's concerned
     *
     * @param {String} i18n string or user typed string
     * @return {String} Translated string
     */
    format: function(value) {
        var module = this.context.parent && this.context.parent.get('module') || this.context.get('module');
        return app.lang.get(value, module) || value;
    },

    /**
     * @inheritdoc
     *
     * Override template for dashboard title on home page.
     * Need display it as label so use `f.base.detail` template.
     */
    _loadTemplate: function() {
        app.view.Field.prototype._loadTemplate.call(this);

        if (this.context && this.context.get('model') &&
            this.context.get('model').dashboardModule === 'Home'
        ) {
            var tpl = (this.tplName === 'detail') ? 'dashboardtitle' : this.tplName;
            this.template = app.template.getField('dashboardtitle', tpl, this.module) || this.template;
        }
    },

    /**
     * Called by record view to set max width of inner record-cell div
     * to prevent long names from overflowing the outer record-cell container
     */
    setMaxWidth: function(width) {
        this.$el.css({'max-width': width});
    },

    /**
     * Return the width of padding on inner record-cell
     */
    getCellPadding: function() {
        var padding = 0,
            $cell = this.$('.dropdown-toggle');

        if ($cell.length > 0) {
            padding = parseInt($cell.css('padding-left'), 10) + parseInt($cell.css('padding-right'), 10);
        }

        return padding;
    }
})
