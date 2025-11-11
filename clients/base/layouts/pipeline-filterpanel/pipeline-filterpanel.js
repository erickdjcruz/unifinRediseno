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
 * @class View.Layouts.Base.PipelineFilterpanelLayout
 * @alias SUGAR.App.view.layouts.BasePipelineFilterpanelLayout
 * @extends View.Layouts.Base.FilterpanelLayout
 */
({
    extendsFrom: 'FilterpanelLayout',

    /**
     * @override
     * @private
     */
    _render: function() {
        this._initSortingDropdownData();

        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.context, 'app:view:sorting-dropdown:changeDropdownValue', this._sortSortableDataComponent);
        this.listenTo(this.context, 'app:view:sorting-dropdown:clickArrow', this._sortSortableDataComponent);
    },

    /**
     * @inheritdoc
     * @private
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    },

    /**
     * @private
     */
    _sortSortableDataComponent: function() {
        let sortingDropdownComponent = this.getComponent('sorting-dropdown');
        let sortableDataComponent = this.getComponent('pipeline-recordlist-content');

        sortableDataComponent.orderBy.field = sortingDropdownComponent.currentField;
        sortableDataComponent.orderBy.direction = sortingDropdownComponent.currentDirection;
        sortableDataComponent.sortData();
    },

    /**
     * @private
     */
    _initSortingDropdownData: function() {
        let sortingDropdownComponent = this.getComponent('sorting-dropdown');
        let sortableDataComponent = this.getComponent('pipeline-recordlist-content');

        if (!_.isUndefined(sortingDropdownComponent) && !_.isUndefined(sortableDataComponent)) {
            let sortBy = sortableDataComponent.orderBy;

            let sortableFields = sortableDataComponent.getSortableFields();

            let sortedSortableFields = _.sortBy(sortableFields, 'name');

            sortingDropdownComponent.setDropdownFields(sortedSortableFields);
            sortingDropdownComponent.setState(sortBy.field, sortBy.direction);
        }
    }
})
