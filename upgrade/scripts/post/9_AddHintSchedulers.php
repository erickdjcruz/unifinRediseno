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

use Sugarcrm\Sugarcrm\Hint\HintSetup;

/**
 * Create default Calendar configurations
 */
class SugarUpgradeAddHintSchedulers extends UpgradeScript
{
    public $order = 9351;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->log('creating schedulers ...');

        if (version_compare($this->from_version, '12.0.0', '<')) {
            $hintSetup = new HintSetup();
            $hintSetup->run('post_execute');
        }
        $this->log('Done creating schedulers');
    }
}
