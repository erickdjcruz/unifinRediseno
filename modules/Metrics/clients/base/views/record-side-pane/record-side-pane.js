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
 * @class View.Views.Base.Metrics.RecordSidePaneView
 * @alias SUGAR.App.view.views.BaseMetricsRecordSidePaneView
 * @extends View.Views.Base.ConfigPanelView
 */
({
    extendsFrom: 'BaseConfigPanelView',

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');
        let paneGroup = $('.record-side-pane-group');
        let ariaControls = this.context.get('ariaControls');

        paneGroup.toggle(this.context.get('action') === 'edit' && ariaControls === 'list_layout');
    }
})
