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
 * @class View.Views.Base.ActivityCardDetailView
 * @alias SUGAR.App.view.views.BaseActivityCardDetailView
 * @extends View.Views.Base.ActivityCardView
 */
({
    extendsFrom: 'ActivityCardView',

    className: 'activity-card-detail',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.initDateDetails();
    },

    /**
     * Initializes hbs date variables with date_entered
     */
    initDateDetails: function() {
        let fieldName;
        if (this.activity) {
            const meta = this.getModulesCardMeta(this.activity.module);

            if (meta.record_date) {
                fieldName = meta.record_date;
                this.detailDateTimeTooltip = meta.date_tooltip;
                if (!this.detailDateTimeTooltip) {
                    let field = app.metadata.getField({module: this.activity.module, name: fieldName});
                    if (field) {
                        this.detailDateTimeTooltip = field.label || field.vname || 'LBL_LIST_DATE_ENTERED';
                    }
                }
            } else {
                fieldName = 'date_entered';
                this.detailDateTimeTooltip = 'LBL_LIST_DATE_ENTERED';
            }
            this.setDateDetails(this.activity.get(fieldName));
        }
    },

    /**
     * Get the activity-timeline dashlet metadata for the baseModule
     *
     * @param {string} baseModule module name
     */
    getModulesCardMeta: function(baseModule) {
        return app.metadata.getView(baseModule, 'activity-card-definition');
    },

    /**
     * Set date variables for use in the hbs template
     *
     * @param dateString the date string
     */
    setDateDetails: function(dateString) {
        if (dateString) {
            var date = app.date(dateString);

            this.detailDay = date.format('dddd');
            this.detailDateTime = this.formatDate(date);
        }
    },

    /**
     * Set date variables for use in the hbs template
     *
     * @param {Object} date the date string
     */
    formatDate: function(date) {
        return date.formatUser();
    },
})
