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
 * @class View.Fields.Base.Documents.SendDocusignField
 * @alias SUGAR.App.view.fields.BaseDocumentsSendDocusignField
 * @extends View.Fields.Base.RowactionField
 */
 ({
    extendsFrom: 'RowactionField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Set properties before init
     *
     * @param {Object}
     */
    _beforeInit: function(options) {
        options.def.events = _.extend({}, options.def.events, {
            'click [name=send-docusign]': 'sendToDocuSign',
            'click [name=send-docusign-template]': 'sendToDocuSignTemplate'
        });
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this.type = 'rowaction';
    },

    /**
     * Initiate the send process, by opening the tab
     */
    sendToDocuSign: function(e) {
        var controllerCtx = app.controller.context;
        var controllerModel = controllerCtx.get('model');
        var module = controllerModel.get('_module');
        var modelId = controllerModel.get('id');
        var documents = [this.model.id];
        var recipients = [];

        var data = {
            returnUrlParams: {
                parentRecord: module,
                parentId: modelId,
                token: app.api.getOAuthToken()
            },
            recipients: recipients,
            documents: documents
        };

        app.events.trigger('docusign:send:initiate', data);
    },

    /**
     * Initiate the composite send process, by opening the tab
     */
    sendToDocuSignTemplate: function(e) {
        const controllerCtx = app.controller.context;
        const controllerModel = controllerCtx.get('model');
        const module = controllerModel.get('_module');
        const modelId = controllerModel.get('id');
        const documents = [this.model.id];
        const recipients = [];

        var data = {
            returnUrlParams: {
                parentRecord: module,
                parentId: modelId,
                token: app.api.getOAuthToken()
            },
            recipients: recipients,
            documents: documents
        };

        app.events.trigger('docusign:compositeSend:initiate', data, 'selectTemplate');
    }
})
