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
 * @class View.Fields.Base.CJScrollField
 * @alias SUGAR.App.view.fields.BaseCJScrollField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        const module = (this.context && this.context.get('parentModule')) ? this.context.get('parentModule') : '';
        let buttonNameMap = {
                'vertical_scroll_view': 'V',
                'horizontal_scroll_view': 'H',
            };
        let showHide = app.CJBaseHelper.getValueFromCache('togglestate', 'cj_presentation_mode',module,
            'dri-workflows') === buttonNameMap[this.name];
        this.$el.toggleClass('toggleButtonBg', showHide);
    }
})
