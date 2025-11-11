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
 * @class View.Layouts.Base.SidebarHelpLayout
 * @alias SUGAR.App.view.layouts.BaseSidebarHelpLayout
 * @extends View.Layout
 */
({
    events: {
        'click .resource': 'onClickResourseLink',
        'click .helplet-body a': 'onClickResourseLink',
    },

    /**
     * {@inheritDoc}
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        /**
         * Each view requires its own help object.
         * On view change, the helpObject needs to be recreated.
         */
        app.events.on('app:view:change', this._contentUpdate, this);
    },

    /**
     * Dispose previous components and initialize new components
     * @private
     */
    _contentUpdate: function() {
        this._disposeComponents();
        this.initComponents();
        this._render();
    },

    /**
     * Close flyout after click on the links on the Help layout
     */
    onClickResourseLink: function() {
        this.layout.close();
    },
})
