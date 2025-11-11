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

use ApiHelper;
use BeanFactory;
use ServiceBase;
use SugarApiExceptionInvalidParameter;
use SugarApiExceptionNotFound;
use Sugarcrm\Sugarcrm\Filters\ApiSerializable;

/**
 * Formats or unformats a $from, $to, $cc, or $bcc filter.
 */
final class EmailParticipants implements ApiSerializable
{
    /**
     * The filter definition.
     *
     * @var array
     */
    private $filter;

    /**
     * The link used by the operand. The link is used as the value for `_link` for
     * each email participant in the filter definition.
     *
     * @var string
     */
    private $link;

    /**
     * Constructor.
     *
     * @param string $operand The operand: $from, $to, $cc, or $bcc.
     * @param array $filter The filter definition.
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    public function __construct(string $operand, array $filter)
    {
        $this->filter = $filter;

        $links = [
            '$from' => 'from',
            '$to' => 'to',
            '$cc' => 'cc',
            '$bcc' => 'bcc',
        ];

        if (!array_key_exists($operand, $links)) {
            throw new SugarApiExceptionInvalidParameter(
                "Did not recognize the operand: {$operand}"
            );
        }

        $this->link = $links[$operand];
    }

    /**
     * Returns the filter definition after expanding it to include the names and
     * email addresses of each email participant.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return array
     * @throws SugarApiExceptionNotFound If the parent record or email address can't
     * be found.
     */
    public function apiSerialize(ServiceBase $api)
    {
        $options = [
            'display_acl' => true,
            'args' => [
                'erased_fields' => true,
            ],
        ];

        return array_map(
            function ($value) use ($api, $options) {
                $notFoundMsg = 'Could not find record: %s in module: %s';

                // Make certain that the link attribute is set.
                // Sidecar clients, in particular, need it.
                // This follows the convention set forth by `RelateCollectionApi`.
                $value['_link'] = $this->link;

                // Load the parent record.
                if (isset($value['parent_type']) && isset($value['parent_id'])) {
                    $bean = $value['parent_id'] === '$current_user_id' ?
                        $GLOBALS['current_user'] :
                        BeanFactory::retrieveBean(
                            $value['parent_type'],
                            $value['parent_id'],
                            ['erased_fields' => true]
                        );

                    if (!$bean) {
                        throw new SugarApiExceptionNotFound(
                            sprintf(
                                $notFoundMsg,
                                $value['parent_id'],
                                $value['parent_type']
                            )
                        );
                    }

                    // Format the parent record for API clients.
                    $helper = ApiHelper::getHelper($api, $bean);
                    $data = $helper->formatForApi($bean, ['id', 'name'], $options);

                    // Set the parent record data for the response, including the
                    // name, ACL's, and erased fields.
                    $value['parent_name'] = $data['name'];
                    $value['parent'] = $data;
                    $value['parent']['type'] = $bean->getModuleName();

                    // When the $current_user_id macro is used, always refer to the
                    // record by the macro instead of the user's real ID. This allows
                    // the filter to be shared by more than one user without
                    // unintentionally changing the definition on a save.
                    $value['parent']['id'] = $value['parent_id'];
                }

                // Load the email address.
                if (isset($value['email_address_id'])) {
                    $bean = BeanFactory::retrieveBean(
                        'EmailAddresses',
                        $value['email_address_id'],
                        ['erased_fields' => true]
                    );

                    if (!$bean) {
                        throw new SugarApiExceptionNotFound(
                            sprintf(
                                $notFoundMsg,
                                $value['email_address_id'],
                                'EmailAddresses'
                            )
                        );
                    }

                    // Format the email address for API clients.
                    $helper = ApiHelper::getHelper($api, $bean);
                    $data = $helper->formatForApi(
                        $bean,
                        ['id', 'email_address'],
                        $options
                    );

                    // Set the email address data for the response, including the
                    // email address, ACL's, and erased fields.
                    $value['email_address'] = $data['email_address'];
                    $value['email_addresses'] = $data;
                }

                return $value;
            },
            $this->filter
        );
    }

    /**
     * Returns the filter definition after removing the names and email addresses of
     * each email participant.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return array
     */
    public function apiUnserialize(ServiceBase $api)
    {
        return array_map(
            function ($value) {
                return array_filter(
                    $value,
                    function ($key) {
                        $fieldsToKeep = [
                            'parent_type',
                            'parent_id',
                            'email_address_id',
                        ];
                        return in_array($key, $fieldsToKeep);
                    },
                    ARRAY_FILTER_USE_KEY
                );
            },
            $this->filter
        );
    }
}
