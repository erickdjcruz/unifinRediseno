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
 * @class View.Fields.Base.Users.AvailableModulesField
 * @alias SUGAR.App.view.fields.BaseUsersAvailableModulesField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    events: {
        'click .sicon-remove': '_removeClicked'
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.model, 'change:number_pinned_modules', this._updateDivider);
        this.listenTo(this.model, `change:${this.name}`, this.render);
    },

    /**
     * @inheritdoc
     */
    format: function(value) {
        return {
            display: value && value.display || [],
            hide: value && value.hide || []
        };
    },

    /**
     * @inheritdoc
     */
    unformat: function() {
        let newValue = app.utils.deepCopy(this.model.get(this.name)) || {};

        let lists = this.$el.find('.sortable-list');
        _.each(lists, function(list) {
            let $list = $(list);
            let listName = $list.data('name');
            newValue[listName] = [];

            let items = $list.find('li:not(.ui-sortable-helper)');
            _.each(items, function(item) {
                let $item = $(item);
                let moduleName = $item.data('name');
                newValue[listName].push(moduleName);
            }, this);
        }, this);

        return newValue;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._updateDivider();
        if (this.action === 'edit') {
            this._initSortable();
        }
    },

    /**
     * Initializes the sortability of the item lists
     *
     * @private
     */
    _initSortable: function() {
        this.$('.sortable-list').sortable({
            connectWith: '.sortable-list',
            appendTo: 'body',
            classes: {
                'ui-sortable-helper': 'list-none border-none'
            },
            change: () => {
                this._updateDivider();
            },
            update: () => {
                this._updateDivider();
            },
            stop: (event, ui) => {
                this._updateRemoveButtonVisibility(ui.item);
                this._updateDivider();
                this._updateModel();
            }
        });
    },

    /**
     * Toggles the visiblity of a list item's remove button based on the list
     * it is contained in
     *
     * @param {jQuery} $item the jQuery object representing the list item
     * @private
     */
    _updateRemoveButtonVisibility: function($item) {
        $item.find('i.sicon-remove').toggleClass('hide', $item.closest('ul').data('name') === 'hide');
    },

    /**
     * Updates which item in the list shows a bottom border to mark where
     * the pinned items end
     *
     * @private
     */
    _updateDivider: function() {
        let borderClass = 'border-b-2';

        this.$el.find(`li.${borderClass}`).removeClass(borderClass);

        let numberPinned = this.model.get('number_pinned_modules');
        if (_.isNumber(numberPinned)) {
            let displayList = this._getListElementByName('display');
            if (displayList) {
                $(displayList).find(`li:not(.ui-sortable-helper):nth-child(${numberPinned})`).addClass(borderClass);
            }
        }
    },

    /**
     * Gets the DOM element for one of the lists by its name
     *
     * @param {string} listName the name of the list
     * @return {Element|undefined} the list element, or undefined it does not exist
     * @private
     */
    _getListElementByName: function(listName) {
        let lists = this.$el.find('.sortable-list');
        return _.find(lists, function(list) {
            let $list = $(list);
            return $list.data('name') === listName;
        });
    },

    /**
     * Updates the model's stored value based on the current state of the field
     *
     * @private
     */
    _updateModel: function() {
        this.model.set(this.name, this.unformat());
    },

    /**
     * Handles when the remove button is clicked on a list item
     *
     * @param event the Javascript click event
     * @private
     */
    _removeClicked: function(event) {
        // Get the list item that was clicked
        let $item = $(event.target).closest('li');

        // Move the list item to the hide column
        let targetList = this._getListElementByName('hide');
        if (targetList) {
            $item.detach().appendTo($(targetList));
            this._updateRemoveButtonVisibility($item);
            this._updateDivider();
            this._updateModel();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    }
})
