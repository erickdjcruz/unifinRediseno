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

({
    extendsFrom: 'RecordListView',
    plugins: [
        'SugarLogic',
        'ListColumnEllipsis',
        'ResizableColumns',
        'ErrorDecoration',
        'MergeDuplicates',
        'Pagination',
        'MassCollection'
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.allowFreezeFirstColumn = false;
        this.isFirstColumnFreezed = false;
    },

    /**
     * Adds the right side preview column to the recordlist view.
     * Overrides the base recordlist view to remove the left side selection column
     */
    addActions: function() {
        var meta = this.meta;
        if (meta && _.isObject(meta.rowactions)) {
            this.addRowActions();
        }
    },

    /**
     * Overrides the function defined in the base flex-list view. Since Portal
     * no longer has a footer, this function alters how the scroll helper
     * is toggled.
     * @override
     */
    _toggleScrollHelper: function() {
        // If the table is not wide enough to need a horizontal scrollbar, or
        // the user is scrolled down to the bottom of the table, hide the fixed
        // scrollbar helper
        this.$helper.toggle(true);
        if (this.$spy.get(0).scrollWidth <= this.$spy.width() ||
            this.$helper.offset().top > this.$('tbody').offset().top + this.$('tbody').height()) {
            this.$helper.toggle(false);
            return;
        }

        if (this.$helper.css('display') !== 'none') {
            this.$helper.scrollLeft(this.$spy.scrollLeft());
        }
    }
})
