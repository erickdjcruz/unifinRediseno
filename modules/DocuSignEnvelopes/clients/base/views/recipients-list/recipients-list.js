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
 * @class View.Views.Base.DocuSignEnvelopes.RecipientsListView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesRecipientsListView
 * @extends View.Views.Base.MultiSelectionListView
 */
({
    extendsFrom: 'MultiSelectionListView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        const panelIdx = _.findIndex(options.meta.panels, function(panel) {
            return panel.name === 'recipients-list-panel';
        });
        if (options.context.get('templateDetails')) {
            options.meta.panels[panelIdx].fields = _.filter(options.meta.panels[panelIdx].fields, function(field) {
                return field.name !== 'type';
            });
        } else {
            options.meta.panels[panelIdx].fields = _.filter(options.meta.panels[panelIdx].fields, function(field) {
                return field.name !== 'role';
            });
        }
        this._super('initialize', [options]);

        this.rightColumns = [];

        this._initProperties();
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this._fieldsInEditMode = [];
    },

    /**
     * Checks the `[data-check=one]` element when the row is clicked.
     *
     * @param {Event} event The `click` event.
     */
    triggerCheck: function(event) {
        const editableFieldTypes = ['base', 'enum', 'docusign-recipient-role'];
        const parentTd = event.target.closest('td');
        if (!parentTd) {
            return;
        }
        const parentTdType = parentTd.dataset.type;
        if (!editableFieldTypes.includes(parentTdType)) {
            //revert everything to detail
            _.each(this._fieldsInEditMode, function(field) {
                field.setMode('list');
            });
            return;
        }

        const fieldUUID = this.$(parentTd).find('span').attr('sfuuid');

        if (this.fields[fieldUUID].action === 'list') {
            this.fields[fieldUUID].setMode('edit');
            this._fieldsInEditMode.push(this.fields[fieldUUID]);
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this.createShortcutSession();
        this.registerShortcuts();
    },

    /**
     * Create new shortcut session.
     */
    createShortcutSession: function() {
        app.shortcuts.saveSession();
        app.shortcuts.createSession([
            'Recipients:Inline:Cancel'
        ], this);
    },

    /**
     * Register shortcuts
     */
    registerShortcuts: function() {
        app.shortcuts.register({
            id: 'Recipients:Inline:Cancel',
            keys: ['esc'],
            component: this,
            description: 'LBL_SHORTCUT_EDIT_RECIPIENT_CANCEL',
            callOnFocus: true,
            handler: _.bind(this._cancelKeyPressedHandler, this)
        });
    },

    /**
     * Cancel key pressed
     *
     * @param {Event} event
     */
    _cancelKeyPressedHandler: function(event) {
        const parentTd = event.target.closest('td');
        if (!parentTd) {
            return;
        }
        const fieldUUID = this.$(parentTd).find('span').attr('sfuuid');

        if (this.fields[fieldUUID].action === 'edit') {
            this.fields[fieldUUID].setMode('list');
            this._fieldsInEditMode = _.filter(this._fieldsInEditMode, function(field) {
                return field.sfId !== fieldUUID;
            });
        }
    },

    /**
     * @inheritdoc
     */
    _validateSelection: function() {
        let selectedModels = this.context.get('mass_collection');

        let recipientWithoutRole = _.find(selectedModels.models, function(recipient) {
            return !_.isString(recipient.get('role')) && !_.isString(recipient.get('type'));
        });

        if (!_.isUndefined(recipientWithoutRole)) {
            app.DocuSign.utils._showRolesNotSetAlert();
            return;
        }

        if (selectedModels.length > this.maxSelectedRecords) {
            this._showMaxSelectedRecordsAlert();
            return;
        }

        app.drawer.close(this._getCollectionAttributes(selectedModels));
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        app.shortcuts.restoreSession();
        this._super('_dispose');
    }
});
