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

namespace Sugarcrm\Sugarcrm\Security\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @see LegacyCleanStringValidator
 *
 */
class LegacyCleanString extends Constraint
{
    // Available filters
    public const STANDARD = 'STANDARD';
    public const STANDARDSPACE = 'STANDARDSPACE';
    public const FILE = 'FILE';
    public const NUMBER = 'NUMBER';
    public const SQL_COLUMN_LIST = 'SQL_COLUMN_LIST';
    public const PATH_NO_URL = 'PATH_NO_URL';
    public const SAFED_GET = 'SAFED_GET';
    public const UNIFIED_SEARCH = 'UNIFIED_SEARCH';
    public const AUTO_INCREMENT = 'AUTO_INCREMENT';
    public const ALPHANUM = 'ALPHANUM';

    // Error codes
    public const FILTER_ERROR = 1;

    protected static $errorNames = [
        self::FILTER_ERROR => 'FILTER_ERROR',
    ];

    public $filter = self::STANDARD;
    public $message = 'LegacyCleanString violation [%filter%]';
}
