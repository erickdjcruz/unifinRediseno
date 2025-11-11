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
 * @class View.Views.Base.ArchiveRunsSubpanelListView
 * @alias SUGAR.App.view.views.BaseArchiveRunsSubpanelListView
 * @extends View.Views.Base.SubpanelListView
 */
({
    extendsFrom: 'SubpanelListView',

    /**
     * @inheritdoc
     *
     * Overrides the parent bindDataChange to make sure this view is re-rendered
     * when the config is reset
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        var component = this.closestComponent('main-pane');
        if (component) {
            component.on('subpanel_refresh', function() {
                this.refreshCollection();
            }, this);
        }
    },
})
