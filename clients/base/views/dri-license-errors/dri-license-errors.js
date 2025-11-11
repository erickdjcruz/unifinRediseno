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
 * @class View.Views.Base.DRILicenseErrors
 * @alias SUGAR.App.view.views.DRILicenseErrors
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.listenTo(app.events, 'data:sync:error', this.onError);
    },

    /**
     * Displays an error when having an invalid license
     *
     * @param {string} method
     * @param {Object} model
     * @param {Object} options
     * @param {Object} error
     */
    onError: function(method, model, options, error) {
        if (error.code === 'invalid_license') {
            app.CJBaseHelper.invalidLicenseError();
        }
    },
});
