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
 * @class App.view.views.Base.DriWorkflowsHeaderView
 * @alias SUGAR.App.view.views.BaseDriWorkflowsHeaderView
 * @extends App.view.views.BaseView
 */
({
    /**
     * Use ToggleMoreLess plugin
     */
    plugins: ['ToggleMoreLess'],

    /**
     * Events information
     *
     * @type {Object}
     */
    events: {
        'click [data-moreless]': 'moreLessClicked',
    },

    /**
     * Class name for this view
     *
     * @property
     */
    className: 'dri-workflows-header-wrapper -mb-1 relative',

    /**
     * Status values.
     *
     * @property
     */
    MORE_LESS_STATUS: {
        MORE: 'more',
        LESS: 'less',
    },

    /**
     * To hide the action buttons for header according to showHeaderRow value
     *
     * @property
     */
    access: true,

    /**
     * Contains grace period message
     *
     * @property {string} gracePeriodMessage
     */
    gracePeriodMessage: '',

    /**
     * @inheritdoc
     *
     * @param {Object} options Options to be passed to base view
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        let moreLess = app.user.lastState.get(app.user.lastState.key(this.MORE_LESS_KEY, this));

        this.context.set('moreLess', moreLess);

        this.listenTo(this.context, 'change:moreLess', this.render);
        this.listenTo(this.collection, 'add remove sync', this.render);
        this.listenTo(this, 'more-less:toggled', this._toggleMoreLess);

        this.editModel = app.data.createBean(this.context.get('parentModel').module);

        let acls = this.editModel.get('_acl') || {fields: {}};
        acls.create = 'yes';
        acls.edit = 'yes';
        acls.fields.dri_workflow_template_id = {
            write: 'yes',
            create: 'yes',
        };

        this.editModel.set('_acl', acls);

        _.each(this.meta.fields,
            function(def) {
                _.extend(def, this.editModel.fields[def.name]);
            },this
        );

        if (!app.user.hasAutomateLicense()) {
            this.access = false;

            if (!this.disposed) {
                this.render();
            }
        }

        // To hide the action buttons for header according to showHeaderRow value
        if (this.layout && !this.layout.showHeaderRow) {
            this.access = false;
        }

        this.displaySetting = app.CJBaseHelper.getCJRecordViewSettings(this.context.get('parentModule'));
        this.useTabs = app.metadata.getView(this.context.get('parentModule'), 'record');
        this.smartGuidesCount = 0;
        this.smartGuidesFlag = false;
        this.loadRemoval = true;

        let url = app.api.buildURL('DRI_Workflows', 'graceperiod-remaining-days');

        app.api.call('read', url, null, {
            success: _.bind(this.checkUserLimitSuccess, this),
        });
    },

    /**
     * Set grace period message on successful check-user-limit api call
     *
     * @param {Object} response
     */
    checkUserLimitSuccess: function(response) {
        if (!_.isUndefined(response.remaining_days)) {
            this.gracePeriodMessage = app.lang.get(
                'LBL_CJ_REMAINING_DAYS_WHEN_USER_LIMIT_REACHED',
                'DRI_Workflows',
                {gracePeriodDays: response.remaining_days}
            );
            this._render();
        }
    },

    /**
     * Toggle the more and less view
     *
     * @param {string} moreLess
     * @private
     */
    _toggleMoreLess: function(moreLess) {
        this.context.set('moreLess', moreLess);
    },

    /**
     * Initializes the jQuery Sortable plugin to this layout
     *
     * @return {Object}
     */
    _initSortablePlugin: function() {
        this.$el.parent().sortable({
            axis: 'y',
            items: '.dri-workflow-wrapper',
            containment: this.$el.parent(),
            handle: '[data-sortable-journey=true]',
            tolerance: 'pointer',
            scrollSensitivity: 50,
            scrollSpeed: 15,
            update: _.bind(this.handleSort, this)
        });

        return this;
    },

    /**
     * Handler for subpanel re-order.
     *
     * @param {Event} evt The jQuery update event.
     * @param {Object} ui The jQuery UI object.
     */
    handleSort: function(evt, ui) {
        let newOrder = this.$el.parent().sortable('toArray', {
            attribute: 'data-id'
        });
        let moduleName = this.context.parent.get('module');
        let mode = app.CJBaseHelper.getValueFromCache('toggleActiveArchived',
            'cj_active_or_archive_filter',
            moduleName,
            'dri-workflows-widget-configuration'
        );
        let key = app.user.lastState.buildKey(`${mode}-journeys-order`, this.context.parent.get('modelId'),
            moduleName
        );

        // set user preference
        app.user.lastState.set(key, newOrder);

        app.alert.show('journeys_order_updated', {
            level: 'success',
            messages: app.lang.get('LBL_SAVED_LAYOUT', this.module),
            autoClose: true
        });
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this._initSortablePlugin();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    },
});
