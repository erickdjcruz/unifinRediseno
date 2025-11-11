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
 * @class View.Layouts.Base.Calendar.AddCalendarLayout
 * @alias SUGAR.App.view.layouts.BaseAddCalendarLayout
 * @extends View.Layouts.Base.BaseLayout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.collection.sync = this.sync;
        this.collection.getSyncCallbacks = this.getSyncCallbacks;

        this.collection.allowed_modules = ['Users', 'Teams'];
        this.context.on('calendar:add:search', this.search, this);
    },

    /**
     * Collection's sync method
     *
     * @param {string} method
     * @param {Data.Bean} model
     * @param {Object} options
     */
    sync: function(method, model, options) {
        let callbacks;
        let url;

        options = options || {};

        // only fetch from the approved modules
        if (_.isEmpty(options.module_list)) {
            options.module_list = ['all'];
        } else {
            options.module_list = _.intersection(this.allowed_modules, options.module_list);
        }

        app.config.maxQueryResult = app.config.maxQueryResult || 20;
        options.limit = options.limit || app.config.maxQueryResult;

        options = app.data.parseOptionsForSync(method, model, options);

        callbacks = this.getSyncCallbacks(method, model, options);
        this.trigger('data:sync:start', method, model, options);

        url = app.api.buildURL('Calendar', 'usersAndTeams', null, options.params);
        app.api.call('read', url, null, callbacks);
    },

    /**
     * @override
     * Customizations needed just for teams_offset flag we send from server
     */
    getSyncCallbacks: function(method, model, options) {
        return {
            success: app.data.getSyncSuccessCallback(method, model, options),
            error: app.data.getSyncErrorCallback(method, model, options),
            complete: app.data.getSyncCompleteCallback(method, model, options),
            abort: app.data.getSyncAbortCallback(method, model, options)
        };
    },
    /**
     * Adds the set of modules and term that should be used to search for recipients.
     *
     * @param {Array} modules
     * @param {string} term
     */
    search: function(modules, term) {
        // reset offset to 0 on a search. make sure that it resets and does not update.
        this.collection.fetch({
            query: term,
            module_list: modules,
            offset: 0,
            update: false
        });
    },

    /**
     * @override
     */
    _dispose: function() {
        this.context.off('calendar:add:search');

        this._super('_dispose');
    }
});
