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
 * @class App.view.views.Base.CjWebhookDashletView
 * @alias SUGAR.App.view.views.BaseCjWebhookDashletView
 * @extends View.Views.Base.View
 */
({
    plugins: ['Dashlet'],

    /**
     * Variable to store data
     *
     * @property
     */
    data: null,

    /**
     * Events to be called on click
     *
     * @property
     */
    events: {
        'click .sendRequest': 'sendClicked',
    },

    /**
     * Dashlet click action
     *
     * @property
     */
    defaultActions: {
        'dashlet:edit:clicked': 'editClicked',
        'dashlet:refresh:clicked': 'refreshClicked',
        'dashlet:delete:clicked': 'removeClicked',
        'dashlet:send:clicked': 'sendClicked',
    },

    /**
     * Error mapping
     *
     * @property
     */
    tplErrorMap: {
        ERROR_INVALID_LICENSE: 'invalid-license',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        Handlebars.registerPartial('cj-webhook-dashlet.loader', app.template.get('cj-webhook-dashlet.loader'));
        this._super('initialize', [options]);
        this._noAccessTemplate = app.template.get(`${ this.name }.noaccess`);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!app.user.hasAutomateLicense()) {
            this.$el.html(this._noAccessTemplate());
            return;
        }

        this._super('_render');

        if (this.loaded) {
            this.$('pre').show();
        }
    },

    /**
     * Start loading after the request has sent
     */
    startLoading: function() {
        let parent = this.$el.parent();
        let dashletLoadingElement = this.$('.cj-dashlet-loading');

        let width = parent.width();
        let height = parent.height();

        dashletLoadingElement.height(height);
        dashletLoadingElement.width(width);
        dashletLoadingElement.show();
    },

    /**
     * Send click handler
     */
    sendClicked: function() {
        this._retrieveData();
    },

    /**
     * Retrieve the data from custom end point
     */
    _retrieveData: function() {
        this.loaded = false;
        this.data = null;

        this.startLoading();

        let url = app.api.buildURL(this.model.module, 'send-request', {
            id: this.model.get('id'),
        });

        app.api.call('read', url, null, {
            success: _.bind(this.loadCompleted, this),
            error: _.bind(this.loadError, this),
            complete: _.bind(this.stopLoading, this),
        });
    },

    /**
     * Set template and data when send-request api is successful
     *
     * @param {Object} data
     */
    loadCompleted: function(data) {
        this.loaded = true;
        this.error = '';
        this.template = app.template.get(this.name);
        this.data = this.getJsonString(data);

        if (!this.disposed) {
            this.render();
        }
    },

    /**
     * Show error template when send-request api fails
     *
     * @param {Object} error
     */
    loadError: function(error) {
        this.loaded = true;

        if (this.disposed) {
            return;
        }

        let tpl = this.tplErrorMap[error.message] || 'error';

        if (_.isUndefined(error.message)) {
            error.message = 'ERR_HTTP_DEFAULT_TEXT';
        }

        this.error = error;
        this.template = app.template.get(`${this.name}.${tpl}`);
        this.render();
    },

    /**
     * Stop the Loading when response will receive from End Point
     */
    stopLoading: function() {
        this.$('.cj-dashlet-loading').hide();
    },

    /**
     * Return data in JSON string if data is parsed successfully in JSON format
     * else data as it is
     *
     * @param {string} data
     */
    getJsonString: function(data) {
        let response = data;

        try {
            const jsonObj = JSON.parse(data);

            if (jsonObj && _.isObject(jsonObj)) {
                response = JSON.stringify(jsonObj, null, 2);
            }
        } catch (e) {}

        return response;
    },
});
