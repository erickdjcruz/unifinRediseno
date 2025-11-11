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
 * @class View.Views.Base.ExtendedFabView
 * @alias SUGAR.App.view.views.BaseExtendedFabView
 * @extends View.View
 */
({
    className: 'extended-fab',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setOptions(options);
    },

    /**
     * Set options.
     * @param {Object} options
     */
    setOptions: function(options) {
        options = options || {};
        this.icon = options.icon || '';
        this.label = app.lang.get(options.label || '');
        if (this.style) {
            // remove existing style
            this.$el.removeClass(this.style);
        }
        this.style = options.style || '';
        if (this.style) {
            this.$el.addClass(this.style);
        }
        if (this.action && this.events) {
            this.events = _.omit(this.events, 'click [data-action=' + this.action + ']');
        }
        this.action = options.action || '';
        if (this.action) {
            this.events = _.extend({}, this.events);
            this.events['click [data-action=' + this.action + ']'] =  _.bind(function() {
                this.context.trigger(this.action + ':clicked');
            }, this);
        }
    }
})
