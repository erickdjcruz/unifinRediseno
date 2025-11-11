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
 * @class View.Views.elseifBase.DRI_Workflow_Templates.TemplateImportHeaderpaneView
 * @alias SUGAR.App.view.views.DRI_Workflow_TemplatesTemplateImportHeaderpaneView
 * @extends View.Views.Base.HeaderpaneView
 */
({
    extendsFrom: 'HeaderpaneView',

    events: {
        'click [name=project_finish_button]': 'initiateFinish',
        'click [name=project_cancel_button]': 'initiateCancel',
    },

    /**
     * @inheritdoc
     */
    initiateFinish: function() {
        let messages = `${ app.lang.get('LBL_PMSE_IMPORT_EXPORT_WARNING') }<br/><br/>`;
        messages += app.lang.get('LBL_PMSE_IMPORT_CONFIRMATION');

        if (app.cache.get('show_template_import_warning')) {
            app.alert.show('project-import-confirmation', {
                level: 'confirmation',
                messages: messages,
                onConfirm: _.bind(function() {
                    app.cache.set('show_template_import_warning', false);
                    this.context.trigger('template:import:finish');
                }, this),
                onCancel: function() {
                    app.router.goBack();
                }
            });
        } else {
            this.context.trigger('template:import:finish');
        }
    },

    /**
     * Cancel the current view and navigate to module's listview
     */
    initiateCancel: function() {
        app.router.navigate(app.router.buildRoute(this.module), {trigger: true});
    },
});
