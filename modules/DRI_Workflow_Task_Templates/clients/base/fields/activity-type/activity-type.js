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
 * @class View.Fields.Base.CjDRIWorkflowTaskTemplateActivityTypeField
 * @alias SUGAR.App.view.fields.BaseCjDRIWorkflowTaskTemplateActivityTypeField
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
        this.model.on('sync', this.modelSyncHandler, this);
    },

    /**
     * Model On sync Handler
     */
    modelSyncHandler: function() {
        this.hideOrShowGuestsField();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        if (this.model) {
            this.model.on(`change:${this.name}`, this.bindDataChangeHandler, this);
        }
    },

    /**
     * Bind Data Change Handler
     */
    bindDataChangeHandler: function() {
        this.hideOrShowGuestsField();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        if (!_.isUndefined(this.view) && !_.isUndefined(this.view.action) && this.view.action === 'detail') {
            this.hideOrShowGuestsField();
        }
    },

    /**
     * It will hide the Guests field
     * for Tasks activity type
     */
    hideOrShowGuestsField: function() {
        if (this.view) {
            var field = this.view.getField('select_to_guests');
            if (field) {
                if (this.model && _.contains(['Calls', 'Meetings'], this.model.get(this.name))) {
                    app.CJFieldHelper._showField(field);
                } else {
                    app.CJFieldHelper._hideField(field, _.bind(function() {
                        field.resetVariablesAndData();
                    }, field));
                    this.model.set(field.name, '');
                }
            }
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.model.off(`change:${this.name}`, this.bindDataChangeHandler, this);
        this._super('_dispose');
    },
});
