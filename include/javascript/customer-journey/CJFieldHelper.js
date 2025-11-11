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
(function(app) {
    /**
     * CJField helper.
     * These functions are to be used on fields for certain operations
     *
     * Usage:
     *   app.CJFieldHelper.<func_name>()
     *
     * Example:
     *   app.CJFieldHelper._hideField(field, c)
     */
    app.augment('CJFieldHelper', {

        /**
         * It will hide the current field by making it readonly and also hide the label with the callback
         *
         * @param {Object} field
         * @param callback function()
         *   Called after hiding the field and it's label in order to reset variables and data.
         * @return {undefined}
         */
        _hideField: function(field, callback) {
            if (!field) {
                return;
            }

            field.def = field.def || {};
            field.def.readonly = true;
            field._hide();
            this._hideFieldLabel(field);

            if (_.isFunction(callback)) {
                callback();
            }
        },

        /**
         * It will hide the current field label
         *
         * @param {Object} field
         * @return {undefined}
         */
        _hideFieldLabel: function(field) {
            if (!field) {
                return;
            }

            let firstParent = field.$el.parents(`div[data-name="${field.name}"]`).eq(0);
            if (firstParent.length >= 1) {
                firstParent.find('.record-label').removeClass('ellipsis_inline').addClass('hide').hide();
            }
        },

        /**
         * It will show the current field label
         *
         * @param {Object} field
         * @return {undefined}
         */
        _showFieldLabel: function(field) {
            if (!field) {
                return;
            }

            let firstParent = field.$el.parents(`div[data-name="${field.name}"]`).eq(0);
            if (firstParent.length >= 1) {
                firstParent.find('.record-label').removeClass('hide').addClass('ellipsis_inline').show();
            }
        },

        /**
         * It will show the current field by making it readonly and also show the label
         *
         * @param {Object} field
         * @return {undefined}
         */
        _showField: function(field) {
            if (!field) {
                return;
            }

            field.def = field.def || {};
            field.def.readonly = false;
            field._show();

            let action = 'detail';
            if (
                field.view &&
                (field.view.createMode || _.isEqual(field.view.currentState, 'create') ||
                _.isEqual(field.view.currentState, 'edit'))
            ) {
                action = 'edit';
            }
            field.setMode(action);
            this._showFieldLabel(field);
        },

        /**
         * It will enable of disable the field according to the flag
         *
         * @param {Object} field
         * @param {boolean} enableOrDisable
         */
        _enableOrDisableField: function(field, enableOrDisable) {
            if (field) {
                field.readonly = enableOrDisable;
                field.setDisabled(enableOrDisable);
            }
        },
    }, true);
})(SUGAR.App);
