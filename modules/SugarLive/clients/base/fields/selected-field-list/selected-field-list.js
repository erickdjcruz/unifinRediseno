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
 * @class View.Fields.Base.SugarLive.SelectedFieldListField
 * @alias SUGAR.App.view.fields.BaseSugarLiveSelectedFieldListField
 * @extends View.Fields.Base.BaseField
 */
({
    removeFldIcon: '<i class="sicon sicon-remove console-field-remove"></i>',

    events: {
        'click .sicon.sicon-remove.console-field-remove': 'removePill'
    },

    /**
     The list of the fields selected.
     */
    selectedFields: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setSelectedFields();
    },

    /**
     * @inheritdoc
     *
     * Overrides the parent bindDataChange to make sure this field is re-rendered when the config is reset.
     */
    bindDataChange: function() {
        var module = this.model.fieldModule;
        this.resetListener = _.bind(this.resetToDefaults, this);
        this.context.on('sugarlive:resetpreview:' + module, this.resetListener);
    },

    /**
     * Will reset the available fields to the default value.
     */
    resetToDefaults: function() {
        var module = this.model.fieldModule;
        var defaults = app.metadata.getView('', 'omnichannel-detail');
        this.setSelectedFields(defaults);
        this.render();
    },

    /**
     * From the list of all fields for a module find the fields that must appear selected.
     *
     * @param {Object} meta The metadata for the given module.
     */
    setSelectedFields: function(meta) {
        this.selectedFields = [];

        var moduleName = this.model.fieldModule;
        var allFields = app.metadata.getModule(moduleName, 'fields');
        meta = meta || app.metadata.getView(moduleName, 'omnichannel-detail');
        var fieldsToSelect = _.map(meta.fields, function(field) {
            return field.name;
        });

        _.each(fieldsToSelect, function(fieldName) {
            this.selectedFields.push({
                'name': fieldName,
                'label': (allFields[fieldName].label || allFields[fieldName].vname),
                'displayName': app.lang.get(allFields[fieldName].label || allFields[fieldName].vname, moduleName)
            });
        }, this);
    },

    /**
     * Removes a pill from the selected fields list.
     *
     * @param {e} event Remove icon click event.
     */
    removePill: function(event) {
        var pill = event.target.parentElement;

        event.target.remove();
        $(pill).addClass('pill outer');
        this.getAvailableSortable().append(pill);
        this.handleColumnsChanging();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.initDragAndDrop();
        this.collection.trigger('preview');
    },

    /**
     * Initialize drag & drop for the selected field (main) list.
     */
    initDragAndDrop: function() {
        var sortableEl = this.$('#columns-sortable');
        sortableEl.sortable({
            cursor: 'move',
            items: '.outer.pill',
            containment: 'parent',
            connectWith: '.connectedSortable',
            receive: _.bind(this.handleDrop, this),
            update: _.bind(this.handleColumnsChanging, this)
        });
    },

    /**
     * Event handler for the field drag & drop. The event is fired when an item is dropped to a list.
     * When moving a field from the right to the left we add the remove icon.
     *
     * @param {e} event jQuery sortable event handler.
     * @param {Object} ui jQuery UI's helper object for drag & drop operations.
     */
    handleDrop: function(event, ui) {
        if ('fields-sortable' == ui.sender.attr('id')) {
            ui.item.append(this.removeFldIcon);
        }
    },

    /**
     * Trigger a preview event.
     */
    handleColumnsChanging: function() {
        this.collection.trigger('preview');
    },

    /**
     * Will cache and return the sortable list with the available fields.
     *
     * @return {jQuery} The available fields sortable lost node.
     */
    getAvailableSortable: function() {
        var parentSelector = '#' + this.model.fieldModule + '-side';
        return this.availableSortable || (this.availableSortable = $(parentSelector).find('#fields-sortable'));
    },

    /**
     * @inheritdoc
     * Remove the preview event listener.
     */
    _dispose: function() {
        var module = this.model.fieldModule;
        this.context.off('sugarlive:resetpreview:' + module, this.resetListener);
        this._super('_dispose');
    }
});
