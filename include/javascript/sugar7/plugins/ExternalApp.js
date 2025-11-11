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
(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('ExternalApp', ['view'], {

            onAttach: function(component, plugin) {
                this.on('init', function() {
                    this.addExternalAppFieldsToContext();
                }, this);
            },

            /**
             * Adds the external app field to the context so it can be loaded
             * through the Sugar APIs
             */
            addExternalAppFieldsToContext: function() {
                var fieldsToAdd = [];
                if (this.meta && this.meta.panels) {
                    _.each(this.meta.panels, function(panel) {
                        var fields = panel.fields;
                        _.each(fields, function(field) {
                            if (field.type === 'external-app-field' && _.isArray(field.loadField)) {
                                fieldsToAdd = fieldsToAdd.concat(field.loadField);
                            }
                        });
                    });
                }
                this.context.addFields(fieldsToAdd);
            }
        });
    });
})(SUGAR.App);
