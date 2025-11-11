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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Exception;

/**
 * General error, no specific cause known
 */
class CustomerJourneyException extends \SugarApiException
{
    /**
     * @var integer
     */
    public $httpCode = 450;

    /**
     * @var string
     */
    public $errorLabel = 'cj_exception';

    /**
     * @var string
     */
    public $messageLabel = 'LBL_NOT_AVAILABLE';

    /**
     * @var string
     */
    public $moduleName = '';

    /**
     * @inheritdoc
     */
    public function __construct(
        $messageLabel = null,
        $msgArgs = null,
        $moduleName = null,
        $httpCode = 0,
        $errorLabel = null
    ) {

        if (is_array($msgArgs) && !empty($msgArgs['moduleName'])) {
            $this->moduleName = $msgArgs['moduleName'];
            $module_strings = return_module_language($GLOBALS['current_language'], $msgArgs['moduleName']);
            $msgArgs['moduleName'] = $module_strings['LBL_MODULE_TITLE'];
        }

        parent::__construct($messageLabel, $msgArgs, $moduleName, $httpCode, $errorLabel);
    }

    /**
     * Return the module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
