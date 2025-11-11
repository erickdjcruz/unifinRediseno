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
 * @class View.Views.Base.MetricsRecordlistView
 * @alias SUGAR.App.view.views.BaseMetricsRecordlistView
 * @extends View.Views.Base.RecordlistView
 */
({
    extendsFrom: 'RecordlistView',

    /**
     * @inheritdoc
     */
    getDeleteMessages: function(model) {
        var messages = this._super('getDeleteMessages', [model]);
        messages.confirmation = messages.confirmation + '<br> ' + app.lang.get('LBL_METRIC_DELETE_WARNING');
        return messages;
    }
})
