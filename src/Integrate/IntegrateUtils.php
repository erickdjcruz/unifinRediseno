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

namespace Sugarcrm\Sugarcrm\Integrate;

use Activity;
use LoggerManager;
use SugarApiExceptionNotAuthorized;
use Sugarcrm\Sugarcrm\Logger\Factory;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use TrackerManager;

class IntegrateUtils
{
    /**
     * Ensure current user has admin permissions
     * @throws SugarApiExceptionNotAuthorized
     */
    public function ensureAdminUser(): void
    {
        if (empty($GLOBALS['current_user']) || !$GLOBALS['current_user']->isAdmin()) {
            self::getUpsertLogger()->error('Attempt to call the integrate api as a non-admin user for user ' .
                $GLOBALS['current_user']->id . ' with username ' . $GLOBALS['current_user']->user_name);
            throw new SugarApiExceptionNotAuthorized('EXCEPTION_NOT_AUTHORIZED');
        }
    }

    /**
     * Perform integration checks
     */
    public function integrationChecks(\ServiceBase $api): void
    {
        $this->ensureAdminUser();

        // set fts async
        $fts = SearchEngine::getInstance();
        $fts->setForceAsyncIndex(true);

        // disable activity tracking
        Activity::disable();

        // disable tracker
        TrackerManager::getInstance()->pause();
    }

    /**
     * Reverts integration checks
     */
    public function revertIntegrationChecks(): void
    {
        // set it back to the default value from the config.
        $fts = SearchEngine::getInstance();
        $fts->setForceAsyncIndex(
            \SugarConfig::getInstance()->get('search_engine.force_async_index', false)
        );

        // restore activity tracking
        Activity::restoreToPreviousState();
    }

    /**
     * Loads the API implementation for the given module
     */
    protected function loadApiImplementation(\ServiceBase $api, string $apiClass, array $args, string $action)
    {
        if (empty($args['module']) || empty($action)) {
            return null;
        }

        if ($apiClass === 'RelateRecordApi' && empty($args['link_name'])) {
            return null;
        }

        $module = $args['module'];
        $link = empty($args['link_name']) ? '' : $args['link_name'];

        $mapping = [
            'ModuleApi' => [
                'GET' => [
                    'route' => [
                        $module,
                        '123456',
                    ],
                ],
                'DELETE' => [
                    'route' => [
                        $module,
                        '123456',
                    ],
                ],
                'PUT' => [
                    'route' => [
                        $module,
                        '123456',
                    ],
                ],
                'POST' => [
                    'route' => [
                        $module,
                    ],
                ],
            ],
            'RelateRecordApi' => [
                'DELETE' => [
                    'route' => [
                        $module,
                        '123456',
                        'link',
                        $link,
                        '789101',
                    ],
                ],
                'POST' => [
                    'route' => [
                        $module,
                        '123456',
                        'link',
                        $link,
                        '789101',
                    ],
                ],
            ],
        ];

        $dict = new \ServiceDictionaryRest();
        $dict->loadDictionary();
        $route = $dict->lookupRoute(
            $mapping[$apiClass][$action]['route'],
            $api->getVersion(),
            $action,
            'base'
        );

        if (file_exists($route['file'])) {
            $this->getUpsertLogger()->debug('Attempting to leverage api implementation ' . $action . ' ' .
                $apiClass . ' from file ' . $route['file'] . ' and class ' . $route['className']);
            require_once $route['file'];
            return new $route['className']();
        }

        return null;
    }

    public function getModuleApi(\ServiceBase $api, array $args, string $action)
    {
        $mod = $this->getApi($api, 'ModuleApi', $args, $action);
        return (is_null($mod) ? new \ModuleApi() : $mod);
    }

    public function getRelateRecordApi(\ServiceBase $api, array $args, string $action)
    {
        $rel = $this->getApi($api, 'RelateRecordApi', $args, $action);
        return (is_null($rel) ? new \RelateRecordApi() : $rel);
    }

    /**
     * Returns the API implementation for the given module
     */
    protected function getApi(\ServiceBase $api, string $apiClass, array $args, string $action)
    {
        $res = $this->loadApiImplementation($api, $apiClass, $args, $action);
        if ($res instanceof $apiClass) {
            return $res;
        }
        return null;
    }

