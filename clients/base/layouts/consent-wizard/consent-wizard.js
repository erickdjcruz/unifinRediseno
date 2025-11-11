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
 * @class View.Layouts.Base.ConsentWizardLayout
 * @alias SUGAR.App.view.layouts.BaseConsentWizardLayout
 * @extends View.Layouts.Base.WizardLayout
 */
({
    extendsFrom: 'WizardLayout',

    /**
     * @deprecated Since 14.0.0. Will be removed in 14.1.0.
     * @param options
     */
    initialize: function(options) {
        app.logger.warn('View.Layouts.Base.ConsentWizardLayout is deprecated and will be removed in 14.1.0');
        this._super('initialize', [options]);
    },

    /**
     * Skip to the Layout addComponent to ignore logic around adding buttons to meta
     * inside wizard.js
     *
     * @override
     */
    addComponent: function(component, def) {
        if (_.result(component, 'showPage')) {
            app.view.Layout.prototype.addComponent.call(this, component, def);
        }
    }
})
