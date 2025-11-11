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

class IntegrateApi extends SugarApi
{
    protected $utils;
    protected $defaultField = 'sync_key';

    public function registerApiRest()
    {
        return [
            'getBySyncKey' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'sync_key', '?'],
                'pathVars' => ['module', '', 'sync_key_field_value'],
                'method' => 'getByField',
                'shortHelp' => 'Retrieve record with given sync_key.',
            ],
            'getByField' => [
                'reqType' => 'GET',
                'path' => ['integrate', '<module>', '?', '?'],
                'pathVars' => ['', 'module', 'sync_key_field_name', 'sync_key_field_value'],
                'method' => 'getByField',
                'shortHelp' => 'Retrieve record with given field.',
            ],
            'deleteBySyncKey' => [
                'reqType' => 'DELETE',
                'path' => ['<module>', 'sync_key', '?'],
                'pathVars' => ['module', '', 'sync_key_field_value'],
                'method' => 'deleteByField',
                'shortHelp' => 'Delete record with given sync_key.',
            ],
            'deleteByField' => [
                'reqType' => 'DELETE',
                'path' => ['integrate', '<module>', '?', '?'],
                'pathVars' => ['', 'module', 'sync_key_field_name', 'sync_key_field_value'],
                'method' => 'deleteByField',
                'shortHelp' => 'Delete record with given field.',
            ],
            'upsertBySyncKey' => [
                'reqType' => ['PATCH', 'PUT'],
                'path' => ['<module>', 'sync_key', '?'],
                'pathVars' => ['module', '', 'sync_key_field_value'],
                'method' => 'upsertByField',
                'shortHelp' => 'Upsert based on sync_key. If the record can be found with sync_key, then update.
                    If the record does not exist, then create it. Recommended use of PATCH HTTP verb.',
            ],
            'upsertByField' => [
                'reqType' => ['PATCH', 'PUT'],
                'path' => ['integrate', '<module>', '?', '?'],
                'pathVars' => ['', 'module', 'sync_key_field_name', 'sync_key_field_value'],
                'method' => 'upsertByField',
                'shortHelp' => 'Upsert based on a field. If the record can be found with the provided field, then update.
                    If the record does not exist, then create it. Recommended use of PATCH HTTP verb.',
            ],
            'setSyncKey' => [
                'reqType' => ['PATCH', 'PUT'],
                'path' => ['integrate', '<module>', '?', '?', '?'],
                'pathVars' => ['', 'module', 'record_id', 'sync_key_field_name', 'sync_key_field_value'],
                'method' => 'setSyncKey',
                'shortHelp' => 'Set synchronization key for a provided module and a record_id',
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

    public function getByField(ServiceBase $api, array $args): array
    {
        if (empty($args['sync_key_field_name'])) {
            $args['sync_key_field_name'] = $this->defaultField;
        }
        $this->requireArgs($args, ['module', 'sync_key_field_name', 'sync_key_field_value']);
        $this->utils()->integrationChecks($api);
        $result = $this->utils()->getRecordId(
            $args['module'],
            $args['sync_key_field_name'],
            $args['sync_key_field_value'],
            false
        );
        unset($args['sync_key_field_name']);
        unset($args['sync_key_field_value']);
        $args['record'] = $result['id'];
        $moduleApi = $this->utils()->getModuleApi($api, $args, 'GET');
        $response = $moduleApi->retrieveRecord($api, $args);
        $this->utils()->revertIntegrationChecks();
        return $response;
    }

    public function deleteByField(ServiceBase $api, array $args): array
    {
        if (empty($args['sync_key_field_name'])) {
            $args['sync_key_field_name'] = $this->defaultField;
        }
        $this->requireArgs($args, ['module', 'sync_key_field_name', 'sync_key_field_value']);
        $this->utils()->integrationChecks($api);
        $result = $this->utils()->getRecordId(
            $args['module'],
            $args['sync_key_field_name'],
            $args['sync_key_field_value'],
            false
        );
        unset($args['sync_key_field_name']);
        unset($args['sync_key_field_value']);
        $args['record'] = $result['id'];
        $moduleApi = $this->utils()->getModuleApi($api, $args, 'DELETE');
        $response = $moduleApi->deleteRecord($api, $args);
        $this->utils()->revertIntegrationChecks();
        return $response;
    }

    /**
     * If record can be found with sync_key_field_name and sync_key_field_value, then update.
     * If record does not exist, then create it.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function upsertByField(ServiceBase $api, array $args): array
    {
        if (empty($args['sync_key_field_name'])) {
            $args['sync_key_field_name'] = $this->defaultField;
        }
        $this->requireArgs($args, ['module', 'sync_key_field_name', 'sync_key_field_value']);
        $this->utils()->integrationChecks($api);

        // retrieve without the possibility of returning an exception all deleted and non-deleted records
        $existingRecord = $this->utils()->getRecordIdByField(
            $args['module'],
            $args['sync_key_field_name'],
            $args['sync_key_field_value'],
            true
        );

        // set default field returned by the parents api to be only the id
        $args['fields'] = 'id';
        $fieldname = $args['sync_key_field_name'];
        $fieldvalue = $args['sync_key_field_value'];
        unset($args['sync_key_field_name']);
        unset($args['sync_key_field_value']);

        if (empty($existingRecord) || (!empty($existingRecord['id']) && $existingRecord['deleted'] == true)) {
            if (!empty($existingRecord['id']) && $existingRecord['deleted'] == true) {
                // remove the found key from the deleted record
                $this->utils()->getUpsertLogger()->debug('Detected duplicate deleted record for module ' . $args['module'] . ' with ' .
                    $fieldname . ' ' . $fieldvalue . ' and id ' . $existingRecord['id'] . '. Unsetting ' . $fieldname);
                $this->utils()->removeField($args['module'], $existingRecord['id'], $fieldname);
            }

            // set the initial value for the integration key field, this would intentionally overwrite the same field in the body
            $args[$fieldname] = $fieldvalue;

            $this->utils()->getUpsertLogger()->debug('Creating new record for module ' . $args['module'] . ' with ' .
                $fieldname . ' ' . $fieldvalue);

            try {
                $moduleApi = $this->utils()->getModuleApi($api, $args, 'POST');
                $response = ['record' => ($moduleApi->createBean($api, $args))->id];
            } catch (Exception $e) {
                $this->utils()->getUpsertLogger()->error($e->getMessage());
                throw new \SugarApiExceptionInvalidParameter($e->getMessage());
            }

            if (!empty($response)) {
                // set http status to 201
                $api->setResponse($api->getResponse()->setStatus(201));
            }
        } else {
            $args['record'] = $existingRecord['id'];

            $this->utils()->getUpsertLogger()->debug('Updating existing record with id ' . $existingRecord['id'] . ' for module ' .
                $args['module'] . ' with ' . $fieldname . ' ' . $fieldvalue);

            // log field discrepancies between body and url, the integration sync field value has been intentionally changed
            if (!empty($args[$fieldname]) && $args[$fieldname] !== $fieldvalue) {
                $this->utils()->getUpsertLogger()->debug('Updating existing record integration id for record id ' .
                    $existingRecord['id'] . ' for module ' . $args['module'] . ' with integration sync field ' .
                    $fieldname . ' from value ' . $fieldvalue . ' to value ' . $args[$fieldname]);

                // double check that the new key is not already in use before setting it
                $newKey = $this->utils()->getRecordIdByField($args['module'], $fieldname, $args[$fieldname], true);
                if (!empty($newKey) && !empty($newKey['id'])) {
                    if ($newKey['deleted'] == true) {
                        // remove the found new key from the deleted record
                        $this->utils()->getUpsertLogger()->debug('Detected duplicate deleted record for module ' . $args['module'] .
                            ' with ' . $fieldname . ' ' . $fieldvalue . ' and id ' . $newKey['id'] . '. Unsetting ' . $fieldname);
                        $this->utils()->removeField($args['module'], $newKey['id'], $fieldname);
                    } else {
                        $this->utils()->getUpsertLogger()->error(
                            'Detected error while updating existing integration id for record id ' .
                            $existingRecord['id'] . ' for module ' . $args['module'] . ' with integration sync field ' . $fieldname .
                            ' from value ' . $fieldname . ' to value ' . $args[$fieldname] . '. A database entry with unique integration key ' .
                            $args[$fieldname] . ' already exists with id ' . $newKey['id']
                        );
                        throw new \SugarApiExceptionInvalidParameter(
                            'LBL_INTEGRATE_DUPLICATE_PARAM',
                            [
                                $newKey['id'],
                                $fieldname,
                                $args[$fieldname],
                                $args['module'],
                            ]
                        );
                    }
                }
            }

            $moduleApi = $this->utils()->getModuleApi($api, $args, 'PUT');
            $response = ['record' => ($moduleApi->updateRecord($api, $args))['id']];
        }

        $this->utils()->revertIntegrationChecks();
        return $response;
    }

    public function setSyncKey(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['module', 'record_id', 'sync_key_field_name', 'sync_key_field_value']);
        $this->utils()->integrationChecks($api);

        $moduleName = $args['module'];
        $bean = BeanFactory::newBean($args['module']);
        $recordId = $args['record_id'];
        $syncKeyField = $args['sync_key_field_name'];

        if (empty($bean->field_defs[$syncKeyField]['is_sync_key'])) {
            $this->utils()->getUpsertLogger()->error('Invalid module ' . $moduleName . ' or field ' . $syncKeyField);
            throw new \SugarApiExceptionInvalidParameter('LBL_INTEGRATE_INVALID_FIELD', [$syncKeyField, $moduleName]);
        }

        $value = $args['sync_key_field_value'];
        $result = false;

        try {
            $result = $bean->db->updateParams(
                $bean->getTableName(),
                $bean->getFieldDefinitions(),
                [$syncKeyField => $value],
                ['id' => $recordId]
            );
        } catch (Exception $e) {
            $this->utils()->getUpsertLogger()->error(
                'Cannot set sync key for module ' . $moduleName . ': ' . $e->getMessage()
            );
        }

        return ['success' => $result];
    }
}
