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

namespace Sugarcrm\Sugarcrm\Filters\Field;

use ServiceBase;
use SugarApiExceptionInvalidParameter;
use Sugarcrm\Sugarcrm\Filters\ApiSerializable;
use Sugarcrm\Sugarcrm\Filters\Operand\EmailParticipants as EmailParticipantsOperand;

/**
 * Formats or unformats a filter for {@link \Email::$from_collection},
 * {@link \Email::$to_collection}, {@link \Email::$cc_collection}, or
 * {@link \Email::$bcc_collection}.
 */
final class EmailParticipants implements ApiSerializable
{
    /**
     * The field name: from_collection, to_collection, cc_collection, or
     * bcc_collection.
     *
     * @var string
     */
    private $field;

    /**
     * The filter definition.
     *
     * @var array
     */
    private $filter;

    /**
     * The operand to which the field maps: $from, $to, $cc, or $bcc.
     *
     * @var string
     */
    private $operand;

    /**
     * Constructor.
     *
     * @param string $field The name of the field: from_collection, to_collection,
     * cc_collection, or bcc_collection.
     * @param array $filter The filter definition.
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    public function __construct(string $field, array $filter)
    {
        if (!array_key_exists('$in', $filter)) {
            throw new SugarApiExceptionInvalidParameter(
                "{$field} requires the use of the \$in operand"
            );
        }

        $supportedOperands = ['$in'];
        $unsupportedOperands = array_diff(array_keys($filter), $supportedOperands);

        if (!empty($unsupportedOperands)) {
            throw new SugarApiExceptionInvalidParameter(
                sprintf(
                    '%s does not support these operands: %s',
                    $field,
                    implode(', ', $unsupportedOperands)
                )
            );
        }

        $this->field = $field;
        $this->filter = $filter;

        $operands = [
            'from_collection' => '$from',
            'to_collection' => '$to',
            'cc_collection' => '$cc',
            'bcc_collection' => '$bcc',
        ];

        if (!array_key_exists($field, $operands)) {
            throw new SugarApiExceptionInvalidParameter(
                "Did not recognize the field: {$field}"
            );
        }

        $this->operand = $operands[$field];
    }

    /**
     * Returns the filter definition after expanding it to include the names and
     * email addresses of each email participant.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     * @throws \SugarApiExceptionNotFound
     */
    public function apiSerialize(ServiceBase $api)
    {
        // Use a copy of the filter. We want to produce a new array.
        $filter = $this->filter;
        $operand = new EmailParticipantsOperand($this->operand, $filter['$in']);
        $filter['$in'] = $operand->apiSerialize($api);

        return $filter;
    }

    /**
     * Returns the filter definition after removing the names and email addresses of
     * each email participant.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function apiUnserialize(ServiceBase $api)
    {
        // Use a copy of the filter. We want to produce a new array.
        $filter = $this->filter;
        $operand = new EmailParticipantsOperand($this->operand, $filter['$in']);
        $filter['$in'] = $operand->apiUnserialize($api);

        return $filter;
    }
}
