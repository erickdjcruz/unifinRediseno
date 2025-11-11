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
 * @class View.Views.Base.Audit.ActivityCardContentView
 * @alias SUGAR.App.view.views.BaseAuditActivityCardContentView
 * @extends View.Views.Base.ActivityCardContentView
 */
({
    extendsFrom: 'ActivityCardContentView',

    /**
     * The panel_change panel metadata
     */
    changePanel: null,

    /**
     * A list of change field definitions for a single field
     *
     * 0 name def | 1 before def | 2 after def
     */
    changeDef: [],

    /**
     * Enum type fields that can take the type of 'enum-colorcoded' field
     */
    enumColorcodedFields: [
        'status'
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.setChangeDef(true);
    },

    /**
     * Initializes hbs date variables with date_created
     */
    initDateDetails: function() {
        if (this.activity && this.activity.get('date_created')) {
            value = app.date(this.activity.get('date_created'));

            if (value.isValid()) {
                this.dateModified = value.formatUser();
            }
        }
    },

    /**
     * Get and cache the change panel
     *
     * @return {Object}
     */
    getChangePanel: function() {
        if (!this.changePanel) {
            this.changePanel = this.getMetaPanel('panel_change');
        }

        return this.changePanel;
    },

    /**
     * Get the specified field def from the change panel's
     * defaultFields
     *
     * @param fieldName the field name to find
     * @return {Object} the field def from panel metadata
     */
    getFieldDefFromChangePanelFields: function(fieldName) {
        var panel = this.getChangePanel();

        return _.find(panel.defaultFields, function(field) {
            return field.name === fieldName;
        });
    },

    /**
     * Get the specified field from the model and add relevant data
     *
     * @param model
     * @param fieldName
     * @param type
     * @return {Object}
     */
    getChangeFieldDefFromModel: function(model, fieldName, type) {
        var def = {};

        // retrieving field def from the model will not have the defined css_class
        var fieldDefFromPanel = this.getFieldDefFromChangePanelFields(type);
        if (fieldDefFromPanel) {
            def.css_class = fieldDefFromPanel.css_class;
        }

        // if the value is empty, return minimal field def
        if (!model.get(fieldName)) {
            return def;
        }

        def = _.extend(
            def,
            _.find(model.fields, function(field) {
                return field.name === fieldName;
            }),
            {
                model: model
            }
        );

        // convert fields to type 'enum-colorcoded' if conditions hold
        if (type === 'after' && def.type === 'enum' &&
            _.indexOf(this.enumColorcodedFields, fieldName) !== -1) {
            def.type = 'enum-colorcoded';
            def.template = 'list';
        }

        return def;
    },

    /**
     * Get the specified field from the panel and add relevant data
     *
     * @param model
     * @param fieldName
     * @return {Object}
     */
    getChangeFieldDefFromPanel: function(model, fieldName) {
        var def = this.getFieldDefFromChangePanelFields(fieldName);

        if (!def) {
            return {};
        }

        return _.extend(def, {
            model: model
        });
    },

    /**
     * Get the change value from the activity model
     *
     * Depending on the field, the change value can be in an array or a string
     *
     * @param type 'before' or 'after'
     * @return {string} the value
     */
    getChangeValue: function(type) {
        var value = this.activity.get(type);
        return _.isArray(value) ? _.first(value) : value;
    },

    /**
     * Get the change model for the specified module and type
     *
     * @param module the parent module (usually not Audit)
     * @param type 'before' or 'after'
     * @return {Bean}
     */
    getTypeChangeModel: function(module, type) {
        var value = this.getChangeValue(type);
        var fieldName = this.activity.get('field_name');

        return app.data.createBean(module, {
            [fieldName]: value
        });
    },

    /**
     * Set change def with change fields
     *
     * @param resetDef true will clear existing changeDef
     */
    setChangeDef: function(resetDef) {
        if (!this.activity) {
            return;
        }

        if (resetDef) {
            this.changeDef = [];
        }

        var parentModule = this.activity.get('parent_model').get('_module') ?
            this.activity.get('parent_model').get('_module') : this.context.get('module');
        var fieldName = this.activity.get('field_name');

        // the hbs template expects the following index order:
        // 0 name field
        // 1 before field
        // 2 after field
        this.changeDef.push(
            this.getChangeFieldDefFromPanel(this.activity, 'field_name'),
            this.getChangeFieldDefFromModel(
                this.getTypeChangeModel(parentModule, 'before'),
                fieldName,
                'before'
            ),
            this.getChangeFieldDefFromModel(
                this.getTypeChangeModel(parentModule, 'after'),
                fieldName,
                'after'
            )
        );
    }
})
