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
 * @class View.Fields.Base.CalendarHtmleditableTinymceField
 * @alias SUGAR.App.view.fields.BaseCalendarHtmleditableTinymceField
 * @extends View.Fields.Base.BaseHtmleditableTinymceField
 */
({
    /**
     * Fields which should not be available to insert in the template
     */
    badFields: [
        'deleted',
        'team_count',
        'user_name',
        'user_hash',
        'password',
        'is_admin',
        'mkto_id',
        'parent_type'
    ],

    specialFields: [
        'created_by_name',
        'modified_by_name',
        'primary_contact_name',
        'duration_minutes',
        'duration_hours',
        'entry_source',
        'email1'
    ],

    /**
     * Field types which should not be available to insert in the template
     */
    badFieldTypes: [
        'link',
        'id',
        'collection',
        'widget',
        'html',
        'htmleditable_tinymce',
        'image',
        'teamset',
        'team_list',
        'password',
        'file'
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // The plugin 'insertfield' needs to be aplied again to show fields of the current module
        this.listenTo(this.model, 'change:calendar_module', _.bind(this.render, this));
    },

    /**
     * Return submenu items
     *
     * @param editor
     * @return {Array}
     */
    getSubmenu: function(editor) {
        var module = this.model.get('calendar_module');
        var fields = app.metadata.getModule(module).fields;
        fields = _.filter(fields, function(field) {
            return _.contains(this.specialFields, field.name) || (
                !_.isEmpty(field.name) &&
                !_.isEmpty(field.vname) &&
                !_.contains(this.badFields, field.name) &&
                !_.contains(this.badFieldTypes, field.type) &&
                !_.contains(this.badFieldTypes, field.dbType) &&
                field.link_type !== 'relationship_info' &&
                (
                    _.isUndefined(field.studio) ||
                    (_.isObject(field.studio) || field.studio == 'true' || field.studio == true)
                ) &&
                field.source !== 'non-db' &&
                typeof field.processes == 'undefined'
            );
        });

        fields.push({
            name: 'event_timestamp',
            vname: app.lang.getModString('LBL_INSERTFIELD_EVENT_TIMESTAMP', 'Calendar')
        });

        var insertOptions = [];

        fields = _.sortBy(fields, function sortFieldsAlphabetically(field) {
            var fieldLabel = app.lang.get(field.vname, module);

            if (_.isString(fieldLabel)) {
                return fieldLabel.toLowerCase();
            } else {
                return '';
            }
        });

        _.each(
            fields,
            function(field) {
                var fieldLabel = app.lang.get(field.vname, module);

                var option = {
                    type: 'menuitem',
                    text: fieldLabel,
                    onAction: () =>
                        editor.insertContent(`{::${field.name}::}`),
                };

                insertOptions.push(option);
            }, this
        );

        return insertOptions;
    },

    /**
     * Add custom button to the UI
     *
     * @param editor
     */
    addCustomButtons: function(editor) {
        if (!app.acl.hasAccess('view', this.model.get('calendar_module'))) {
            return;
        }
        if (_.isEmpty(this.model.get('calendar_module'))) {
            return;
        }

        editor.ui.registry.addMenuButton('insertfield_calendar', {
            text: app.lang.getModString('LBL_INSERTFIELD', 'Calendar'),
            onAction: () => {},
            fetch: (callback) =>
                callback(this.getSubmenu(editor)),
        });
    },

    /**
     * @override
     */
    getTinyMCEConfig: function() {
        var getConfig = this._super('getTinyMCEConfig') || {};

        getConfig.toolbar += ' insertfield_calendar';

        if (this.fieldDefs.name == 'ical_event_template') {
            getConfig.toolbar = 'insertfield_calendar';
        }
        return getConfig;
    }
});
