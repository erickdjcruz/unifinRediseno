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
 * @class View.Fields.Base.ConsoleConfiguration.AvailableFieldListField
 * @alias SUGAR.App.view.fields.BaseConsoleConfigurationAvailableFieldListField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * Fields with these names should not be displayed in fields list.
     */
    ignoredNames: ['deleted', 'mkto_id', 'googleplus', 'team_name'],

    /**
     * Fields with these types should not be displayed in fields list.
     */
    ignoredTypes: ['id', 'link', 'tag'],

    /**
     * Here are stored all available fields for all available tabs.
     */
    availableFieldLists: [],

    /**
     * List of fields that are displayed for a given module.
     */
    currentAvailableFields: [],

    /**
     * @inheritdoc
     *
     * Collects all supported fields for all available modules and sets the module specific fields to be displayed.
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        var moduleName = this.model.get('enabled_module');

        this.setAvailableFields(moduleName);
        this.currentAvailableFields = this.availableFieldLists;
    },

    /**
     * @inheritdoc
     *
     * Overrides the parent bindDataChange to make sure this field is re-rendered
     * when the config is reset
     */
    bindDataChange: function() {
        if (this.model) {
            this.context.on('consoleconfig:reset:defaultmetaready', function() {
                // the default meta data is ready, use it to re-render
                var defaultViewMeta = this.context.get('defaultViewMeta');
                var moduleName = this.model.get('enabled_module');
                if (!_.isEmpty(defaultViewMeta) && !_.isEmpty(defaultViewMeta[moduleName])) {
                    this.setAvailableFields(moduleName);
                    this.currentAvailableFields = this.availableFieldLists;
                    this.render();
                    this.context.trigger('consoleconfig:reset:defaultmetarelay');
                }
            }, this);
        }
    },

    /**
     * Return the proper view metadata.
     *
     * @param {string} moduleName The selected module name from the available modules.
     */
    getViewMetaData: function(moduleName) {
        // If defaultViewMeta exists, it means we are restoring the default settings.
        var defaultViewMeta = this.context.get('defaultViewMeta');
        if (!_.isEmpty(defaultViewMeta) && !_.isEmpty(defaultViewMeta[moduleName])) {
            return this.context.get('defaultViewMeta')[moduleName];
        }

        // Not restoring defaults, use the regular view meta data
        return app.metadata.getView(moduleName, 'multi-line-list');
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.handleDragAndDrop();
    },

    /**
     * Sets the available fields for the requested module.
     *
     * @param {string} moduleName The selected module name from the available modules.
     */
    setAvailableFields: function(moduleName) {
        var allFields = app.metadata.getModule(moduleName, 'fields');
        var multiLineList = this.getViewMetaData(moduleName);
        var multiLineFields = this.getSelectedFields(_.first(multiLineList.panels).fields);
        this.availableFieldLists = [];

        _.each(allFields, function(field) {
            if (this.isFieldSupported(field, multiLineFields)) {
                this.availableFieldLists.push({
                    'name': field.name,
                    'label': (field.label || field.vname),
                    'displayName': app.lang.get(field.label || field.vname, moduleName)
                });
            }
        }, this);

        // Sort available fields alphabetically
        this.availableFieldLists = _.sortBy(this.availableFieldLists, 'displayName');
    },

    /**
     * Parse metadata and return array of fields that are already defined in the metadata.
     *
     * @param {Array} multiLineFields List of fields that appear on the multi-line list view.
     * @return {Array} True if the field is already in, false otherwise.
     */
    getSelectedFields: function(multiLineFields) {
        var fields = [];
        _.each(multiLineFields, function(column) {
            _.each(column.subfields, function(subfield) {
                // if widget_name exists, it's a special field, use widget_name instead of name
                fields.push({'name': subfield.widget_name || subfield.name});
            }, this);
        }, this);
        return fields;
    },

    /**
     * Restricts specific fields to be shown in available fields list.
     *
     * @param {Object} field Field to be verified.
     * @param {Array} multiLineFields List of fields that appear on the multi-line list view.
     * @return {boolean} True if field is supported, false otherwise.
     */
    isFieldSupported: function(field, multiLineFields) {
        // Specified fields names should be ignored.
        if (!field.name || _.contains(this.ignoredNames, field.name)) {
            return false;
        }

        // Specified field types should be ignored.
        if (_.contains(this.ignoredTypes, field.type) || field.dbType === 'id') {
            return false;
        }

        // Multi-line list view fields should not be displayed.
        if (_.findWhere(multiLineFields, {'name': field.name})) {
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

            if (!_.isUndefined(studio.listview)) {
                if (studio.listview === 'false' || studio.listview === false) {
                    return true;
                }
            }
        }
        return false;
    },

    /**
     * Handles the dragging of the items from available fields list to the columns list section
     * But not the way around
     */
    handleDragAndDrop: function() {
        this.$('#fields-sortable').sortable({
            connectWith: '.connectedSortable',
            update: _.bind(function(event, ui) {
                var multiRow = app.lang.get('LBL_CONSOLE_MULTI_ROW', this.module);
                var multiRowHint = app.lang.get('LBL_CONSOLE_MULTI_ROW_HINT', this.module);
                var hint = '<div class="multi-field-hint">' + multiRowHint + '</div>';
                if ($(ui.sender).hasClass('multi-field') && ui.sender.children().length > 0) {
                    var header = '';
                    var headerLabel = '';
                    var i = 0;
                    _.each(ui.sender.children(), function(field) {
                        if (i > 1) {
                            header += '/';
                            headerLabel += '/';
                        }
                        if (i++ > 0 && !_.isUndefined(field) && !_.isUndefined(field.textContent)) {
                            if (field.textContent.trim() === multiRowHint) {
                                // clean hint text, it will be added later
                                $(field).remove();
                            } else {
                                header += field.textContent.trim();
                                headerLabel += field.getAttribute('fieldlabel');
                            }
                        }
                    }, this);
                    if (header.endsWith('/')) {
                        header = header.slice(0, -1);
                        headerLabel = headerLabel.slice(0, -1);
                    }
                    header = header ? header : multiRow;
                    $(ui.sender.children()[0]).text(header)
                        .append(this.removeColIcon);
                    $(ui.sender.children()[0]).attr('data-original-title', header);
                    $(ui.sender.children()[0]).attr('fieldname', header);
                    $(ui.sender.children()[0]).attr('fieldlabel', headerLabel);
                }
            }, this),
            receive: _.bind(function(event, ui) {
                ui.sender.sortable('cancel');
            }, this)
        });
    }
})
