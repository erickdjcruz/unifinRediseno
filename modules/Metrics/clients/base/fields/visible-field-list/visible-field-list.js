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
 * @class View.Fields.Base.Metrics.VisibleFieldListField
 * @alias SUGAR.App.view.fields.BaseVisibleFieldListField
 * @extends View.Fields.Base.BaseField
 */
({
    removeFldIcon: '<i class="sicon sicon-remove console-field-remove"></i>',

    events: {
        'click .sicon.sicon-remove.console-field-remove': 'removePill',
    },

    /**
     * Fields mapped to their subfields.
     */
    visibleFields: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.previewEvent = 'consoleconfig:preview:' + this.model.get('enabled_module');
    },

    /**
     * @inheritdoc
     *
     * Overrides the parent bindDataChange to make sure this field is re-rendered
     * when the config is reset.
     */
    bindDataChange: function() {
        if (this.model) {
            this.context.on('consoleconfig:reset:defaultmetarelay', function() {
                var defaultViewMeta = this.context.get('defaultViewMeta');
                var moduleName = this.model.get('enabled_module');
                if (defaultViewMeta && defaultViewMeta[moduleName]) {
                    this.context.set('defaultViewMeta', null);
                    this.render();
                }
            }, this);
        }
    },

    /**
     * Removes a pill from the selected fields list.
     *
     * @param {e} event Remove icon click event.
     */
    removePill: function(event) {
        var pill = event.target.parentElement;

        event.target.remove();
        pill.setAttribute('class', 'pill outer');
        this.getAvailableSortable().append(pill);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        let url = app.api.buildURL('Metrics', 'visible', null, {
            metric_context: this.context.get('metric_context') || 'service_console',
            metric_module: this.context.get('metric_module') || 'Cases'
        });
        app.api.call('GET', url, null, {
            success: _.bind(function(results) {
                this.visibleFields = [];
                if (!_.isEmpty(results)) {
                    _.each(results, function(field) {
                        this.visibleFields.push({
                            'name': field.id,
                            'displayName': field.name
                        });
                    }, this);
                }
                this.renderAfterFetch();
            }, this),
        });
    },

    /**
     * Render the field and initializes the drag and drop on the pills once the visible metrics are fetched
     */
    renderAfterFetch: function() {
        this._super('_render');
        this.initDragAndDrop();
    },

    /**
     * Initialize drag & drop for the selected field (main) list.
     */
    initDragAndDrop: function() {
        this.$('#columns-sortable').sortable({
            items: '.outer.pill',
            connectWith: '.connectedSortable',
            receive: _.bind(this.handleDrop, this),
        });
    },

    /**
     * Event handler for the drag & drop. The event is fired when an item is dropped to a list.
     *
     * @param {e} event jQuery sortable event handler.
     * @param {Object} ui jQuery UI's helper object for drag & drop operations.
     */
    handleDrop: function(event, ui) {
        if ('fields-sortable' === ui.sender.attr('id')) {
            ui.item.append(this.removeFldIcon);
        }
    },

    /**
     * Return the proper view metadata. If there is a default metadata we restore it,
     * otherwise we return the view metadata.
     *
     * @param {string} moduleName The selected module name from the available modules.
     * @return {Object} The default view meta or the multi line list metadata.
     */
    getViewMetaData: function(moduleName) {
        var defaultViewMeta = this.context.get('defaultViewMeta');
        return defaultViewMeta && defaultViewMeta[moduleName] ? defaultViewMeta[moduleName] :
            app.metadata.getView(moduleName, 'multi-line-list');
    },

    /**
     * Will cache and return the sortable list with the available fields.
     *
     * @return {jQuery} The available fields sortable lost node.
     */
    getAvailableSortable: function() {
        return this.availableSortable || (this.availableSortable = $('#fields-sortable'));
    },

})
