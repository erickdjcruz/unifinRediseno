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
 * @class View.Fields.Base.Reports.OrientationWidgetField
 * @alias SUGAR.App.view.fields.BaseReportsOrientationWidgetField
 * @extends View.Views.Base.Field
 */
({
    events: {
        'click [data-action="change-orientation"]': 'changeOrientation',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit();
        this._super('initialize', [options]);
        this._registerEvents();
    },

    /**
     * Before init properties handling
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this.HORIZONTAL = 'horizontal';
        this.VERTICAL = 'vertical';

        this._orientation = this.HORIZONTAL;

        this.ORIENTATION_DEPENDENCY = {
            horizontal: this.VERTICAL,
            vertical: this.HORIZONTAL,
        };
    },

    /**
     * Register related events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'report-layout-config-retrieved', this.setOrientation);
        this.listenTo(this.context, 'orientation-visibility-change', this.updateVisibilityByWidget);
        this.listenTo(this.context, 'toggle-orientation-buttons', this.updateVisibilityByWidget);
    },

    /**
     * Change buttons visibility
     */
    _updateVisibility: function() {
        this.$(`#${this.HORIZONTAL}`).toggleClass('active', this._orientation === this.HORIZONTAL);
        this.$(`#${this.VERTICAL}`).toggleClass('active', this._orientation === this.VERTICAL);
    },

    /**
     * Update visibility by widget
     *
     * Updates DOM elements triggered by visiblity widget changes
     */
    updateVisibilityByWidget: function(toggle) {
        const resizeConfig = this.context.get('resizeConfig') || {};
        let orientationWidgetActive = false;

        if (resizeConfig.hidden === false) {
            orientationWidgetActive = true;
        }
        if (_.isBoolean(toggle) && resizeConfig.hidden === false) {
            orientationWidgetActive = toggle;
        }

        const horizontalWidgetEl = this.$(`#${this.HORIZONTAL}`);
        const verticalWidgetEl = this.$(`#${this.VERTICAL}`);

        if (orientationWidgetActive) {
            const widgetToActivate = this.$(`#${this._orientation}`);
            widgetToActivate.toggleClass('active', true);

            const widgetToDeactivate = this.$(`#${this.ORIENTATION_DEPENDENCY[this._orientation]}`);
            widgetToDeactivate.toggleClass('active', false);
        } else {
            horizontalWidgetEl.toggleClass('active', false);
            verticalWidgetEl.toggleClass('active', false);
        }

        horizontalWidgetEl.toggleClass('disabled', !orientationWidgetActive);
        horizontalWidgetEl.prop('disabled', !orientationWidgetActive);
        verticalWidgetEl.toggleClass('disabled', !orientationWidgetActive);
        verticalWidgetEl.prop('disabled', !orientationWidgetActive);
    },

    /**
     * Change widget buttons visibility
     *
     * @param {jQuery} e
     */
    changeOrientation: function(e) {
        if (e.currentTarget.id === this._orientation) {
            return;
        }

        this._orientation = e.currentTarget.id;

        const resizeConfig = this.context.get('resizeConfig');
        let filtersActive = !!this.context.get('filtersActive');
        if (resizeConfig && resizeConfig.filtersActive === false) {
            filtersActive = false;
        }
        const config = {
            direction: this._orientation,
            hidden: false,
            firstScreenRatio: '50%',
            filtersActive
        };

        this._updateVisibility();

        this.context.trigger('split-screens-config-change', config, true);
        this.context.trigger('split-screens-orientation-change', config);
        this.context.trigger('split-screens-resized', config);
        this.context.trigger('container-resizing');
    },

    /**
     * Set the visibility state of buttons
     *
     * @param {Object} config
     */
    setOrientation: function(config) {
        this._orientation = config.direction || this.HORIZONTAL;

        this.updateVisibilityByWidget();
    },
})
