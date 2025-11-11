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
 * Widget Configuration view with save/cancel functionalities
 * @class App.view.views.Base.DriWorkflowsWidgetConfigurationView
 * @alias SUGAR.App.view.views.BaseDriWorkflowsWidgetConfigurationView
 * @extends App.view.views.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * Save button name
     *
     * @property
     */
    saveButtonName: 'widget_save_button',

    /**
     * Cancel button name
     *
     * @property
     */
    cancelButtonName: 'widget_cancel_button',

    /**
     * Fields name that are displyed on the view
     *
     * @property
     */
    actualViewFields: [
        'cj_active_or_archive_filter',
        'cj_presentation_mode',
    ],

    /**
     * Fields name that mapped for toggle
     *
     * @property
     */
    toggleViewFields: [
        'cj_active_or_archive_filter',
        'cj_presentation_mode',
    ],

    /**
     * Add button click event
     *
     * @inheritdoc
     */
    delegateButtonEvents: function() {
        this.listenTo(this.context, `button:${this.saveButtonName}:click`, this.save);
        this.listenTo(this.context, `button:${this.cancelButtonName}:click`, this.cancel);
    },

    /**
     * Save the data and close drawer
     */
    save: function() {
        _.each(this.fields, function(field) {
            if (
                _.contains(this.actualViewFields, field.name) &&
                _.contains(this.toggleViewFields, field.name) &&
                _.isFunction(field.setToggleFieldStateInCache)
            ) {
                field.setToggleFieldStateInCache();
            }
        }, this);

        app.alert.show('save-success', {
            level: 'success',
            messages: app.lang.get('LBL_SAVED'),
            autoClose: true,
        });

        if (this.closestComponent('drawer')) {
            app.drawer.close(this.layout.context.get('parentLayout') ||
                this.context.parent.get('parentLayout'), 'widget-config-saved');
        } else {
            app.navigate(this.context, this.model);
        }
    },

    /**
     * Handle click on the cancel link
     * Close the drawer and dismiss the alerts
     */
    cancel: function() {
        this.$el.off();
        if (app.drawer.count()) {
            app.drawer.close(this.context);
            this._dismissAllAlerts();
        } else {
            app.router.navigate(this.module, {trigger: true});
        }
    },

    /**
     * Called when current record is being saved to allow
     * customization of options and params during save
     *
     * Override to return set of custom options
     *
     * @param {Object} options The current set of options that is going to be used.
     */
    getCustomSaveOptions: function(options) {
        return {};
    },
});
