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
 * @class View.Views.Base.AdministrationJobListField
 */
({
    initialize: function(options) {
        this._super('initialize', [options]);

        this.on('render', function() {
            this.loadData();
        }, this);

        this.events = _.extend({}, this.events, {
            'click [data-action=refreshList]': 'loadData',
            'click [data-action=removeJob]': 'removeJob'
        });
    },

    /**
     * @inheritdoc
     */
    loadData: function() {
        var options = {};

        options.success = _.bind(function(data) {
            this.buildList(data);
        }, this);
        app.api.call(
            'read', app.api.buildURL(this.module, 'denormalization/status'), [], options, {context: this}
        );
    },

    buildList: function(data) {
        data.dataParsed = data.data ? JSON.parse(data.data) : {};
        data.dataPretty = JSON.stringify(data.dataParsed, null, 2);
        data.module = this.module;
        this.$el.html(this.template(data));
    },

    removeJob: function() {
        var self = this;

        app.alert.show('relate-denormalization-rm-job-warning', {
            level: 'confirmation',
            title: app.lang.get('LBL_WARNING'),
            messages: app.lang.get('LBL_ALERT_CONFIRM_DELETE'),
            onConfirm: function() {
                var options = {
                    success: function() {
                        self.loadData();
                    }
                };
                app.api.call(
                    'create', app.api.buildURL(self.module, 'denormalization/abort'), [], options, {context: self}
                );
            }
        });
    }
})
