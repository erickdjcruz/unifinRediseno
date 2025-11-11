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
 * Pagination view showing prev, next and page numbers.
 * Its extracted from View.Views.Base.Opportunities.ProductQuickPicksView to make it reusable.
 *
 * @class View.Views.Base.PaginationView
 * @alias SUGAR.App.view.views.BasePaginationView
 * @extends View.View
 */
({
    events: {
        'click [data-action=page-clicked]': 'getPageNumClicked',
        'click [data-action=page-nav-clicked]': 'onPageNavClicked'
    },

    /**
     * Pagination items.
     * @property {Object[]}
     */
    pageNumList: [],

    /**
     * Clicked page number.
     * @property {number}
     */
    pageNumClicked: 1,

    /**
     * Total pages.
     * @property {number}
     */
    paginationLength: 0,

    /**
     * Is 'prev' enabled?
     * @property {boolean}
     */
    isPrevDisabled: false,

    /**
     * Is 'next' enabled?
     * @property {boolean}
     */
    isNextDisabled: false,

    /**
     * Is pagination enabled?
     * @property {boolean}
     */
    isPageNumDisabled: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._init();
        this.hide();
        this.context.on('data:fetched', this.show, this);
        this.context.on('data:fetching', this.hide, this);
    },

    /**
     * Initializes pagination.
     *
     * @private
     */
    _init: function() {
        this.pageNumClicked = 1;
        this.paginationLength = 0;
        this.pageNumList = [];
        this.isPrevDisabled = false;
        this.isNextDisabled = false;
        this.isPageNumDisabled = false;
    },

    /**
     * Hides pagination.
     */
    hide: function() {
        this.$el.hide();
    },

    /**
     * Shows pagination based on data to display.
     *
     * @param {Object} data The data to display
     */
    show: function(data) {
        // reset global variables
        this._init();

        if (data.records.length === 0) {
            this.hide();
            return;
        }
        this.$el.show();

        var tmpLeftEllipsesObject = {};
        var tmpRightEllipsesObject = {};

        var currentIndex = 0;
        var startIndex = 0;

        // if some data is returned
        if (data.totalPages > 0) {
            this.paginationLength = data.totalPages;
            this.pageNumClicked = data.currentPage;

            if (this.pageNumClicked === this.paginationLength || this.paginationLength === 1) {
                this.isNextDisabled = true;
            }
            if (this.pageNumClicked === 1 || this.paginationLength === 1) {
                this.isPrevDisabled = true;
            }

            this.isPageNumDisabled = this.pageNumClicked === 1 && this.paginationLength === 1 ? true : false;

            tmpLeftEllipsesObject = {
                isIcon: true,
                listClass: 'pagination-li',
                subListClass: 'left-ellipsis-icon sicon sicon-ellipsis'
            };

            tmpRightEllipsesObject = {
                isIcon: true,
                listClass: 'pagination-li',
                subListClass: 'right-ellipsis-icon sicon sicon-ellipsis'
            };

            // push details for each list item in the pagination
            for (var page = 0; page < data.totalPages; page++) {
                this.pageNumList.push({
                    isIcon: false,
                    listClass: 'pagination-li',
                    subListClass: 'paginate-num-button btn btn-link btn-invisible',
                    pageNum: page + 1,
                    isActive: this.pageNumClicked === page + 1 && !this.isPageNumDisabled ? true : false
                });
            }

            // if more than 4 pages then display just 3 pages with ellipsis
            if (data.totalPages > 4) {
                currentIndex = this.pageNumList.indexOf(this.getCurrentObj());

                if (currentIndex > 0) {
                    startIndex = currentIndex < this.pageNumList.length - 1 ?
                        currentIndex - 1 : this.pageNumList.length - 3;
                } else {
                    startIndex = 0;
                }

                // get just three objects with active item in the center
                this.pageNumList = this.pageNumList.slice(startIndex, startIndex + 3);
                if (startIndex !== 0) {
                    this.pageNumList.unshift(tmpLeftEllipsesObject);
                }
                if (data.totalPages - currentIndex >= 3) {
                    this.pageNumList.push(tmpRightEllipsesObject);
                }
            }
        }

        this.render();
    },

    /**
     * Gets the current pagination number <li> object
     * to be used while showing just three pages with current
     * page at center, in case total pages are more than 4
     *
     * @return {Object} The current pagination number <li> object
     */
    getCurrentObj: function() {
        return _.find(this.pageNumList, function(tmpObj) {
            return tmpObj.pageNum === this.pageNumClicked;
        }, this);
    },

    /**
     * Gets the page Number clicked.
     *
     * @param {Object} evt The click event
     */
    getPageNumClicked: function(evt) {
        evt.preventDefault();
        // To stop the click event as it's not needed any more and may cause the contentsearch-dropdown to be hidden
        // due to race condition, depending on whether the "active" attribute is added by the "show" function here,
        // or the event being handled by contentsearch-dropdown's show()
        evt.stopPropagation();
        var pageId = this.$(evt.target).data('page-id');
        if (this.pageNumClicked === pageId) {
            return;
        }
        this.pageNumClicked = pageId;
        this.context.trigger('page:clicked', {pageNum: this.pageNumClicked});
    },

    /**
     * Event handler for navigantion button click events.
     *
     * @param {Object} evt The click event
     */
    onPageNavClicked: function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        var $el = this.$(evt.target);
        var currentPageNum = $el.data('page-id');
        if ($el.hasClass('previous-fav') || $el.hasClass('nav-previous')) {
            this.pageNumClicked = currentPageNum - 1;
        } else if ($el.hasClass('next-fav') || $el.hasClass('nav-next')) {
            this.pageNumClicked = currentPageNum + 1;
        }
        this.context.trigger('page:clicked', {pageNum: this.pageNumClicked});
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('data:fetching', null, this);
            this.context.off('data:fetched', null, this);
        }
    }
})
