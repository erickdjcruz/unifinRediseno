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
 * @class View.Layouts.Portal.HeaderHelpView
 * @alias SUGAR.App.view.views.PortalHeaderHelpView
 * @extends View.Views.Base.HeaderHelpView
 */
({
    extendsFrom: 'HeaderHelpView',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.layout) {
            this.layout.on('button:help_button:click', this._createNewCase, this);
        }
    },

    /**
     * Create a new case
     * @private
     */
    _createNewCase: function() {
        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: 'Cases'
            }
        });
    },

    /**
     * @inheritdoc
     */
    _dispose() {
        if (this.layout) {
            this.layout.off('button:help_button:click', null, this);
        }
        this._super('_dispose');
    }
})
