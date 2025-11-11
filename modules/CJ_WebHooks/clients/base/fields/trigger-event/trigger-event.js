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
 * @class View.Fields.Base.CJWebHooksTriggerEvent
 * @alias SUGAR.App.view.fields.BaseCJWebhooksTriggerEventField
 * @extends View.Fields.Base.EnumField
 */
 ({
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';
        if (this.model) {
            this.model.on('change:parent_id', this._modelChangeHandler, this);
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
         this._super('_render');
         this._modelChangeHandler();
     },

    /**
     * Updates the model with required changes for the activity
     */
    _modelChangeHandler: function() {
        if (_.isEqual(this.model.get('parent_type'), 'DRI_Workflow_Task_Templates') &&
            !_.isEmpty(this.model.get('parent_id'))) {
            let list = app.lang.getAppListStrings(this.def.options);
            let removeOptions = ['before_in_progress', 'after_in_progress'];
            let addOptions = {
                'before_in_progress': list.before_in_progress,
                'after_in_progress': list.after_in_progress,
            };
            let bean = app.data.createBean(this.model.get('parent_type'), {
                id: this.model.get('parent_id'),
            });
            bean.fetch({
                fields: ['activity_type'],
                success: _.bind(function() {
                    this.editOptionsOnSuccess(bean, removeOptions, addOptions);
                }, this)
            });
        }
    },

    /**
     * Edit options on bean fetch success
     * @param {Object} bean The bean of related parent module.
     * @param {Array} removeOptions The options to remove from dropdown.
     * @param {Array} addOptions The options to add in the dropdown.
     */
    editOptionsOnSuccess: function(bean, removeOptions, addOptions) {
        if (_.contains(['Calls', 'Meetings'], bean.get('activity_type'))) {
            _.each(this.items, function(item, key) {
                if (_.contains(removeOptions, key)) {
                    delete this.items[key];
                }
            }, this);
            if (_.contains(removeOptions, this.model.get(this.name))) {
                this.model.set(this.name, '');
            }
        } else {
            _.extend(this.items, addOptions);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.model) {
            this.model.off('change:parent_id', this._modelChangeHandler, this);
        }
        this._super('_dispose');
    },
})
