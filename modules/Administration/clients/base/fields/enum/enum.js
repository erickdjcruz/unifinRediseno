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
 * @class View.Views.Base.AdministrationModuleIconField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    getSelect2Options: function(optionsKeys) {
        let select2Options = {};
        select2Options = this._super('getSelect2Options', [optionsKeys]);

        if (_.contains(['color', 'icon'], this.def.formatOptions)) {
            select2Options.formatResult = _.bind(this.formatIconsOrColors, this);
            select2Options.formatSelection = _.bind(this.formatIconsOrColors, this);
        }

        return select2Options;
    },

    /**
     * Format options to show the icon or color associated with the value
     * @param opt
     * @return {string|*|jQuery|HTMLElement}
     */
    formatIconsOrColors: function(opt) {
        if (!opt.id) {
            return opt.text.toUpperCase();
        }

        return $(
            `<span><i class="sicon ${_.escape(opt.id)} mr-1.5 rounded w-3 h-3"></i>${_.escape(opt.text)}</span>`
        );
    },
})
