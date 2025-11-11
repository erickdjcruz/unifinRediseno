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

use Sugarcrm\Sugarcrm\Integrate\IntegrateUtils;

class IntegrateRelateApi extends SugarApi
{
    protected $utils;
    protected $defaultField = 'sync_key';

    public function registerApiRest()
    {
        return [
            'relateBySyncKeys' => [
                'reqType' => 'POST',
                'path' => [
                    '<module>',
                    '?',
                    'link_by_sync_keys',
                    '?',
                    '?',
                ],
                'pathVars' => [
                    'module',
                    'lhs_sync_key_field_value',
                    '',
                    'link_name',
                    'rhs_sync_key_field_value',
                ],
                'method' => 'relateByFields',
                'shortHelp' => 'Create a relationship based on both sync_keys.
                    If both the LHS and RHS records can be found with the respective sync_key,
                    then relate the RHS record to the LHS record.',
            ],
            'relateByFields' => [
                'reqType' => 'POST',
                'path' => [
                    'integrate',
                    '<module>',
                    '?',
                    '?',
                    'link',
                    '?',
                    '?',
                    '?',
                ],
                'pathVars' => [
                    '',
                    'module',
                    'lhs_sync_key_field_name',
                    'lhs_sync_key_field_value',
                    '',
                    'link_name',
                    'rhs_sync_key_field_name',
                    'rhs_sync_key_field_value',
                ],
                'method' => 'relateByFields',
                'shortHelp' => 'Create a relationship based on fields.
                    If both the LHS and RHS records can be found with the respective fields,
                    then relate the RHS record to the LHS record.',
            ],
            'unrelateBySyncKeys' => [
                'reqType' => 'DELETE',
                'path' => [
                    '<module>',
                    '?',
                    'link_by_sync_keys',
                    '?',
                    '?',
                ],
                'pathVars' => [
                    'module',
                    'lhs_sync_key_field_value',
                    '',
                    'link_name',
                    'rhs_sync_key_field_value',
                ],
                'method' => 'unrelateByFields',
                'shortHelp' => 'Remove a relationship based on both sync_keys.
                    If both the LHS and RHS records can be found with the respective sync_key,
                    and those records are related, then remove the relationship of the RHS record to the LHS record.',
            ],
            'unrelateByFields' => [
                'reqType' => 'DELETE',
                'path' => [
                    'integrate',
                    '<module>',
                    '?',
                    '?',
                    'link',
                    '?',
                    '?',
                    '?',
                ],
                'pathVars' => [
                    '',
                    'module',
                    'lhs_sync_key_field_name',
                    'lhs_sync_key_field_value',
                    '',
                    'link_name',
                    'rhs_sync_key_field_name',
                    'rhs_sync_key_field_value',
                ],
                'method' => 'unrelateByFields',
                'shortHelp' => 'Remove a relationship based on fields.
                    If both the LHS and RHS records can be found with the respective fields,
                    and those records are related, then remove the relationship of the RHS record to the LHS record.',
            ],
        ];
    }

    protected function utils(): IntegrateUtils
    {
        if (empty($this->utils)) {
            $this->utils = new IntegrateUtils();
        }
        return $this->utils;
    }

    /**
     * Create a relationship based on both field names and values.
     * If both the LHS and RHS records can be found with the respective field names and values, then relate the RHS record to the LHS record.
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function relateByFields(ServiceBase $api, array $args): array
    {
        $updatedArgs = $this->handleRelationshipDiscoveryByFields($api, $args);
        $response = $this->relateRecords($api, $updatedArgs);
        $this->utils()->revertIntegrationChecks();
        return $response;
    }

    /**
     * Remove a relationship based on both field names and values.
     * If both the LHS and RHS records can be found with the respective field names and values, then relate the RHS record to the LHS record.
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function unrelateByFields(ServiceBase $api, array $args): array
    {
        $updatedArgs = $this->handleRelationshipDiscoveryByFields($api, $args);
        $response = $this->unrelateRecords($api, $updatedArgs);
        $this->utils()->revertIntegrationChecks();
        return $response;
    }

    protected function handleRelationshipDiscoveryByFields(ServiceBase $api, array $args): array
    {
        if (empty($args['lhs_sync_key_field_name'])) {
            $args['lhs_sync_key_field_name'] = $this->defaultField;
        }
        if (empty($args['rhs_sync_key_field_name'])) {
            $args['rhs_sync_key_field_name'] = $this->defaultField;
        }

        $this->requireArgs($args, [
            'module',
            'link_name',
            'lhs_sync_key_field_name',
            'lhs_sync_key_field_value',
            'rhs_sync_key_field_name',
            'rhs_sync_key_field_value',
        ]);

        $this->utils()->integrationChecks($api);

        // set default field returned by the parents api to be only the id
        $args['fields'] = 'id';

        $main = $this->utils()->getRecordId(
            $args['module'],
            $args['lhs_sync_key_field_name'],
            $args['lhs_sync_key_field_value']
        );
        $args['record'] = $main['id'];

        $secondary = $this->utils()->getRecordId(
            $this->getRelatedModuleName($api, $args),
            $args['rhs_sync_key_field_name'],
            $args['rhs_sync_key_field_value']
        );
        $args['remote_id'] = $secondary['id'];

        unset($args['lhs_sync_key_field_name']);
        unset($args['lhs_sync_key_field_value']);
        unset($args['rhs_sync_key_field_name']);
        unset($args['rhs_sync_key_field_value']);

        return $args;
    }

    /**
     * Relates records and return two records sugar ids
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    protected function relateRecords(ServiceBase $api, array $args): array
    {
        $this->utils()->getUpsertLogger()->debug('Relating record ' . $args['record'] . ' of module ' . $args['module'] .
            ' to record ' . $args['remote_id'] . ' with link field ' . $args['link_name']);
        $result = $this->utils()->getRelateRecordApi($api, $args, 'POST')->createRelatedLink($api, $args);
        return [
            'record' => $result['record']['id'],
            'related_record' => $result['related_record']['id'],
        ];
    }

    /**
     * Unrelates records and return two records sugar ids
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    protected function unrelateRecords(ServiceBase $api, array $args): array
    {
        $this->utils()->getUpsertLogger()->debug('Unrelating record ' . $args['record'] . ' of module ' . $args['module'] .
            ' from record ' . $args['remote_id'] . ' with link field ' . $args['link_name']);
        $result = $this->utils()->getRelateRecordApi($api, $args, 'DELETE')->deleteRelatedLink($api, $args);
        return [
            'record' => $result['record']['id'],
            'related_record' => $result['related_record']['id'],
        ];
    }

    /**
     * Retrieve the related module name based on primary module and link name from API arguments
     * @param ServiceBase $api
     * @param array $args
     * @return string
     * @throws SugarApiExceptionNotFound
     */
    public function getRelatedModuleName(ServiceBase $api, array $args): string
    {
        $primaryBean = $this->loadBean($api, $args);
        $linkName = $args['link_name'];
        if (!$primaryBean->load_relationship($linkName)) {
            $this->utils()->getUpsertLogger()->error('Could not find link field ' . $args['link_name']);
            throw new SugarApiExceptionNotFound('LBL_INTEGRATE_INVALID_RELATIONSHIP', [$args['link_name']]);
        }
        $relatedModuleName = $primaryBean->$linkName->getRelatedModuleName();
        return $relatedModuleName;
    }
}
