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
 * @class View.Views.Base.DataArchiver.CreateView
 * @alias SUGAR.App.view.views.DataArchiverCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    moduleRequirements: {
        'pmse_Inbox': [
            'cas_status',
        ],

    },

    /**
     * @override
     */
    save() {
        let canSave = true;
        const filterModule = this.model.get('filter_module_name');
        if (this.model.get('filter_def') && filterModule in this.moduleRequirements) {
            const filters = JSON.parse(this.model.get('filter_def')).map(f => Object.keys(f)[0]);
            const reqsNotMet = this.moduleRequirements[filterModule].filter(f => !filters.includes(f));
            if (reqsNotMet.length > 0) {
                // If there are many reqs not met, just show the first in error me
                this.showModuleRequirementsError(reqsNotMet[0], filterModule);
                canSave = false;
            }
        }
        canSave && this._super('save');
    },

    /**
     * Show the error message that will occur when module requirements are not met. Will only show the first of any
     * requirements not met
     */
    showModuleRequirementsError(field, module) {
        app.alert.dismissAll();
        const fieldName = app.lang.get(app.metadata.getModule(module).fields[field].vname, module) || field;
        const args = {fieldName: fieldName, moduleName: module};
        app.alert.show('req_not_met', {
            level: 'error',
            messages: app.lang.get('TPL_PMSE_INBOX_ERROR_MESSAGE', this.module, args),
            autoClose: true,
            autoCloseDelay: 5000
        });
    }
})
