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
 * This component shows action items driven by metadata. A component can
 * have multiple 'action-items' components with varying items definition
 *
 * To use:
 * [
 *  'view' => [
 *      'name' => 'action-items',
 *      'items' => [
 *          [
 *              'icon' => 'the_icon',
 *              'tooltip' => 'the_tooltip',
 *              'href' => 'the_href',
 *              'label' => 'the_label',
 *               'onclick' => 'the_onclick'
 *              'target' => 'the_target'
 *          ]
 *      ]
 *  [
 * ]
 *
 * @class View.Views.Base.ActionItemsView
 * @alias SUGAR.App.view.views.BaseActionItemsView
 * @extends View.View
 */
({
    className: 'action-items',

    /**
     * A list of items
     */
    items: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.addItems(this.getItemsDef());
    },

    /**
     * Add items to be rendered
     *
     * @param items
     */
    addItems: function(items) {
        if (!this.items) {
            this.items = [];
        }

        this.items.push(...items);
    },

    /**
     * Get item definitions
     *
     * @return array
     */
    getItemsDef: function() {
        return this.meta.items || [];
    }
})
