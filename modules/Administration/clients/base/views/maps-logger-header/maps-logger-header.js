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
 * @class View.Views.Base.AdministrationMapsLoggerHeaderView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsLoggerHeaderView
 * @extends View.Views.Base.AdministrationConfigHeaderView
 */
 ({
    extendsFrom: 'AdministrationConfigHeaderView',

    /**
    * @inheritdoc
    */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
    * Property initialization
    *
    */
    _initProperties: function() {
        //remove the main dropdown with save button since this is a log viewer
        if (_.has(this.meta, 'buttons')) {
            this.meta.buttons = _.chain(this.meta.buttons)
                .filter(function map(button) {
                    if (!(button.name === 'main_dropdown' && button.type === 'actiondropdown')) {
                        return button;
                    }
                })
                .value();
        }
    },

    /**
    * @inheritdoc
    */
    enableButton: function(flag) {
        const saveButton = this.getField('save_button');

        if (saveButton) {
            saveButton.setDisabled(!flag);
        }
    },
});
