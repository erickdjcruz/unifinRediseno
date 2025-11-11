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
 * @class View.Fields.Base.UnlinkcabField
 * @alias SUGAR.App.view.fields.BaseUnlinkcabField
 * @extends View.Fields.Base.CabField
 */
({
    extendsFrom: 'CabField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'cab';
    },

    /**
     * Handle the click event on "Unlink" dropdown menu item
     *
     * @param {Event}
     */
    handleClick: function() {
        var unlinkMessages = this.getUnlinkMessages(this.model);

        app.alert.show('unlink_confirmation', {
            level: 'confirmation',
            messages: unlinkMessages.confirmation,
            onConfirm: _.bind(this.destroyModel, this, this.model, unlinkMessages.success)
        });
    },

    /**
     * Destroy the selected model
     */
    destroyModel: function(model, unlinkMessagesSuccess) {
        model.destroy({
            showAlerts: {
                'process': true,
                'success': {
                    messages: unlinkMessagesSuccess
                }
            },
            relate: true,
            success: _.bind(function() {
                let parentModel = this.context && this.context.parent &&
                    this.context.parent.get('model') || null;
                let parentModule = parentModel ? parentModel.get('_module') : '';
                let parentId = parentModel ? parentModel.get('id') : '';
                app.events.trigger('timeline:link:removed', parentModule, parentId, model);
                this.reloadData();
            }, this)
        });
    },

    /**
     * Format the messages to display in the alerts when unlinking a record.
     *
     * @param {Data.Bean} model The model concerned.
     * @return {Object} The list of messages.
     * @return {string} return.confirmation Confirmation message.
     * @return {string} return.success Success message.
     */
    getUnlinkMessages: function(model) {
        var module = model.get('_module') || '';
        var moduleName = app.lang.getModuleName(module) || '';
        var msgContext = [moduleName.toLowerCase(), model.get('name')].join(' ');
        var messages = {
            confirmation: app.utils.formatString(app.lang.get('NTC_UNLINK_CONFIRMATION_FORMATTED'), [msgContext]),
            success: app.utils.formatString(app.lang.get('NTC_UNLINK_SUCCESS'), [msgContext])
        };

        return messages;
    },

    /**
     * Reload data after unlink
     */
    reloadData: function() {
        if (_.isFunction(this.view.reloadData)) {
            this.view.reloadData();
        }
    }
})
