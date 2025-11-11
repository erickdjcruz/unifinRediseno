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
 * @class View.Views.Base.SortingDropdownView
 * @alias SUGAR.App.view.views.BaseSortingDropdownView
 * @extends View.View
 */
({
    events: {
        'change .sorting-dropdown-select select': 'changeDropdownValue',
        'click .sorting-dropdown-arrow': 'clickArrow'
    },

    /**
     * Options array of dropdown
     * Array must consist of objects with properties "name" as value of option and "label" as string of option
     */
    dropdownFields: [],

    /**
     * Current selected field of dropdown
     * It must be empty or one of "name" property of object in dropdownFields
     */
    currentField: '',

    /**
     * Current direction of sorting
     * It must be "desc" or "asc" depends on sorting state
     */
    currentDirection: 'desc',

    /**
     * Default selected option of dropdown
     * It must be empty or one of "name" property of object in dropdownFields
     */
    defaultField: '',

    /**
     * Default direction of sorting
     * It must be "desc" or "asc" depends on sorting state
     */
    defaultDirection: 'desc',

    /**
     * Setter for dropdownFields property
     * Array must consist of objects with properties "name" as value of option and "label" as string of option
     * @param {Array} fields
     */
    setDropdownFields: function(fields) {
        this.dropdownFields = fields;
    },

    /**
     * State setter for sorting dropdown
     * It sets current selected option of dropdown and direction for sorting as "desc" or "asc"
     * @param {string} field
     * @param {string} direction
     */
    setState: function(field, direction) {
        this._setCurrentField(field);
        this._setCurrentDirection(direction);
    },

    /**
     * Event for dropdown value changing
     * @param event
     */
    changeDropdownValue: function(event) {
        let field = event.val;

        this._setCurrentField(field);
        this._setArrowState();

        this.context.trigger('app:view:sorting-dropdown:changeDropdownValue');
    },

    /**
     * Event for dropdown sorting arrow clicking
     * @param event
     */
    clickArrow: function(event) {
        if (this._isCurrentFieldEmpty()) {
            return;
        }

        this._toggleCurrentDirection();
        this._setArrowState();

        this.context.trigger('app:view:sorting-dropdown:clickArrow');
    },

    /**
     * @override
     * @private
     */
    _render: function() {
        this._super('_render');

        this._setArrowState();

        this._setSelect2();
    },

    /**
     * Enable select2.
     */
    _setSelect2: function() {
        this.$('.sorting-dropdown-select select').val(this.currentField).select2();
    },

    /**
     * Sets current selected value of dropdown
     * @param {string} field
     * @private
     */
    _setCurrentField: function(field) {
        this.currentField = field;
    },

    /**
     * Sets current direction of sorting
     * @param {string} direction
     * @private
     */
    _setCurrentDirection: function(direction) {
        this.currentDirection = direction;
    },

    /**
     * Checks is selected value of dropdown empty
     * @return {boolean}
     * @private
     */
    _isCurrentFieldEmpty: function() {
        return _.isEmpty(this.currentField);
    },

    /**
     * Enables arrow of sorting dropdown
     * @private
     */
    _enableArrow: function() {
        this.$('.sorting-dropdown-arrow').removeClass('disabled');
    },

    /**
     * Disables arrow of sorting dropdown
     * @private
     */
    _disableArrow: function() {
        this.$('.sorting-dropdown-arrow').addClass('disabled');
    },

    /**
     * Toggles current direction of sorting between "desc" and "asc"
     * @private
     */
    _toggleCurrentDirection: function() {
        let direction = this.currentDirection === 'desc' ? 'asc' : 'desc';

        this._setCurrentDirection(direction);
    },

    /**
     * Sets arrow state as desc sorting
     * @private
     */
    _setArrowDesc: function() {
        this.$('.sorting-dropdown-arrow > i').switchClass('sicon-arrow-up', 'sicon-arrow-down');
    },

    /**
     * Sets arrow state as asc sorting
     * @private
     */
    _setArrowAsc: function() {
        this.$('.sorting-dropdown-arrow > i').switchClass('sicon-arrow-down', 'sicon-arrow-up');
    },

    /**
     * Sets arrow state based on current direction of sorting
     * @private
     */
    _setArrowState: function() {
        if (this._isCurrentFieldEmpty()) {
            this._setCurrentDirection(this.defaultDirection);
            this._disableArrow();
        } else {
            this._enableArrow();
        }

        if (this.currentDirection === 'asc') {
            this._setArrowAsc();
        }

        if (this.currentDirection === 'desc') {
            this._setArrowDesc();
        }
    },
})
