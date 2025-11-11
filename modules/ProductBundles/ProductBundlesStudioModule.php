<?php

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

class ProductBundlesStudioModule extends StudioModule
{
    public function __construct($module)
    {
        parent::__construct($module);
    }

    public function getModule()
    {
        $nodes = parent::getModule();

        unset($nodes[translate('LBL_LABELS')]);
        unset($nodes[translate('LBL_RELATIONSHIPS')]);
        unset($nodes[translate('LBL_LAYOUTS')]);
        unset($nodes[translate('LBL_SUBPANELS')]);
        unset($nodes[translate('LBL_WIRELESSLAYOUTS')]);

        return $nodes;
    }
}
