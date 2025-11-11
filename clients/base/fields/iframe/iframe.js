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
 * @class View.Fields.Base.IframeField
 * @alias SUGAR.App.view.fields.BaseIframeField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     *
     * The direction for this field should always be `ltr`.
     */
    direction: 'ltr',

    /**
     * The sepcurity policy violation event handler. We keep it as a property
     * so we could remove it when disposing of this component.
     */
    spvEventHandler: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.spvEventHandler = _.bind(this.securityPolicyViolationHandler, this);
        this.helpBlockContext = {
            linkToDocumentation: app.help.getDocumentationUrl('ContentSecurityPolicy')
        };
        document.addEventListener('securitypolicyviolation', this.spvEventHandler);
    },

    /**
     * If the uri ends with a slash, remove it.
     *
     * @param {string} uri An uri address.
     * @return {string} The escaped uri.
     */
    removeLastSlashFromURI: function(uri) {
        if (uri.slice(-1) === '/') {
            return uri.substr(0, uri.length - 1);
        }
        return uri;
    },

    /**
     * In case the current iframe's uri is blocked set the error message to be displayed.
     * It triggers a render to hide the empty iframe and render the message.
     *
     * @param {Event} event The security policy violation handler.
     */
    securityPolicyViolationHandler: function(event) {
        var currentURI = this.removeLastSlashFromURI(this.value);
        var blockedURI = this.removeLastSlashFromURI(event.blockedURI);

        // Create regex that will make sure our csp message only triggers for things directly related to the embedded
        // content, and makes sure that subdirectories will trigger the message
        var re = new RegExp('^(' + blockedURI + ')(\\\.*)*');

        if (re.test(currentURI)) {
            // If securitypolicyviolation event triggers and we have a URI match, render our CSP message
            this.cspMessage = this.getCSPMessage();
            this.render();
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        if (this.tplName === 'disabled') {
            this.$(this.fieldTag).attr('disabled', 'disabled');
        }
    },

    /**
     * It will look up a content security message.
     *
     * @return {string} The content security message.
     */
    getCSPMessage: function() {
        if (app.user.get('type').toLowerCase() === 'admin') {
            return app.lang.get('LBL_CSP_ERROR_MESSAGE_ADMIN', null, this.helpBlockContext);
        } else {
            return app.lang.get('LBL_CSP_ERROR_MESSAGE_USER', null, this.helpBlockContext);
        }
    },

    /**
     * Check if a field has a value.
     * Custom iFrame fields can have a default value defined from Studio, which also counts as a value.
     *
     * @return {boolean} True if the field is empty.
     */
    isFieldEmpty: function() {
        return this._super('isFieldEmpty') && !this.def.default;
    },

    /**
     * @inheritdoc
     */
    unformat: function(value) {
        value = (value !== '' && value != 'http://') ? value.trim() : '';
        return value;
    },

    /**
     * @inheritdoc
     *
     * Formatter for the iframe field. If the iframe field definition is
     * configured with a generated url (`this.def.gen`) by another field, those
     * field values (defined in curly braces) are parsed from the model and set
     * on the value to be returned. Finally, if the value doesn't contain
     * 'http://' or 'https://', it is prepended on the value before being
     * returned.
     *
     * @param {String} value The value set on the iframe field.
     * @return {String} The formatted iframe value.
     */
    format: function(value) {
        if (_.isEmpty(value)) {
            // Name conflict with iframe's default value def and the list view's
            // default column flag
            value = _.isString(this.def['default']) ? this.def['default'] : undefined;
        }

        if (this.def.gen == '1') {
            var regex = /{(.+?)}/,
                result = null;
            do {
                result = regex.exec(value);
                if (result) {
                    value = value.replace(result[0], this.model.get(result[1]));
                }
            } while (result);
        }

        if (_.isString(value) && !value.match(/^(http|https):\/\//)) {
            value = 'http://' + value.trim();
        }
        return value;
    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        this._super('dispose');
        document.removeEventListener('securitypolicyviolation', this.spvEventHandler);
    }
})
