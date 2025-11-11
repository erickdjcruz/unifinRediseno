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
 * @class View.Views.Base.AdministrationErrorsView
 * @alias SUGAR.App.view.views.BaseAdministrationErrorsView
 * @extends View.Views.Base.View
 */
({
    /**
     * Errors
     * @property {Array}
     */
    errors: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.fetchErrors();
    },

    /**
     * Fetch errors and render if successful
     */
    fetchErrors: function() {
        this.errors = [];

        app.api.call('read', app.api.buildURL(this.module, 'errors'), [], {
            success: _.bind(function(errors) {
                if (this.disposed) {
                    return;
                }
                this.errors = errors;
                this.render();

                this.updateContentGridWrapperStyles();
            }, this)
        });
    },

    /**
     * Update styles on the content-grid-wrapper
     */
    updateContentGridWrapperStyles: function() {
        let contentGridWrapper = this.layout.getComponent('content-grid-wrapper');

        if (!contentGridWrapper) {
            return;
        }

        if (!_.isEmpty(this.errors)) {
            contentGridWrapper.$el.removeClass('pt-8');
        } else {
            contentGridWrapper.$el.addClass('pt-8');
        }
    },
});
