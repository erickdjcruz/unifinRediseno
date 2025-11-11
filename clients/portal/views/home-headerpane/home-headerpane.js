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
 * @class View.Views.Portal.HomeHeaderpaneView
 * @alias SUGAR.App.view.views.PortalHomeHeaderpaneView
 * @extends View.Views.Base.HeaderpaneView
 */
({
    extendsFrom: 'HeaderpaneView',

    className: 'preview-headerbar',

    events: {
        'click [name=collapse_button]': 'collapseClicked',
        'click [name=expand_button]': 'expandClicked'
    },

    collapseClicked: function() {
        this.context.trigger('dashboard:collapse:fire', true);
    },

    expandClicked: function() {
        this.context.trigger('dashboard:collapse:fire', false);
    },
})
