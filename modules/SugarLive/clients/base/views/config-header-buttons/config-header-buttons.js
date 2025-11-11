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
 * @class View.Views.Base.SugarliveConfigHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseSugarliveConfigHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    /**
     * @inheritdoc
     */
    _saveConfig: function() {
        this.getField('save_button').setDisabled(true);
        this._super('_saveConfig');
    },

    /**
     * @inheritdoc
     */
    _beforeSaveConfig: function() {
        this.model.set({
            viewdefs: this.buildSelectedList()
        }, {silent: true});
        return this._super('_beforeSaveConfig');
    },

    /**
     * @inheritdoc
     */
    cancelConfig: function() {
        if (this.triggerBefore('cancel')) {
            if (app.drawer.count()) {
                app.drawer.close(this.context, this.context.get('model'));
            }
        }
        if (app.omniConsoleConfig) {
            app.omniConsoleConfig.open();
        }
    },

    /**
     * This builds a list of selected fields for SugarLive Summary Panel Configuration
     * @return {{}}
     */
    buildSelectedList: function() {
        var selectedList = {};

        // the main ul elements of the selected list, one ul for each module
        var fieldLists = document.querySelectorAll('.drawer.active .columns .field-list');
        _.each(fieldLists, function(ul) {
            var module = ul.getAttribute('module_name');

            // init selectedList for this module
            selectedList[module] = {
                base: {
                    view: {
                        'omnichannel-detail': {
                            fields: []
                        }
                    }
                }
            };

            _.each(ul.children, function(li) {
                var field = {};
                field.name = li.getAttribute('fieldname');
                field.label = li.getAttribute('fieldlabel');

                var fieldDef = app.metadata.getField({name: field.name, module: module});
                var fieldMeta = app.metadata._patchFields(module, app.metadata.getModule(module), [fieldDef]);
                selectedList[module].base.view['omnichannel-detail'].fields.push(_.first(fieldMeta));
            });
        });

        return selectedList;
    }
});