    /**
     * Returns record id based on the provided field
     * It does not throw exceptions in case of record not found
     */
    public function getRecordIdByField(string $moduleName, string $fieldName, string $fieldValue, bool $getDeleted = true): array
    {
        $this->getUpsertLogger()->debug('Retrieving record id for field ' . $fieldName . ' with value ' .
            $fieldValue . ' and module ' . $moduleName);

        $bean = \BeanFactory::newBean($moduleName);

        if (empty($bean->field_defs[$fieldName])) {
            $this->getUpsertLogger()->error('Field ' . $fieldName . ' does not exist for module ' . $moduleName);
            throw new \SugarApiExceptionInvalidParameter('LBL_INTEGRATE_INVALID_FIELD', [$fieldName, $moduleName]);
        }

        if (empty($bean->field_defs[$fieldName]['is_sync_key'])) {
            $this->getUpsertLogger()->error(
                'Field ' . $fieldName . ' is not marked as "sync_key" for module ' . $moduleName
            );
            throw new \SugarApiExceptionInvalidParameter('LBL_INTEGRATE_INVALID_FIELD', [$fieldName, $moduleName]);
        }

        $options = [];
        if ($getDeleted) {
            $this->getUpsertLogger()->debug('Retrieving also deleted records for field ' . $fieldName . ' with value ' .
                $fieldValue . ' and module ' . $moduleName);
            $options['add_deleted'] = false;
        }

        $query = new \SugarQuery();
        $query->from($bean, $options);
        $query->select(['id', 'deleted']);
        $query->where()->equals($fieldName, $fieldValue);
        $results = $query->execute();

        if (empty($results)) {
            $this->getUpsertLogger()->debug(
                'Could not find record id for field ' . $fieldName . ' with value ' . $fieldValue
                . ' and module ' . $moduleName
            );
            return [];
        }

        $this->getUpsertLogger()->debug('Found record id ' . $results['0']['id'] . ' for field ' . $fieldName . ' with value ' .
            $fieldValue . ' and module ' . $moduleName);

        // this is for database implementations that in Sugar do not support unique database constraints with multiple null values
        if (safeCount($results) > 1) {
            $outputDuplicate = '';
            foreach ($results as $result) {
                if (!empty($outputDuplicate)) {
                    $outputDuplicate .= ', ';
                }
                $outputDuplicate .= $result['id'];
                if ($result['deleted']) {
                    $outputDuplicate .= ' ' . translate('LBL_INTEGRATE_DUPLICATE_RECORDS_DELETED');
                }
            }
            $this->getUpsertLogger()->error('Found multiple database records for ' . $fieldName . ' ' . $fieldValue . ' on module ' . $moduleName .
                '. Please remove or update the matching records accordingly. Matching records: ' . $outputDuplicate);
            throw new \SugarApiExceptionInvalidParameter('LBL_INTEGRATE_DUPLICATE_RECORDS', [$fieldName, $fieldValue, $moduleName, $outputDuplicate]);
        }

        return [
            'id' => $results['0']['id'],
            'deleted' => $results['0']['deleted'],
        ];
    }

    /**
     * Returns record id based on the provided field
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getRecordId(string $moduleName, string $fieldName, string $fieldValue, bool $getDeleted = true): array
    {
        $result = $this->getRecordIdByField($moduleName, $fieldName, $fieldValue, $getDeleted);
        if (empty($result)) {
            $this->getUpsertLogger()->error('Could not find record id for field ' . $fieldName . ' with value ' .
                $fieldValue . ' and module ' . $moduleName);
            throw new \SugarApiExceptionInvalidParameter('LBL_INTEGRATE_INVALID_PARAM', [$fieldName, $fieldValue, $moduleName]);
        }
        return $result;
    }

    /**
     * Set the field to null
     */
    public function removeField(string $moduleName, string $id, string $fieldName): void
    {
        $bean = \BeanFactory::newBean($moduleName);
        $qb = \DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->update($bean->table_name)
            ->set($fieldName, 'NULL')
            ->where($qb->expr()->eq('id', $qb->createPositionalParameter($id)))
            ->execute();
    }

    /**
     * Returns 'upsert_api' logger channel
     * @return LoggerManager
     */
    public function getUpsertLogger()
    {
        // to change the log level use:
        // $sugar_config['logger']['channels']['upsert_api']['level'] = 'debug';
        return Factory::getLogger('upsert_api');
    }
}
