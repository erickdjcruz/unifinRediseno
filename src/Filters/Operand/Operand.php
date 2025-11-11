<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Filters\Operand;

use ServiceBase;
use Sugarcrm\Sugarcrm\Filters\ApiSerializable;

/**
 * Formats or unformats a filter for a standard operand.
 */
class Operand implements ApiSerializable
{
    /**
     * The filter definition.
     *
     * @var mixed Typically a string or array.
     */
    private $filter;

    /**
     * The operand name.
     *
     * @var string
     */
    private $operand;

    /**
     * Constructor.
     *
     * @param string $operand The name of the operand.
     * @param mixed $filter The scalar value of the operand or an array.
     */
    public function __construct(string $operand, $filter)
    {
        $this->operand = $operand;
        $this->filter = $filter;
    }

    /**
     * Returns the filter definition without making any changes.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return mixed
     */
    public function apiSerialize(ServiceBase $api)
    {
        return $this->filter;
    }

    /**
     * Returns the filter definition without making any changes.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return mixed
     */
    public function apiUnserialize(ServiceBase $api)
    {
        return $this->filter;
    }
}
