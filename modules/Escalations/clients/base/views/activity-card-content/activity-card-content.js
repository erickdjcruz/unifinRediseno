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
 * @class View.Views.Base.Escalations.ActivityCardContentView
 * @alias SUGAR.App.view.views.BaseEscalationsActivityCardContentView
 * @extends View.Views.Base.ActivityCardContentView
 */
({
    extendsFrom: 'ActivityCardContentView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.formatDescriptionField();
    },

    /**
     * Formats the description field to account for line breaks
     */
    formatDescriptionField: function() {
        if (this.activity) {
            var description = this.activity.get('description');
            this.descriptionField = this.formatContent(description);
        }
    }
})
