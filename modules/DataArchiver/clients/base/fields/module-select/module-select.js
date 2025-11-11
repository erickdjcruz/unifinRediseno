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
 * @class View.Fields.Base.DataArchiver.ModuleSelectField
 * @alias SUGAR.App.view.fields.BaseDataArchiverModuleSelectField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * Trigger the archiver:module:change when the user affects the module-select field
     */
    bindDomChange: function() {
        this.$el.on('change', _.bind(function() {
            this.model.trigger('archiver:module:change', this.model.get(this.name));
        }, this));
    }
})
