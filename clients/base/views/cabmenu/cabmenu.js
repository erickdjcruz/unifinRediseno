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
 * This view is created from the old ‘activity-card-menu’, which now extends this view to add
 * customizations specific to timeline. Since its implemented as a view, it can be added
 * to a layout's metadata file or in a view's controller dynamically, eg:
 *
 *      var cm = app.view.createView({
 *          type: 'cabmenu',
 *          context: this.context,
 *          layout: this.layout,
 *          model: this.model,
 *          cab_menu: [
 *              {
 *                  type: 'focuscab',
 *                  css_class: 'dashboard-icon',
 *                  icon: 'sicon-focus-drawer',
 *                  tooltip: 'LBL_FOCUS_DRAWER_DASHBOARD'
 *              },
 *              {
 *                  type: 'cab_actiondropdown',
 *                  buttons: [
 *                      {
 *                          type: 'unlinkcab',
 *                          icon: 'sicon-unlink',
 *                          label: 'LBL_UNLINK_BUTTON'
 *                      },
 *                  ],
 *              },
 *          ],
 *      });
 *      cm.render();
 *      this.$el.append(cm.$el);
 *
 * The menu items can be dashletaction, cab, or other button/field types.
 * @class View.Views.Base.CabmenuView
 * @alias SUGAR.App.view.views.BaseCabmenuView
 * @extends View.Views.Base.View
 */
({
    className: 'cabmenu',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.initCabMenu();
    },

    /**
     * Initialize variables for menu generation
     */
    initCabMenu: function() {
        this.cabMenu = this.getCabMeta();

        var cabDropdown = _.find(this.cabMenu, function(menuItem) {
            return menuItem.type === 'cab_actiondropdown';
        });
        let cabButtons = cabDropdown ? cabDropdown.buttons : [];
        this.cabButtons = _.filter(cabButtons, function(buttonDef) {
            let parentModule = this.context.get('module');
            let link = this.layout && this.layout.model &&
                this.layout.model.link && this.layout.model.link.name || '';
            let unlinkable = app.utils.isRequiredLink(parentModule, link) ||
                (this.layout && this.layout.model && this.layout.model.get('_is_external_link'));
            return buttonDef.type !== 'unlinkcab' || !unlinkable;
        }, this) || [];
    },

    /**
     * Return metadata
     *
     * @return {Array}
     */
    getCabMeta: function() {
        return this.options.cab_menu || [];
    }
})
