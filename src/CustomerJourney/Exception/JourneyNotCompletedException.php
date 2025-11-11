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
 * Journey not completion Exception. Specific for DRI_Workflow
 */
class JourneyNotCompletedException extends CustomerJourneyException
{
    /**
     * @var integer
     */
    public $httpCode = 404;

    /**
     * @var string
     */
    public $errorLabel = 'not_completed';

    /**
     * @var string
     */
    public $messageLabel = 'CJ_JOURNEY_NOT_COMPLETED_EXCEPTION';
}
