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
 * @class View.Views.Base.AdministrationCspConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationCspConfigView
 * @extends View.Views.Base.AdministrationConfigView
 */
({
    extendsFrom: 'AdministrationConfigView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.helpBlockContext = {
            linkToDocumentation: app.help.getDocumentationUrl('ContentSecurityPolicy')
        };

        this._super('initialize', [options]);
        this.meta.firstNonHeaderPanelIndex = 0; // there is no header, so it's always 0
    },

    /**
     * Triggers the field validation through the model.
     * Validation of the following components: IPv4 and IPv6 addresses.
     */
    validateModel: function(fields = null) {
        if (!fields) {
            // pass 'fake' as the module name so the view fields don't get filtered out
            fields = this.getFieldNames('fake');
        }
        let isValid = _.every(fields, function(field) {
            if (this.validateSpecificValue(this.model.get(field))) {
                this.showValidationAlert();
                this.getField(field).decorateError();
                return false;
            }
            return true;
        }, this);
        if (isValid) {
            this.model.doValidate(this.options.meta.panels[0].fields, _.bind(this.validationComplete, this));
        }
    },

    /**
     * Alert for validation failure.
     */
    showValidationAlert: function() {
        var message = app.lang.get('LBL_CSP_ERROR_MESSAGE', null, this.helpBlockContext);
        app.alert.show('csp_send_warning', {
            level: 'error',
            messages: message,
            autoClose: false,
        });
    },

    /**
     * Show alert if validation fails.
     */
    saveErrorHandler: function() {
        this.showValidationAlert();
    },

    /**
     * On a successful save a message will be shown indicating that the settings have been saved.
     * The page will be reloaded in order to refresh CSP settings in browser.
     *
     * @param {Object} settings The CSP settings.
     */
    saveSuccessHandler: function(settings) {
        this.updateConfig(settings);
        this.closeView();
        app.alert.show(this.settingPrefix + '-info', {
            autoClose: true,
            level: 'success',
            messages: app.lang.get(this.saveMessage, this.module),
            onAutoClose: () => window.location.reload(true)
        });
    },
    /**
     * Render the help blocks in their respective tabpanel.
     *
     * @inheritdoc
     */
    renderHelpBlock: function() {
        _.each(this.helpBlock, function(help, name) {
            var $panel = this.$('#' + name + this.cid);
            if ($panel) {
                $panel.append(help);
            }
        }, this);
    },

    /**
     * Simple initial validation. The main validation will be handled on the backend.
     *
     * @param {string} string The string to validate.
     * @return {boolean}
     */
    validateSpecificValue: function(string) {
        return new RegExp(['\'none\'|\'strict-dynamic\'|[,;"]'].join(''), 'g').test(string);
    }
})
