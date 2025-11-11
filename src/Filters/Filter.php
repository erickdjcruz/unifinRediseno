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

namespace Sugarcrm\Sugarcrm\Filters;

use ServiceBase;
use SugarApiExceptionInvalidParameter;
use Sugarcrm\Sugarcrm\Filters\Field\EmailParticipants as EmailParticipantsField;
use Sugarcrm\Sugarcrm\Filters\Field\Field;
use Sugarcrm\Sugarcrm\Filters\Operand\EmailParticipants as EmailParticipantsOperand;
use Sugarcrm\Sugarcrm\Filters\Operand\Operand;

/**
 * Formats or unformats a complete filter definition.
 */
final class Filter implements ApiSerializable
{
    /**
     * The filter definition.
     *
     * @var array
     */
    private $filter;

    /**
     * The module in which the filter definition is used.
     *
     * @var string
     */
    private $module;

    /**
     * Constructor.
     *
     * @param string $module The module in which the filter definition is used.
     * @param array $filter A complete filter definition.
     */
    public function __construct(string $module, array $filter)
    {
        $this->module = $module;
        $this->filter = $filter;
    }

    /**
     * Walks the filter definition, formatting each segment, and returns the filter
     * definition formatted for the API client.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return array
     */
    public function apiSerialize(ServiceBase $api)
    {
        return $this->doFilters(
            $this->filter,
            function (ApiSerializable $serializer) use ($api) {
                return $serializer->apiSerialize($api);
            }
        );
    }

    /**
     * Walks the filter definition, unformatting each segment, and returns the filter
     * definition unformatted for the database.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return array
     */
    public function apiUnserialize(ServiceBase $api)
    {
        return $this->doFilters(
            $this->filter,
            function (ApiSerializable $serializer) use ($api) {
                return $serializer->apiUnserialize($api);
            }
        );
    }

    /**
     * Walks the filter definition and applies the callback to each child segment.
     *
     * @param array $filters The filter defintion to walk.
     * @param callable $fn The function to apply when a field or operand is
     * encountered.
     *
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     * @throws \SugarApiException The operand and field implementations throw
     * instances of {@link \SugarApiException} implementations.
     */
    private function doFilters(array $filters, callable $fn): array
    {
        foreach ($filters as $i => $filter) {
            if (!is_array($filter)) {
                throw new SugarApiExceptionInvalidParameter(
                    sprintf(
                        'Did not recognize the definition: %s',
                        print_r($filter, true)
                    )
                );
            }

            foreach ($filter as $operand => $value) {
                $filters[$i][$operand] = $this->doFilter($operand, $value, $fn);
            }
        }

        return $filters;
    }

    /**
     * Applies the callback to a segment of a filter definition.
     *
     * @param string $operand The operand or field name to which the filter belongs.
     * @param mixed $filter The filter definition under the operand or field name.
     * @param callable $fn The function to apply when a field or operand is
     * encountered.
     *
     * @return mixed The filter definition resulting from the application of the
     * callback.
     * @throws \SugarApiException The operand and field implementations throw
     * instances of {@link \SugarApiException} implementations.
     */
    private function doFilter(string $operand, $filter, callable $fn)
    {
        switch ($operand) {
            case '$or':
            case '$and':
                return $this->doFilters($filter, $fn);
            case '$creator':
            case '$favorite':
            case '$following':
            case '$owner':
            case '$tracker':
            case '$guest':
                return $fn(new Operand($operand, $filter));
            case '$from':
            case '$to':
            case '$cc':
            case '$bcc':
                return $fn(new EmailParticipantsOperand($operand, $filter));
            default:
                return $this->doField($operand, $filter, $fn);
        }
    }

    /**
     * Applies the callback to a field segement of a filter definition.
     *
     * @param string $field The field name.
     * @param mixed $filter The field segment of a filter definition.
     * @param callable $fn The function to apply when a field or operand is
     * encountered.
     *
     * @return mixed The filter definition resulting from the application of the
     * callback.
     * @throws SugarApiExceptionInvalidParameter
     * @throws \SugarApiException The operand and field implementations throw
     * instances of {@link \SugarApiException} implementations.
     */
    private function doField(string $field, $filter, callable $fn)
    {
        $epFields = [
            'from_collection',
            'to_collection',
            'cc_collection',
            'bcc_collection',
        ];
        $isAnEmailParticipantsField = false;

        if ($this->module === 'Emails' && in_array($field, $epFields)) {
            $isAnEmailParticipantsField = true;
        }

        if ($isAnEmailParticipantsField) {
            if (!is_array($filter)) {
                throw new SugarApiExceptionInvalidParameter(
                    "{$field} requires an array"
                );
            }

            return $fn(new EmailParticipantsField($field, $filter));
        }

        return $fn(new Field($field, $filter));
    }
}
