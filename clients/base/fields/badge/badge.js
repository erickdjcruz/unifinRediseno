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
 * @class View.Fields.Base.BadgeField
 * @alias SUGAR.App.view.fields.BaseBadgeField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * This field doesn't support `showNoData`.
     */
    showNoData: false,

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.isHidden()) {
            this.toggleCell('hide');
            this._super('hide');
        } else {
            this.toggleCell('show');
            this._super('show');
        }

        this.hideLabel();
    },

    /**
     * @override
     */
    bindDataChange: function() {
        if (!this.model) {
            return;
        }

        this.model.on('change:' + this.name, this.render, this);
    },

    /**
     * Checks if a field is hidden in the UI. Returns false if field is not hidden.
     *
     * @return {boolean}
     */
    isHidden: function() {
        var value = this.model.get(this.name);
        return _.isUndefined(value) ? true : !value;
    },

    /**
     * @inheritdoc
     */
    hide: function() {
        this.toggleCell('hide');
        // calling the super hide method to trigger the right events and
        // set properties correctly on the actual field as well
        this._super('hide');
    },

    /**
     * @inheritdoc
     */
    show: function() {
        this.toggleCell('show');
        // calling the super show method to trigger the right events and
        // set properties correctly on the actual field as well
        this._super('show');
    },

    /**
     * Return parent DOM object of the current badge
     * @return {Object} jQuery object
     */
    getParentElem: function() {
        return this.$el.closest('[data-type="badge"]') || {};
    },

    /**
     * Hide a label of the current badge if it's exist
     */
    hideLabel: function() {
        var cellEl = !_.isEmpty(this.getParentElem()) ?
            this.getParentElem().parent() :
            {};

        if (!_.isEmpty(cellEl) && cellEl.hasClass('record-cell')) {
            cellEl.find('>.field-label').hide();
        }
    },

    /**
     * Hide or show the badge cell. Badge field might be present on a different views which
     * in turn have their own visibilities. This means that depending on the parent's
     * visibility we must use the correct method for hiding/displaying the field.
     * Note: using `toggle` might result in unwanted behaviour.
     *
     * @param {string} toggleMethod The method name to use for changing element visibility.
     */
    toggleCell: function(toggleMethod) {
        var parentElem = this.getParentElem();
        if (!_.isEmpty(parentElem)) {
            if (parentElem.prop('tagName') === 'TD') {
                return;
            }

            parentElem[toggleMethod]();
            parentElem.closest('.record-cell')[toggleMethod]();
        }
    },
})
