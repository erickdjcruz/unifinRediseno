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
 * @class View.Views.Base.MultiLineSortingDropdownView
 * @alias SUGAR.App.view.views.BaseMultiLineSortingDropdownView
 * @extends View.Views.Base.SortingDropdownView
 */
({
    extendsFrom: 'SortingDropdownView',

    /**
     * Is this primary or secondary sort?
     */
    isPrimary: true,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.isPrimary = !!options.primary;
    },

    /**
     * Sets default value of dropdown
     * @param {string} field
     */
    setDefaultField: function(field) {
        this.defaultField = field;
    },

    /**
     * @inheritdoc
     */
    _setSelect2: function() {
        if (this.isPrimary) {
            this.$('select').val(this.currentField).select2();
        } else {
            this.$('select').val(this.currentField).select2({
                allowClear: true, placeholder: app.lang.get('LBL_SEARCH_SELECT')
            });
        }
    }
})
