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
 * This component is a non-dashlet dashlet type. It may look, behave, etc.
 * as a dashlet without being coupled with a dashboard
 *
 * @class View.Layouts.Base.ContentContainerLayout
 * @alias SUGAR.App.view.layouts.BaseContentContainerLayout
 * @extends View.Layout
 */
({
    className: 'content-container rounded-md shadow hover:shadow-lg transition-shadow ' +
        'bg-[--dashlet-background] hover:bg-[--content-container-background-hover]',

    /**
     * @inheritdoc
     */
    _placeComponent: function(component) {
        this.$el.find('.content-container-items').append(component.el);
    },
})
