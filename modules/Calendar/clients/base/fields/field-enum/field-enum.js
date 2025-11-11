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
 * @class View.Fields.Base.FieldEnumField
 * @alias SUGAR.App.view.fields.BaseFieldEnumField
 * @extends View.Fields.Base.BaseFieldEnumField
 */
({
    extendsFrom: 'BaseEnumField',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        if (this.model) {
            //Check if the record is copied
            if (typeof this.context.get('copiedFromModelId') === 'string') {
                this._updateFieldDropdown();
            } else {
                this.listenTo(this.model, 'sync', function() {
                    this._updateFieldDropdown();
                }.bind(this));
            }

            this.listenTo(this.model, 'change:calendar_module', _.bind(this._updateFieldDropdown, this));
        }
    },

    /**
     * Update list of items
     */
    _updateFieldDropdown: function() {
        if (this.name == 'dblclick_event') {
            var dropdownOptions = {};
            dropdownOptions['detail:self:id'] = app.lang.get('LBL_NAVIGATE_TO_RECORD', 'Calendar'),
                dropdownOptions['detail-newtab:self:id'] = app.lang.get('LBL_NAVIGATE_TO_RECORD_NEW_TAB', 'Calendar');
            dropdownOptions['edit:self:id'] = app.lang.get('LBL_OPEN_DRAWER_FOR_EDIT', 'Calendar');

            var moduleMetadata = app.metadata.getModule(this.model.get('calendar_module'));
            if (moduleMetadata) {
                var fieldsMetadata = moduleMetadata.fields;
                _.each(fieldsMetadata, _.bind(function(fieldMetadata) {
                    var moduleName = this.model.get('calendar_module');
                    if (fieldMetadata.type == 'relate' &&
                       typeof fieldMetadata.module == 'string' && typeof fieldMetadata.id_name == 'string') {
                        dropdownOptions['detail:' + fieldMetadata.module + ':' + fieldMetadata.id_name] =
                            app.lang.get('LBL_NAVIGATE_TO_RECORD', 'Calendar') +
                            ' (' + app.lang.get(fieldMetadata.vname, moduleName) + ')';
                        dropdownOptions['detail-newtab:' + fieldMetadata.module + ':' + fieldMetadata.id_name] =
                            app.lang.get('LBL_NAVIGATE_TO_RECORD_NEW_TAB', 'Calendar') +
                            ' (' + app.lang.get(fieldMetadata.vname, moduleName) + ')';
                        dropdownOptions['edit:' + fieldMetadata.module + ':' + fieldMetadata.id_name] =
                            app.lang.get('LBL_OPEN_DRAWER_FOR_EDIT', 'Calendar') +
                            ' (' + app.lang.get(fieldMetadata.vname, moduleName) + ')';

                        fieldMetadata.id;
                    }
                }, this));
            }
            this.items = dropdownOptions;
        } else {
            if (!_.isArray(this.fieldDefs.field_types_allowed)) {
                return;
            }

            var fieldsMetadata = [];
            if (this.model.get('calendar_module') == '' || _.isUndefined(this.model.get('calendar_module'))) {
                return;
            }
            var calendarModule = this.model.get('calendar_module');

            var moduleMetadata = app.metadata.getModule(calendarModule);

            if (moduleMetadata) {
                fieldsMetadata = moduleMetadata.fields;

                var dropdownOptions = {};
                if (this.name != 'event_start') {
                    dropdownOptions[''] = '';
                }
                _.each(fieldsMetadata, function(fieldMetadata) {
                    if (typeof fieldMetadata == 'object') {
                        var fieldType = fieldMetadata.dbType || fieldMetadata.dbtype || fieldMetadata.type;
                        var fieldSource = fieldMetadata.source || '';
                        if (
                            this.fieldDefs.field_types_allowed.indexOf(fieldType) >= 0 &&
                            fieldSource != 'non-db' &&
                            this.model.denyFields.indexOf(fieldMetadata.name) == -1
                        ) {
                            dropdownOptions[fieldMetadata.name] = app.lang.get(fieldMetadata.vname, calendarModule);
                        }
                    };
                }, this);

                this.items = dropdownOptions;
            }
        }

        this.render();
    },

    /**
     * Load enum options will not be needed on this field type
     */
    loadEnumOptions: function(fetch, callback, error) {
    }
});
