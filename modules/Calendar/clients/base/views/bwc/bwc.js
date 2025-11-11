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
 * @inheritdoc
 */
({
    convertToSidecarUrl: function(href) {
        var module = this.moduleRegex.exec(href);

        module = (_.isArray(module)) ? module[1] : null;
        if (!module) {
            return '';
        }
        //Route links for BWC modules through bwc/ route
        //Remove any './' nonsense in existing hrefs
        href = href.replace(/^.*\//, '');

        return 'bwc/' + href;
    }
});
