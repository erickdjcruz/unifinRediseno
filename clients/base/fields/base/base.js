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
 * This is the base field and all of other fields extend from it.
 *
 * @class View.Fields.Base.BaseField
 * @alias SUGAR.App.view.fields.BaseBaseField
 * @extends View.Field
 */
({
    plugins: ['MetadataEventDriven'],

    /**
     * Should we initialize the default field value?
     */
    shouldInitDefaultValue: false,

    /**
     * @inheritdoc
     *
     * Some plugins use events which prevents {@link View.Field#delegateEvents}
     * to fallback to metadata defined events.
     * This will make sure we merge metadata events with the ones provided by
     * the plugins.
     *
     * The Base Field will always clear any tooltips after `render`.
     */
    initialize: function(options) {

        this.events = _.extend({}, this.events, options.def.events);

        this._super('initialize', arguments);

        /**
         * Property to add or not the `ellipsis_inline` class when rendering the
         * field in the `list` template. `true` to add the class, `false`
         * otherwise.
         *
         * Defaults to `true`.
         *
         * @property {boolean}
         */
        this.ellipsis = _.isUndefined(this.def.ellipsis) || this.def.ellipsis;

        if (app.tooltip) {
            this.on('render', app.tooltip.clear);
        }

        if (this.shouldInitDefaultValue) {
            this._initDefaultValue();
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        var action = 'view';
        if (this.def.link && this.def.route) {
            action = this.def.route.action;
        }
        if (this.def.link && app.acl.hasAccessToModel(action, this.model)) {
            this.module = this.model.module || this.context.get('module');
            this.modelId = this.model.get('id');
            this.linkTarget = 'focus';
            this.href = this.buildHref();
        }
        app.view.Field.prototype._render.call(this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        this.addElListeners();
    },

    /**
     * Trigger click by edit block if user clicked on field
     */
    addElListeners: function() {
        if (!this.$el) {
            return;
        }

        this.$el.on('click', (e) => {
            const isField = (target) => {
                return $(target).hasClass('fieldset-inline') ||
                    $(target).hasClass('ellipsis_inline') ||
                    $(target).closest('.record-label').length;
            };

            if (isField(e.target) || isField(e.currentTarget)) {
                $(e.target).closest('.record-cell:not(.edit)')
                    .find('.record-edit-link-wrapper:not(.hide) > a').click();
            }
        });
    },

    /**
     * Takes care of building href for when there's a def.link and also if is
     * bwc enabled.
     *
     * Deprecated functionality:
     * If `this.def.bwcLink` is set to `true` on metadata, we force the href
     * to be in BWC.
     *
     * TODO remove this from the base field
     */
    buildHref: function() {
        var defRoute = this.def.route ? this.def.route : {},
            module = this.model.module || this.context.get('module');
        // This is a workaround until bug 61478 is resolved to keep parity with 6.7
        if (module === 'Users' && this.context.get('module') !== 'Users') {
            module = 'Employees';
        }
        // FIXME remove this.def.bwcLink functionality (not yet removed due to Portal need for Documents)
        return '#' + app.router.buildRoute(module, this.model.get('id'), defRoute.action, this.def.bwcLink);
    },

    /**
     * @inheritdoc
     *
     * Trim whitespace from value if it is a String.
     */
    unformat: function(value) {
        return _.isString(value) ? value.trim() : value;
    },

    /**
     * Used by the FocusDrawer plugin to get the name of the record this
     * field links to
     * @param {Object} $el
     * @return {string} the name of the related record
     */
    getFocusContextTitle: function($el) {
        return $el.data('bs-original-title') ||
            $el.data('originalTitle') ||
            (this.model && this.model.get('name') ? this.model.get('name') : '');
    },

    /**
     * Set the default field value from metadata for a new model
     *  [
     *      ...
     *      'default' => '...',
     *      ...
     *  ]
     *
     * This function makes 2 assumptions:
     *      1. the default key is named 'default'
     *      2. the default value requires no parsing
     *
     * @private
     */
    _initDefaultValue: function() {
        if (!this.model.isNew() || this.model.get(this.name) || !this.def.default) {
            return;
        }

        if (_.isFunction(this.model.setDefault)) {
            this.model.setDefault(this.name, this.def.default);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.$el.off('click');
        this._super('_dispose');
    },
})
