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
 * @class View.Fields.Base.CollectionCountField
 * @alias SUGAR.App.view.fields.BaseCollectionCountField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click [data-action="count"]': 'fetchCount'
    },

    /**
     * Fetches the total amount of filtered records from the collection, and
     * renders the field to show the new (or cached) total.
     */
    fetchCount: function() {
        if (_.isNull(this.collection.total)) {
            app.alert.show('fetch_count', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
                autoClose: false
            });
        }

        this.collection.trigger('list:page-total:fetching');

        this.isLoadingCount = true;
        this.updateCount();

        var successCallback = _.bind(function(total) {
            this.isLoadingCount = false;
            var options = {
                total: total
            };
            this.updateCount(options);
        }, this);
        this.collection.fetchTotal({
            success: successCallback,
            complete: function() {
                app.alert.dismiss('fetch_count');
            }
        });
    },

    /**
     * Updates {@link #countLabel the count label} and renders this field.
     *
     * @param {number} [options] Optional hash of values to use for the `length`
     *   and `hasMore` properties. Use this if you want to customize what this
     *   field should display.
     * @param {number} [options.length] The length of values.
     * @param {number} [options.total] Total number of records.
     * @param {boolean} [options.hasMore] `true` if there are more values to be
     *   fetched or paginated, `false` if we've fetched everything.
     */
    updateCount: function(options) {
        if (!this.disposed) {
            this._setCountLabel(options);

            if (!_.isUndefined(options) && !_.isUndefined(options.total)) {
                this.collection.trigger('list:page-total:fetched', options.total);
            }
            this.render();
        }
    },

    /**
     * Returns the label for the count in the headerpane.
     *
     * If you would like to customize these, the following labels are being
     * used: `TPL_LIST_HEADER_COUNT`, `TPL_LIST_HEADER_COUNT_PARTIAL`,
     * `TPL_LIST_HEADER_COUNT_TOTAL`, and `TPL_LIST_HEADER_COUNT_TOOLTIP`.
     *
     * There are several ways the total count label is represented, depending on
     * the state of `this.collection`. If the collection contains all the
     * records, the label will display `this.collection.length`, for example:
     *
     *     (17)
     *
     * If `this.collection.total` exists and is cached, the label will display
     * in the form:
     *
     *     (20 of 50)
     *
     * Otherwise, the returned label will include the link to fetch the total:
     *
     *     (20 of <a data-action="count">21+</a>)
     *
     * @protected
     * @param {number} [options] Optional hash of values to use for the `length`
     *   and `hasMore` properties. Use this if you want to customize what this
     *   field should display.
     * @param {number} [options.length] The length of values. Defaults to
     *   `this.collection.length`.
     * @param {boolean} [options.hasMore] `true` if there are more values to be
     *   fetched or paginated, `false` if we've fetched everything. Defaults to
     *   `false`.
     * @return {string|Handlebars.SafeString} The label to use for the list view
     *   count.
     */
    _setCountLabel: function(options) {
        // Default properties.
        options = options || {};

        if (_.isUndefined(options.hasMore) && this.collection.next_offset) {
            options.hasMore = this.collection.next_offset >= 0;
        }

        const recordsNum = this.getRecordsNum(options);

        if (!recordsNum.current) {
            return this.countLabel = '';
        }

        var tplKey = 'TPL_LIST_HEADER_COUNT_TOTAL';
        var context = {
            num: recordsNum.current,
            total: this.cachedCount,
        };

        if (recordsNum.current === recordsNum.total) {
            tplKey = 'TPL_LIST_HEADER_COUNT';
        } else if (!_.isNull(this.collection.total)) {
            // Save the total on the field - this is the primary save point.
            this.cachedCount = this.collection.total;
            // Since we have a total we display it through the context.
            context.total = this.collection.total;
        } else if (this.isLoadingCount) {
            context.total =  app.lang.get('LBL_LOADING');
        } else if (!this.cachedCount) {
            // Initial load case - we did not have a total for the current collection before.
            var tooltipLabel = app.lang.get('TPL_LIST_HEADER_COUNT_TOOLTIP', this.module);
            // FIXME: When SC-3681 is ready, we will no longer have the need for
            // this link, since the total will be displayed by default.
            context.total = new Handlebars.SafeString(
                `<a href="javascript:void(0);" data-action="count" rel="tooltip" data-bs-placement="right"
title="${tooltipLabel}" role="button" tabindex="0" aria-label="${tooltipLabel}">
                ${Handlebars.Utils.escapeExpression(
                    app.lang.get('TPL_LIST_HEADER_COUNT_PARTIAL', this.module, {num: recordsNum.total})
                )}</a>`
            );
        }

        // FIXME: When SC-3681 is ready, remove the SafeString call.
        return this.countLabel = new Handlebars.SafeString(app.lang.get(tplKey, this.module, context));
    },

    /**
     * Prepare current and total count of pages
     * @param {Object} options
     * @return {Object}
     */
    getRecordsNum: function(options) {
        const collect = this.collection;
        const limit = collect.getOption('limit') || app.config.maxQueryResult || 0;
        const length = options.length || collect.length;
        const start = ((collect.page || 1) - 1) * limit + 1;

        return {
            current: this.getRecordsNumCurrent(start, length, options),
            total: this.getRecordsNumTotal(start, length, options),
        };
    },

    /**
     * Get current numeration of records
     * @param {int} start
     * @param {int} length
     * @param {Object} options
     * @return {int}
     */
    getRecordsNumCurrent: function(start, length, options) {
        let isNumRange = options.hasMore || this.collection.page > 1;
        let layout = this.context.get('layout');

        // if it is not the list layout, check if it is using list-pagination
        if (layout !== 'records') {
            let isUsingListPagination = this.context.get('isUsingListPagination') || this.checkListPagination();
            // use range format only if list-pagination is used
            isNumRange = isNumRange && isUsingListPagination;
        }

        if (isNumRange) {
            return start + '-'  + (start + length - 1);
        } else {
            return length;
        }
    },

    /**
     * Checks if the layout is using the list-pagination component
     *
     * @return {boolean}
     */
    checkListPagination: function() {
        if (_.isEmpty(this.view) || _.isEmpty(this.view.layout)) {
            return false;
        }

        let paginationComponent = this.view.layout.getComponent('list-pagination') || {};

        return !_.isEmpty(paginationComponent);
    },

    /**
     * Get total number of records
     * @param {int} start
     * @param {int} length
     * @param {Object} options
     * @return {int}
     */
    getRecordsNumTotal: function(start, length, options) {
        const collect = this.collection;

        if (collect.total) {
            return collect.total;
        } else if (options.hasMore) {
            return start + length;
        } else {
            collect.total = start + length - 1;
            return collect.total;
        }
    },

    /**
     * @override
     *
     * Re-renders the field when the attached collection is `reset`. Also
     * handles executing a request for the total count when a `pagination` event
     * occurs on the context. We do this on `pagination` because it is a
     * user-initiated action - if we request the count on `reset` as well it
     * would decrease performance.
     */
    bindDataChange: function() {
        if (!this.collection) {
            return;
        }

        this.listenTo(this.collection, 'remove', this.updateCount);

        this.listenTo(this.collection, 'reset', function() {
            if (!this.disposed && this.cachedCount) {
                // check if collection is reset because of record creation
                let isCreate = !_.isUndefined(this.context.get('isCreate')) ? this.context.get('isCreate') : false;
                if (this.context.get('isUsingListPagination') &&
                    this.context.get('paginationAction') === 'PAGINATE' &&
                    !isCreate && !this.context.get('isLink')) {
                    return;
                }

                this.fetchCollectionTotal();
            } else {
                this.updateCount();
            }
        });

        this.listenTo(this.context, 'paginate', function() {
            if (!this.disposed) {
                this.fetchCount();
            }
        });

        this.listenTo(this.context, 'refresh:count', function(hasAmount, properties) {
            this.updateCount(properties);
        });

        this.listenTo(this.context, 'list:paginate', this.updateCount);

        this.listenTo(app.events, 'list:create:success', function() {
            this.context.set('isCreate', true);
        }, this);
    },

    fetchCollectionTotal: function() {
        var successFn = _.bind(function(total) {
            // Update the cached total on reset action.
            this.cachedCount = total;
            this.updateCount({
                total: total,
            });
            this.context.unset('isCreate');
            this.context.unset('isLink');
        }, this);
        this.collection.fetchTotal({success: successFn});
    }
})
