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
 * @class View.Fields.Base.CabField
 * @alias SUGAR.App.view.fields.BaseCabField
 * @extends View.Fields.Base.ButtonField
 */
({
    events: {
        'click [data-cab]': 'actionClicked'
    },

    extendsFrom: 'ButtonField',

    /**
     * Handle click.
     *
     * @param {Event} evt Mouse event.
     */
    actionClicked: function(evt) {
        if (this.preventClick(evt) === false) {
            return;
        }
        var action = $(evt.currentTarget).data('cab');
        this.runAction(evt, action);
    },

    /**
     * Run action to handle click event.
     *
     * @param {Event} evt Mouse event.
     * @param {string} action Action name.
     */
    runAction: function(evt, action) {
        if (!action) {
            return;
        }
        if (_.isFunction(this[action])) {
            this[action](evt, this.def.params);
        } else if (_.isFunction(this.view[action])) {
            this.view[action](evt, this.def.params);
        }
    }
})
