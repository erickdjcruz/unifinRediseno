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
 * Login form view.
 *
 * @class View.Views.Portal.LoginView
 * @alias SUGAR.App.view.views.PortalLoginView
 * @extends View.Views.Base.LoginView
 */
({
    plugins: ['ErrorDecoration'],

    events: {
        'click [name=login_button]': 'login',
        'click [name=signup_button]': 'signup',
        'keypress': 'handleKeypress',
        'click [name=preferred_language]': 'setLanguage'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // need to set the default/selected language
        this.model.setDefault('preferred_language', app.lang.getLanguage() || 'en_us');

        this.model.on('change:preferred_language', function() {
            this.setLanguage(this.model.get('preferred_language'));
        }, this);

        this.showPortalPasswordReset = false;
        if (app.config.smtpServerSet === true) {
            this.showPortalPasswordReset = true;
        }

        this.showPortalSignUp = app.config.enableSelfSignUp === 'enabled';
    },

    /**
     * When a user selects a language in the dropdown, set this language.
     * Note that on login, user's preferred language will be updated to this language
     *
     * @param {string} language
     */
    setLanguage: function(language) {
        if (!language) {
            return;
        }
        app.alert.show('language', {level: 'warning', title: app.lang.get('LBL_LOADING_LANGUAGE'), autoclose: false});
        app.user.setPreference('language',language);
        app.lang.setLanguage(language, function() {
            app.alert.dismiss('language');
        }, this);
    },

    /**
     * Gets the logo image for portal
     */
    getLogoImage: function() {
        // get the image urls for portal
        return app.config.logoURL || app.config.logomarkURL || app.metadata.getLogoUrl();
    },

    /**
     * Navigate to the `Signup` view.
     */
    signup: function() {
        app.router.navigate('#signup', {trigger: true});
    },

    /**
     * @override
     *
     * There is no need to see if there's any post login setup we need to do
     * unlike in the super class. We simply render.
     */
    postLogin: function() {
        app.$contentEl.show();
    },

    /**
     * @inheritdoc
     *
     * Remove event handler for hiding `forgot password` tooltip.
     */
    _dispose: function() {
        $(document).off('click.login');
        this._super('_dispose');
    }
})
