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
 * @class View.Layouts.Base.DashletGridWrapperLayout
 * @alias SUGAR.App.view.layouts.BaseDashletGridWrapperLayout
 * @extends View.Layout
 */
({
    extendsFrom: 'DashletLayout',

    /**
     * Remove the current attached dashlet component
     */
    removeDashlet: function() {
        this.layout.removeDashlet(this);
        this.dispose();
    },

    /**
     * @override
     *
     * The dashboard-grid component maintains dashlet state to track the
     * position/size of its children, so dashlets no longer need to find their
     * metadata within the dashboard component list.
     *
     * @param {Object} meta
     * @return {Object} unmodified metadata
     */
    setDashletMetadata: function(meta) {
        return meta;
    },

    /**
     * Notify listeners that the size has changed
     */
    manageSizeUpdated: function() {
        this.trigger('grid-panel:size:changed');
    },

    /**
     * @override
     *
     * If we get here by updating an existing dashlet, we need to update the
     * metadata on the layout. Then we unset model.updated to avoid unsaved
     * changes warnings from updating metadata.
     *
     * @param {Object} dashletDef
     */
    addDashlet: function(dashletDef) {
        if (this._components.length > 0) {
            // save the change
            this.layout.editDashlet(this, dashletDef);
        }
        this._super('addDashlet', [dashletDef]);
        this.model.unset('updated');
    },
})
