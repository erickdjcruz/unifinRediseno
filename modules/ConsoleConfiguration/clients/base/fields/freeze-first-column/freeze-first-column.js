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
 * @class View.Fields.Base.ConsoleConfiguration.FreezeFirstColumnField
 * @alias SUGAR.App.view.fields.BaseConsoleConfigurationFreezeFirstColumnField
 * @extends View.Fields.Base.BoolField
 */
({
    extendsFrom: 'BoolField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.setupField();
    },

    /**
     * Set the field value on load to be checked/unchecked based on the saved config
     */
    setupField: function() {
        let moduleName = this.model.get('enabled_module');
        let consoleId = this.context.get('consoleId');
        let freezeFirstColumn = this.context.get('model') ? this.context.get('model').get('freeze_first_column') : {};
        let setValue = !_.isEmpty(freezeFirstColumn) && !_.isUndefined(freezeFirstColumn[consoleId]) &&
            !_.isUndefined(freezeFirstColumn[consoleId][moduleName]) ? freezeFirstColumn[consoleId][moduleName] : true;
        this.model.set(this.name, setValue);
    }
})
