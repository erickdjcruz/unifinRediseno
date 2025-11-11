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
 * @class View.Views.Base.DRI_LinkExistingActivityView
 * @alias SUGAR.App.view.views.BaseDRI_LinkExistingActivityView
 * @extends View.Views.Base.MultiSelectionListView
 */
({
    extendsFrom: 'MultiSelectionListView',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.context, 'selection-list:add', this._validateSelection);
        this.massCollection = this.context.get('mass_collection');
        this.listenTo(this.massCollection, 'add', this.checkDuplicate);
        this._super('bindDataChange');
    },

    /**
     * Receives the selected item as the model param and checks for name duplicates.
     *
     * @protected
     * @param {Object} model
     * @return {boolean}
     */
    checkDuplicate: function(model, context) {
        if (!context.get('stageParent')) {
            context = this.context;
        }
        const stageParent = context.get('stageParent');
        const stageActivities = stageParent.get('activities');

        const module = model.get('_module');
        const moduleSingularLower = app.lang.getModuleName(module).toLowerCase();
        let messageContext = _.extend({
            module,
            moduleSingularLower,
            stage: stageParent.get('name'),
        }, model.attributes);

        let isNameDuplicate = stageActivities.some(function(activity) {
            return activity.name === model.get('name');
        });
        if (isNameDuplicate) {
            app.alert.show('duplicate-name', {
                level: 'error',
                messages: app.lang.get('LBL_CJ_ACTIVITY_DUPLICATE_EXISTING', module, messageContext),
                autoClose: true,
                autoCloseDelay: 7000,
            });
            return true;
        }

        const models = context.get('mass_collection');
        isNameDuplicate = models.some(function(activity) {
            return activity.get('name') === model.get('name') && activity.get('id') !== model.get('id');
        });
        if (isNameDuplicate) {
            app.alert.show('duplicate-name', {
                level: 'error',
                messages: app.lang.get('LBL_CJ_ACTIVITY_DUPLICATE_SELECTED', module, messageContext),
                autoClose: true,
                autoCloseDelay: 7000,
            });
            return true;
        }

        return false;
    },

    /**
     * Validates the list of selected items
     *
     * @protected
     * @override
     */
    _validateSelection: function() {
        const selectedItems = this.context.get('mass_collection');
        if (!selectedItems || selectedItems.length === 0) {
            return;
        }

        if (selectedItems.length > this.maxSelectedRecords) {
            this._showMaxSelectedRecordsAlert();
            return;
        }

        const checkDupe = this.checkDuplicate;
        const context = this.context;
        let isNameDuplicate = selectedItems.some(function(item) {
            return checkDupe(item, context);
        });

        if (!isNameDuplicate) {
            app.drawer.close(selectedItems.models);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening(this.context, 'selection-list:add');
        this.stopListening(this.massCollection, 'add');
        this._super('_dispose');
    },
});
