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
 * @class View.Views.Base.AdministrationAwsConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationAwsConfigView
 * @extends View.Views.Base.AdministrationConfigView
 */
({
    extendsFrom: 'AdministrationConfigView',

    /**
     * Label of the help text of login url
     */
    endPointHelpLabel: 'LBL_AWS_LOGIN_URL_HELP_TEXT',

    defaultIdentityProvider: 'Connect',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.meta.firstNonHeaderPanelIndex = 0; // there is no header, so it's always 0
    },

    /**
     * @inheritdoc
     */
    loadSettingsSuccessCallback: function(settings) {
        this._super('loadSettingsSuccessCallback', [settings]);

        this._bindEvents();
    },

    /**
     * Attach events to fields
     * @inheritdoc
     */
    _bindEvents: function() {
        var self = this;
        var nameField = this.getField('aws_connect_instance_name');
        var regionField = this.getField('aws_connect_region');

        var setRegionRequired = _.bind(function() {
            var input = nameField.$('input');
            var required = input.val() ? !!input.val().trim() : !!input.val();

            if (regionField.def.required !== required) {
                var metaRegionField = _.findWhere(this.options.meta.panels[0].fields, {'name': regionField.name});
                regionField.def.required = metaRegionField.required = required;
                regionField._render();
            }
        }, this);

        nameField.$el.on('input', function() {
            var isEmptyName = !$(this).find('input').val().length;

            setRegionRequired();

            if (isEmptyName) {
                self.model.set('aws_connect_region', '');
            }

            regionField.setDisabled(isEmptyName);
        });

        setRegionRequired();

        this.model.on(
            'change:aws_connect_identity_provider',
            this._toggleEndpointField,
            this
        );
        this._toggleEndpointField();

        this.model.on(
            'change:aws_connect_enable_portal_chat',
            this._toggleChatSettings,
            this
        );
        this._toggleChatSettings();
    },

    /**
     * On a successful save the Save button has to be disabled and
     * a message will be shown indicating that the settings have been saved.
     *
     * @param {Object} settings The aws connect settings.
     */
    saveSuccessHandler: function(settings) {
        this.updatePendoMetadata(settings);
        this._super('saveSuccessHandler', [settings]);
    },

    /**
     * Show an error message if the settings could not be saved.
     */
    saveErrorHandler: function() {
        app.alert.show(this.settingPrefix + '-warning', {
            level: 'error',
            title: app.lang.get('LBL_ERROR'),
            messages: app.lang.get('LBL_AWS_SAVING_ERROR', this.module),
        });
    },

    /**
     * Hide/Show api gateway and contact flow id fields depending on whether the chat is enabled. If
     * the fields are shown, they is required. If the field is hidden, we remove the
     * required metadata to avoid an error during save.
     *
     * @private
     */
    _toggleChatSettings: function() {
        var enableChatField = this.getField('aws_connect_enable_portal_chat');
        var required = !!enableChatField.getFormattedValue();
        var fields = [
            'aws_connect_api_gateway_url',
            'aws_connect_contact_flow_id',
            'aws_connect_instance_id'
        ];
        _.each(fields, function(fieldName) {
            var field = this.getField(fieldName);
            var metaAField = _.findWhere(this.options.meta.panels[1].fields, {'name': field.name});
            field.def.required = metaAField.required = required;
            field.render();
            this._toggleFieldVisibility(field, required);
        }, this);

        if (enableChatField.$el) {
            enableChatField.$el.closest('.tab-pane').find('.admin-config-help-block').toggle(required);
        }
    },

    /**
     * Hide/Show endpoint url field depending on identity provider value. If
     * the field is shown, it is required. If the field is hidden, we remove the
     * required metadata to avoid an error during save.
     *
     * @private
     */
    _toggleEndpointField: function() {
        // Get our fields and field metadata
        var identityField = this.getField('aws_connect_identity_provider');
        var endpointField = this.getField('aws_login_url');
        var metaEndpointField = _.findWhere(this.options.meta.panels[0].fields, {'name': endpointField.name});

        // Set the `required` attribute accordingly
        var required = identityField.getFormattedValue() !== this.defaultIdentityProvider;
        endpointField.def.required = metaEndpointField.required = required;
        endpointField.render();

        // Show/Hide the help text of login url
        var endpointHelp = this.$el.find('li.' + this.endPointHelpLabel);
        endpointHelp.toggle(required);

        // Hide or show the field
        this._toggleFieldVisibility(endpointField, required);
    },

    /**
     * Get fields to validate
     * @return {Object}
     */
    getFieldsToValidate: function() {
        return _.union(
            this.options.meta.panels[0].fields,
            this.options.meta.panels[1].fields
        );
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
     * Update pendo metadata when saving new connect settings
     *
     * @param settings
     */
    updatePendoMetadata: function(settings) {
        settings = _.pick(settings, ['aws_connect_url', 'aws_connect_instance_name']);
        app.utils.updatePendoMetadata({}, settings);
    }
})
