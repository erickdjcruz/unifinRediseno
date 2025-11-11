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
 * @class View.Views.Base.DocusignRecipientsMultiSelectionListView
 * @alias SUGAR.App.view.views.BaseDocusignRecipientsMultiSelectionListView
 * @extends View.View.Base.MultiSelectionListContextView
 */
({
    extendsFrom: 'MultiSelectionListView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this._fieldsInEditMode = [];

        this.rightColumns = [];

        const mixedCollection = this.context.get('mixed_collection');
        if (!_.isUndefined(mixedCollection)) {
            const modelsOnThisModule = _.filter(mixedCollection.models, function(model) {
                return model.get('_module') === this.module;
            }, this);

            const massCollection = new app.data.createBeanCollection(this.module, modelsOnThisModule);
            this.massCollection = massCollection;
            this.context.set('mass_collection', massCollection);
        }
    },

    /**
     * @inheritdoc
     */
    triggerCheck: function(event) {
        const editableFieldTypes = ['docusign-recipient-role'];
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
     * Closes the drawer passing the selected models attributes to the callback.
     *
     * @protected
     */
    _validateSelection: function() {
        const selectedModels = this.context.get('mixed_collection');
        const modelWithoutRoleSet = _.find(selectedModels.models, function(model) {
            return _.isEmpty(model.get('role'));
        });

        if (!_.isUndefined(modelWithoutRoleSet)) {
            app.DocuSign.utils._showRolesNotSetAlert();
            return;
        }
        app.drawer.close(this._getCollectionAttributes(selectedModels));
    }
})
