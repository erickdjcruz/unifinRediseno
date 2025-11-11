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
 * @class View.Fields.Base.CJForms.ActionTypeField
 * @alias SUGAR.App.view.fields.BaseCJFormsActionTypeField
 * @extends View.Fields.Base.EnumField
 */
({
    /**
     * ActionType FieldTemplate (base)
     */
    extendsFrom: 'EnumField',

    /*
     * Used to remove the option at runtime from
     * action type on the base of relationship field data
     *
     * structure is like:
     *
     * 'Module' : [
     *      <option keys>
     *  ]
     */
    removeOptionsArray: {
        'Emails': [
            'update_record',
        ],
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';
        if (this.model) {
            this.listenTo(this.model, 'sync', this.modelSyncHandler);
        }
    },

    /**
     * Model On sync Handler
     */
    modelSyncHandler: function() {
        this.reloadEnumOptions();
        this.hideOrShowPopulateFieldPanel();
    },

    /**
     * It will hide/show the populate field panel on the base of action_type
     */
    hideOrShowPopulateFieldPanel: function() {
        if (
            _.isEmpty(this.model.get('main_trigger_type')) ||
            _.isEqual(this.model.get('main_trigger_type'), 'sugar_action_to_smart_guide')
        ) {
            // trigger event to hide the populate field panel
            this.view.trigger('record:showHidePanel', 'LBL_RECORDVIEW_PANEL4', false);
        }
        if (
            _.contains(['create_record', 'update_record'], this.model.get('action_type')) &&
            ((this.action === 'edit' && !_.isEmpty(this.model.get('parent_id'))) || this.action === 'detail')
        ) {
            // trigger event to show the populate field panel
            this.view.trigger('record:showHidePanel', 'LBL_RECORDVIEW_PANEL4', true);
            if (this.view) {
                let field = this.view.getField('populate_fields');
                if (field) {
                    field.render();
                }
            }
        } else {
            // trigger event to hide the populate field panel
            this.view.trigger('record:showHidePanel', 'LBL_RECORDVIEW_PANEL4', false);
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        if (this.model) {
            this.listenTo(this.model, 'change:relationship', this.reloadEnumOptions);
            this.listenTo(this.model, `change:${this.name}`, this.bindDataChangeHandler);
            this.listenTo(this.model, 'change:main_trigger_type', this.bindDataChangeHandler);
            this.listenTo(this.model, 'change:parent_id', this.hideOrShowPopulateFieldPanel);
        }
    },

    /**
     * Bind Data Change Handler
     */
    bindDataChangeHandler: function() {
        let isEmailLast = this.checkLastRelationship('Emails');
        this.hideOrShowEmailRelatedField(isEmailLast);
        this.hideOrShowPopulateFieldPanel();
    },

    /**
     * Hide or show the Email related fields according to Relationship
     *
     * @param {boolean} hideOrShow
     */
    hideOrShowEmailRelatedField: function(hideOrShow) {
        if (
            _.isEqual(this.model.get(this.name), 'view_record') ||
            _.isEmpty(this.model.get('main_trigger_type')) ||
            _.isEqual(this.model.get('main_trigger_type'), 'sugar_action_to_smart_guide')
        ) {
            hideOrShow = false;
        }

        if (this.view) {
            let hideOrShowFields = ['email_templates_name', 'select_to_email_address'];
            // trigger event to show / Hide the Email related fields panel
            this.view.trigger('record:showHidePanel', 'LBL_RECORDVIEW_PANEL_EMAIL_FIELDS', hideOrShow);

            _.each(hideOrShowFields, function(fieldName) {
                let field = this.view.getField(fieldName);
                if (field) {
                    if (!!hideOrShow) {
                        app.CJFieldHelper._showField(field);
                    } else {
                        app.CJFieldHelper._hideField(field, _.bind(function() {
                            if (_.isFunction(field.restVariablesAndData)) {
                                field.restVariablesAndData();
                            }
                        }, field));
                        this.model.set(fieldName, '');
                        this.model.set('email_templates_id', '');
                    }
                }
            }, this);
        }
    },

    /**
     * It will reload the options and re-render it
     */
    reloadEnumOptions: function() {
        this.loadEnumOptions(false, function() {
            // Re-render widget since we have fresh options list
            if (!this.disposed) {
                this.render();
            }
        });
    },

    /**
     * @inheritdoc
     */
    loadEnumOptions: function(fetch, callback, error) {
        this._super('loadEnumOptions', [fetch, callback, error]);
        if (this.items) {
            this.processAfterEnumLoad();
        }
    },

    /**
     * Process the options after enum load to check
     * the last relationship and then make adjusments
     *
     * @param {boolean} updateItems
     */
    processAfterEnumLoad: function(updateItems = true) {
        let isEmailLast = this.checkLastRelationship('Emails');
        if (isEmailLast && updateItems) {
            this.updateItems('Emails');
        }
        this.hideOrShowEmailRelatedField(isEmailLast);
    },

    /**
     * It will add/remove the options accordingly
     *
     * @param {string} module
     */
    updateItems: function(module) {
        if (!_.isUndefined(module) && !_.isUndefined(this.removeOptionsArray[module])) {
            _.each(this.removeOptionsArray[module], function(enumKey) {
                delete this.items[enumKey];
            }, this);

            if (_.isEqual(this.model.get(this.name), 'update_record')) {
                this.model.set(this.name, '');
                this.model.set('action_trigger_type', '');
            }
            if (!this.disposed) {
                this.render();
            }
        }
    },

    /**
     * Return the last selected relationship
     *
     * @return {Object|undefined}
     */
    getLastRelationship: function() {
        if (!this.model) {
            return;
        }
        let relationship = this.model.get('relationship');
        if (!_.isUndefined(relationship)) {
            return _.last(relationship);
        }
    },

    /**
     * Check the last selected relationship
     * according to the given module and relationship type
     *
     * @param {string} module
     * @param {string} relationship
     * @return {boolean}
     */
    checkLastRelationship: function(module, relationship = 'self') {
        let lastIndex = this.getLastRelationship();
        if (_.isUndefined(lastIndex) || _.isNull(lastIndex)) {
            return false;
        }
        if (
            !_.isUndefined(lastIndex.module) &&
            !_.isUndefined(lastIndex.relationship) &&
            _.isEqual(lastIndex.module, module) &&
            _.isEqual(lastIndex.relationship, relationship)
        ) {
            return true;
        }
        return false;
    },
});
