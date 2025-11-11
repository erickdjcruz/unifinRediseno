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
 * @class View.Fields.Base.RecordCountField
 * @alias SUGAR.App.view.fields.BaseRecordCountField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * Module name.
     *
     * @property {string}
     */
    module: '',

    /**
     * Filter definition.
     *
     * @property {Object}
     */
    filter: {},

    /**
     * CSS class.
     *
     * @property {string}
     */
    cssClass: '',

    /**
     * Total amount of filtered records.
     *
     * @property {number}
     */
    count: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.module = options.def.module || '';
        this.filter = options.def.filter || {};
        this.cssClass = options.def.cssClass || '';
        this.tooltipLabel = options.def.tooltip || '';
        this.autoRefresh(true);
    },

    /**
     * Auto refresh the badges every 5 min
     *
     * @param {boolean} start `true` to start the timer, `false` to stop it
     */
    autoRefresh: function(start) {
        if (start) {
            clearInterval(this._timerId);
            this._timerId = setInterval(_.bind(function() {
                this._getCount(true);
            }, this), 5 * 1000 * 60); // 5 min default
        } else {
            clearInterval(this._timerId);
        }
    },

    /**
     * Get the total amount of filtered records and display it.
     *
     * @param {boolean} forceReload `true` to ignore cache and force a new request
     * @private
     */
    _getCount: function(forceReload) {
        if (!this.module) {
            return;
        }
        var params = this.filter ? {filter: this.filter} : {};
        var url = app.api.buildURL(this.module, 'count', {}, params);
        // if cached
        var recordCounts = this.context.get('recordCounts');
        if (!forceReload && recordCounts && !_.isUndefined(recordCounts[url])) {
            this.count = recordCounts[url];
            this.render();
            return;
        }
        app.api.call('read', url, null, {
            success: _.bind(function(data) {
                this.count = data.record_count;
                if (this.context) {
                    // cache it
                    var recordCounts = this.context.get('recordCounts') || {};
                    recordCounts[url] = this.count;
                    this.context.set('recordCounts', recordCounts);
                }
                if (!this.disposed) {
                    this.render();
                }
            }, this),
        });
    },

    /**
     * @inheritdoc
     */
    render: function() {
        if (_.isNull(this.count)) {
            this._getCount();
            return;
        } else if (this.count > 0) {
            this._super('render');
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.autoRefresh(false);
        this._super('_dispose');
    }
})
