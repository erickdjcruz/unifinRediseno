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
 * @class View.Fields.Base.SugarLive.AvailableFieldListField
 * @alias SUGAR.App.view.fields.BaseSugarLiveAvailableFieldListField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * Fields with these names should not be displayed in fields list.
     */
    ignoredNames: ['deleted', 'mkto_id', 'googleplus', 'team_name', 'auto_invite_parent', 'contact_name', 'invitees'],

    /**
     * Fields with these types should not be displayed in fields list.
     */
    ignoredTypes: ['id', 'link', 'tag', 'parent', 'parent_type'],

    /**
     * List of fields that are displayed for a given module.
     */
    availableFields: [],

    /**
     * @inheritdoc
     *
     * Collects all supported fields for all available modules and sets the module specific fields to be displayed.
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setAvailableFields();
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
        var defaultFields = app.metadata.getView('', 'omnichannel-detail');
        this.setAvailableFields(defaultFields);
        this.render();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.handleDragAndDrop();
    },

    /**
     * Gets the list of fields that might be available for the given module.
     *
     * @param {string} moduleName The selected module name from the available modules.
     * @return {Array} A list of field defintions that can be rendered.
     */
    getAllFields: function(moduleName) {
        var allFields = [];
        var metaFields = app.metadata.getModule(moduleName, 'fields');

        _.each(metaFields, function(field) {
            if (this.isFieldSupported(field)) {
                allFields.push({
                    'name': field.name,
                    'label': (field.label || field.vname),
                    'displayName': app.lang.get(field.label || field.vname, moduleName)
                });
            }
        }, this);

        // Sort available fields alphabetically
        return _.sortBy(allFields, 'displayName');
    },

    /**
     * Sets the fields that are available for selection.
     *
     * @param {Object} meta The metadata for the given module.
     */
    setAvailableFields: function(meta) {
        var moduleName = this.model.fieldModule;
        var allFields = this.getAllFields(moduleName);
        meta = meta || app.metadata.getView(moduleName, 'omnichannel-detail') || {};
        var selectedFields = _.map(meta.fields, function(field) {
            return field.name;
        });
        this.availableFields = _.filter(allFields, function(field) {
            return !_.contains(selectedFields, field.name);
        });
    },

    /**
     * Restricts specific fields to be shown in available fields list.
     *
     * @param {Object} field Field to be verified.
     * @return {boolean} True if field is supported, false otherwise.
     */
    isFieldSupported: function(field) {
        // Specified fields names should be ignored.
        if (!field.name || _.contains(this.ignoredNames, field.name)) {
            return false;
        }

        // Specified field types should be ignored.
        if (_.contains(this.ignoredTypes, field.type) || field.dbType === 'id') {
            return false;
        }

        return !this.hasNoStudioSupport(field);
    },

    /**
     * Verify if fields do not have available studio support.
     * Studio fields have multiple value types (array, bool, string, undefined).
     *
     * @param {Object} field Field selected to get verified.
     * @return {boolean} True if there is no support, false otherwise.
     */
    hasNoStudioSupport: function(field) {
        // if it's a special field, do not check studio attribute
        if (!_.isUndefined(field.type) && field.type === 'widget') {
            return false;
        }

        var studio = field.studio;
        if (!_.isUndefined(studio)) {
            if (studio === 'false' || studio === false) {
                return true;
            }
        }

        return false;
    },

    /**
     * Handles the dragging of the items from available fields list
     * to the columns list section, but not the other way around.
     */
    handleDragAndDrop: function() {
        this.$('#fields-sortable').sortable({
            connectWith: '.connectedSortable',
            receive: _.bind(this.cancelDrop, this)
        });
    },

    /**
     * Cancel the drag and drop.
     *
     * @param {Object} event Drop event object.
     * @param {Object} ui Ui tracker object from jquery.
     */
    cancelDrop: function(event, ui) {
        ui.sender.sortable('cancel');
    },

    /**
     * @inheritdoc
     * Remove the preview listener.
     */
    _dispose: function() {
        var module = this.model.fieldModule;
        this.context.off('sugarlive:resetpreview:' + module, this.resetListener);
        this._super('_dispose');
    }
});
