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
 * Portal Cases Record view.
 *
 * @class View.Views.Portal.Cases.PortalRecordView
 * @alias SUGAR.App.view.views.PortalCasesRecordView
 * @extends View.Views.Portal.PortalRecordView
 */
({
    extendsFrom: 'PortalRecordView',

    /**
     * List of statuses for which the Request To Close button is not shown
     * @property {Array}
     */
    blackListOfStatuses: [
        'Closed',
        'Rejected',
    ],

    initialize: function(options) {
        this._super('initialize', [options]);
        this.events = _.extend({}, this.events, {
            'click [name=request_close_button]': 'requestClose'
        });
    },

    /**
     * Listens for a change in the model and updates the rendering of the page to either display or hide the request
     * close button
     */
    bindDataChange: function() {
        var self = this;
        if (this.model) {
            this.model.on('change', function() {
                if (self.model.get('request_close') === false &&
                    app.metadata.getConfig('Cases').allowCloseCase === 'allow') {
                    self.setUpRequestClose();
                }
            }, this);
        }
    },

    /**
     * Sets up the view depending on configuration for requesting close and current status on the case
     */
    setUpRequestClose: function() {
        if (this.shouldShowCloseButton()) {
            this.$('[name=request_close_button]').removeClass('hidden');
        }
    },

    /**
     * Returns true if the request close button should be shown in the portal record view
     */
    shouldShowCloseButton: function() {
        return this.model.get('request_close') === false &&
            app.metadata.getConfig('Cases').allowCloseCase === 'allow' &&
            !_.contains(this.blackListOfStatuses, this.model.get('status'));
    },

    /**
     * Triggered when the request close button is clicked by the user
     */
    requestClose: function() {
        var self = this;
        app.alert.show('confirm', {
            level: 'confirmation',
            messages: app.lang.get('LBL_REQUEST_CLOSE_MESSAGE', 'Cases'),
            autoClose: false,
            onConfirm: function() {
                var url = app.api.buildURL('Cases/' + self.model.get('id') + '/request_close', null, null, null);
                app.api.call('update', url, null, {
                    success: function(result) {
                        self.$('[name=request_close_button]').addClass('hidden');
                        self.context.reloadData();
                    },
                    error: function(e) {
                        // Continue to use Sugar7's default error handler.
                        if (_.isFunction(app.api.defaultErrorHandler)) {
                            app.api.defaultErrorHandler(e);
                        }
                    }
                });
            },
        });
    },
})

