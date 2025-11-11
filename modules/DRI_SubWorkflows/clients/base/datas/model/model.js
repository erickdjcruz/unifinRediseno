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
 * @class Model.Datas.Base.DriSubWorkflowsModel
 * @alias SUGAR.App.model.datas.BaseDriSubWorkflowsModel
 * @extends Data.Bean
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        app.Bean.prototype.initialize.call(this, options);
        this.listenTo(this, 'change:sort_order', this.setLabel);
        this.listenTo(this, 'change:name', this.setLabel);
    },

    /**
     * @inheritdoc
     */
    setLabel: function() {
        let order = this.get('sort_order');

        if (order.toString().length === 1) {
            order = `0${order}`;
        }
        this.set('label', `${order}. ${this.get('name')}`);
    },
});
