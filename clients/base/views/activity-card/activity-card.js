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
 * @class View.Views.Base.ActivityCardView
 * @alias SUGAR.App.view.views.BaseActivityCardView
 * @extends View.View
 */
({
    /**
     * The activity model
     */
    activity: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.setActivityModel();
        this.setFieldsToRenderForPanels();
    },

    /**
     * Gets the name of the timeline view
     */
    getViewNameForMeta: function() {
        return this.getActivityCardLayout().getTimelineType();
    },

    /**
     * Get the activity-card layout
     *
     * @return {Object}
     */
    getActivityCardLayout: function() {
        return this.closestComponent('activity-card');
    },

    /**
     * Set the activity model from the activity-card layout
     */
    setActivityModel: function() {
        var layout = this.getActivityCardLayout();

        if (layout && layout.model) {
            this.activity = layout.model;
        }
    },

    /**
     * Get the field definition from fieldsMeta
     *
     * @param name
     * @return {Object}
     */
    getFieldDefFromFieldMeta: function(name) {
        var def = {};
        var fieldsetFieldDef = {};

        var fieldMeta = this.activity && this.activity.get('fieldsMeta') ?
            this.activity.get('fieldsMeta') : {};

        if (!fieldMeta) {
            return def;
        }

        _.some(fieldMeta.panels, function(panel) {
            def = _.find(panel.fields, function(field) {
                var found = field.name === name;

                // if not found and the field is a fieldset, also check the fieldset's fields
                if (!found && field.type === 'fieldset') {
                    fieldsetFieldDef = _.find(field.fields, function(fieldsetField) {
                        return fieldsetField.name === name;
                    });

                    found = !!fieldsetFieldDef;
                }

                return found;
            });

            // if def is not empty and def name does not match, that means a
            // fieldset field was found
            if (!_.isEmpty(def) && def.name !== name && !_.isEmpty(fieldsetFieldDef)) {
                def = fieldsetFieldDef;
            }

            return !!def;
        }, this);

        return def || {};
    },

    /**
     * Check if a field is empty.
     * @param {Object} fieldDef
     * @return {boolean}
     */
    isFieldEmpty: function(fieldDef) {
        const fields = _.union([fieldDef.name], fieldDef.fields, fieldDef.related_fields);
        return !_.find(fields, function(field) {
            const value = this.activity.get(field);
            return !_.isUndefined(value) && value !== '';
        }, this);
    },

    /**
     * Set and pick fields from fieldsMeta
     *
     * panel.fields will be replaced with the picked fields while the default fields
     * will be stored in panel.defaultFields
     */
    setFieldsToRenderForPanels: function() {
        if (!this.meta || !this.meta.panels) {
            return;
        }

        _.each(this.meta.panels, function(panel) {
            // store a copy of default fields metadata
            panel.defaultFields = panel.fields;

            panel.empty = true;
            if (_.isArray(panel.fields)) {
                panel.fields.map((item) => {
                    if (panel.empty && !this.isFieldEmpty(item)) {
                        panel.empty = false;
                    }
                });
            }

            var pickedFields = [];

            _.each(panel.defaultFields, function(field) {
                var meta = this.getFieldDefFromFieldMeta(field.name);
                let pickedField = field;

                if (!_.isEmpty(meta)) {
                    pickedField = _.extend(meta, field);
                }

                pickedField.empty = this.isFieldEmpty(pickedField);
                pickedFields.push(pickedField);
            }, this);

            // replace panel fields with picked fields
            panel.fields = pickedFields;
        }, this);
    },

    /**
     * Get the specified panel from metadata
     *
     * @param panelName the panel to find
     * @return {Object}
     */
    getMetaPanel: function(panelName) {
        if (!this.meta || !this.meta.panels) {
            return {};
        }

        return _.find(this.meta.panels, function(panel) {
            return panel.name === panelName;
        });
    },
})
