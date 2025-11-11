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
 * @class View.Fields.Base.StateArchivedField
 * @alias SUGAR.App.view.fields.BaseStateArchivedField
 * @extends View.Fields.Base.BaseEnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';
    },

    /**
     * @inheritdoc
     */
    loadEnumOptions: function(fetch, callback, error) {
        this._super('loadEnumOptions', [fetch, callback, error]);
        this.items = this.def.options || this.context.get(_itemsKey);

        if (!_.isUndefined(this.view) && !_.isUndefined(this.view.name) &&
            _.includes(['recordlist', 'dashablelist'], this.view.name)) {
            this.items = app.lang.getAppListStrings(this.items);

            if (this.model.get('archived') === true) {
                _.each(this.items, function(value, key) {
                    if ((key === 'cancelled' || key === 'completed')) {
                        this.items[key] = value + ' / ' + app.lang.getAppString('LBL_CJ_ARCHIVED');
                    }
                }, this);
            }
        } else {
            this.items = app.lang.getAppListStrings(this.items);
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!_.isUndefined(this.view) && !_.isUndefined(this.view.name) &&
            _.includes(['recordlist', 'dashablelist'], this.view.name)) {
            this.items = app.lang.getAppListStrings(this.items);

            if (this.model.get('archived') === true) {
                this.items = false;
            }
        }
        this._super('_render');
    }
})
