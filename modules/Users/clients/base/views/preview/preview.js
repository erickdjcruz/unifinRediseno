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
 * @class View.Views.Base.Users.PreviewView
 * @alias SUGAR.App.view.views.BaseUsersPreviewView
 * @extends View.Views.Base.PreviewView
 */
({
    extendsFrom: 'PreviewView',

    /**
     * Flag to check if we should navigate to Reassign User Records page
     * {boolean}
     */
    triggerReassignUserRecords: false,

    /**
     * Extend the parent function to add editability checking for IDM
     *
     * @param {Array} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.originalMeta = this.meta;

        _.each(this.meta.panels, function(panel) {
            app.utils.setUsersEditableFields(panel.fields, 'record');
        });

        // Always fetch is_group and portal_only so we can determine if we need
        // to show their special views
        let contextFields = this.context.get('fields') || [];
        contextFields.push('is_group', 'portal_only');
        this.context.set('fields', _.uniq(contextFields));
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        // Set up the special view metadata for Group and Portal API type Users
        let viewType = this.model.get('is_group') ? 'group' :
            this.model.get('portal_only') ? 'portalapi' :
                false;
        if (['group', 'portalapi'].includes(viewType)) {
            let desiredMeta = _.extend({}, this.meta,
                app.metadata.getView('Users', `record-${viewType}`));
            this.meta = this._previewifyMetadata(desiredMeta);
        } else {
            this.meta = this.originalMeta;
        }

        // Watch for special User types so that we render the correct metadata
        this.stopListening(this.model, 'change:is_group change:portal_only', this.render);
        this.listenTo(this.model, 'change:is_group change:portal_only', this.render);

        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    _previewifyMetadata: function(meta) {
        let formattedMeta = this._super('_previewifyMetadata', [meta]);

        if (formattedMeta && formattedMeta.panels) {
            formattedMeta.panels = formattedMeta.panels.filter((item) =>
                !['downloads_tab_panel', 'access_tab_user_role_panel'].includes(item.name));
        }

        return formattedMeta;
    },

    /**
     * @inheritdoc
     */
    _getNoAccessErrorMessage: function(error) {
        if (error.code === 'license_seats_needed' && _.isString(error.message)) {
            return error.message;
        }
        return this._super('_getNoAccessErrorMessage', [error]);
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.meta = this._previewifyMetadata(this.meta);

        this._super('_renderHtml');
    },

    /**
     * @inheritdoc
     *
     * Handles IDM alert messaging
     */
    handleEdit: function() {
        this._super('handleEdit');

        if (app.config.idmModeEnabled) {
            let message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_REGULAR_USER', this.module);

            // Admin users should see a link to the SugarIdentity user edit page
            if (app.user.get('type') === 'admin') {
                let link = decodeURI(this.meta.cloudConsoleEditUserLink);
                let linkTemplate = Handlebars.compile(link);
                let url = linkTemplate({
                    record: encodeURIComponent(app.utils.createUserSrn(this.model.get('id')))
                });

                message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_ADMIN_USER', this.module);
                message = message.replace('%s', url);
            }

            app.alert.show('edit-user-record', {
                level: 'info',
                autoClose: false,
                messages: app.lang.get(message)
            });
        }
    },

    /**
     * @inheritdoc
     */
    handleSave: function() {
        let changedAttributes = this.model.changedAttributes();
        if (changedAttributes && changedAttributes.status && changedAttributes.status === 'Inactive') {
            this.triggerReassignUserRecords = true;
        }
        this._super('handleSave');
    },

    /**
     * @inheritdoc
     */
    completeCallback: function() {
        this._super('completeCallback');
        if (this.triggerReassignUserRecords) {
            this.triggerReassignUserRecords = false;
            app.alert.show('reassign_records', {
                level: 'confirmation',
                messages: app.lang.get('LBL_REASS_CONFIRM_REASSIGN', this.module),
                onConfirm: _.bind(function() {
                    let url = app.bwc.buildRoute('Users', this.model.get('id'), 'reassignUserRecords');
                    app.router.navigate(url, {trigger: true});
                }, this),
                onCancel: function() {
                    return;
                },
            }, this);
        }
    }
})
