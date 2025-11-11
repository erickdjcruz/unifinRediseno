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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean;

use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;

class Repository
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Repository
     */
    private static $instance;

    /*
     * default exception type
     */
    protected static $defaultExceptionType = 'Base';

    /*
     * exception namespace root path
     */
    protected static $exceptionNSRoot = 'Sugarcrm\\Sugarcrm\\CustomerJourney\\Exception\\';

    /**
     * Retrieves the singleton instance
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get db instance
     * @return DBManager
     * @codeCoverageIgnore
     */
    private function getDb()
    {
        return \DBManagerFactory::getInstance();
    }

    /**
     * Gets a Customer Journey exception object
     * @param string $type The type of Exception to get
     * @param array $msgArgs Exception msg args
     * @param int $httpCode The exception code
     * @param Exception $previous The previous exception (used for chaining)
     * @return Exception
     */
    public function getException($type = '', $msgArgs = [], $httpCode = 0)
    {
        if (empty($type)) {
            $type = static::$defaultExceptionType;
        }

        $exceptionClassName = $type . 'Exception';
        $defaultClassName = static::$defaultExceptionType . 'Exception';

        // Get the full class name for this exception, getting the custom class if found
        $exceptionClass = \SugarAutoLoader::customClass(static::$exceptionNSRoot . $exceptionClassName);

        // Get the full default class name, getting the custom class if found
        $defaultClass = \SugarAutoLoader::customClass(static::$exceptionNSRoot . $defaultClassName);

        // Set the class name to load based on availability of the class. If the type class exists,
        // use it, otherwise fallback to the default class
        $class = class_exists($exceptionClass) ? $exceptionClassName : $defaultClassName;

        return $this->getExceptionObject($class, $msgArgs, $httpCode);
    }

    /**
     * return  Customer Journey exception object
     * @param string $class Class Name
     * @param array $msgArgs Exception msg args
     * @param int $httpCode The exception code
     * @return Exception
     */
    private function getExceptionObject($class, $msgArgs, $httpCode)
    {
        $exception = null;
        switch ($class) {
            case 'BaseException':
                $exception = new CJException\CustomerJourneyException(null, $msgArgs, null, $httpCode);
                break;
            case 'InvalidLicenseException':
                $exception = new CJException\InvalidLicenseException(null, $msgArgs, null, $httpCode);
                break;
            case 'JourneyNotCompletedException':
                $exception = new CJException\JourneyNotCompletedException(null, $msgArgs, null, $httpCode);
                break;
            case 'NotFoundException':
                $exception = new CJException\NotFoundException(null, $msgArgs, null, $httpCode);
                break;
            case 'ParentNotFoundException':
                $exception = new CJException\ParentNotFoundException(null, $msgArgs, null, $httpCode);
                break;
            case 'UserNotAuthorizedException':
                $exception = new CJException\UserNotAuthorizedException(null, $msgArgs, null, $httpCode);
                break;
            default:
                $exception = new \SugarApiExceptionInvalidParameter();
        }
        return $exception;
    }

    /**
     * Handles validation of required arguments for a request
     * @param array $args
     * @param array $requiredFields
     * @throws SugarApiExceptionMissingParameter
     * @codeCoverageIgnore
     */
    public function requireArgs(array $args, $requiredFields = [])
    {
        foreach ($requiredFields as $fieldName) {
            if (!array_key_exists($fieldName, $args) || empty($args[$fieldName])) {
                throw new \SugarApiExceptionMissingParameter('Missing parameter: ' . $fieldName);
            }
        }
    }

    /**
     * Retrieves a module with id $id and
     * returns a instance of the retrieved bean
     * @param string $id : the id of the module that should be retrieved
     * @param bool $deleted : Set false if the bean is already deleted
     * @return bean
     * @throws CJException\NotFoundException
     */
    public function getById($module, $id, $deleted = true)
    {
        if (empty($id)) {
            $exception = $this->getException('NotFound', [
                'moduleName' => $module,
                'data' => $id,
                'deleted' => $deleted,
            ]);
            throw $exception;
        }

        /** @var DRI* $bean */
        $bean = \BeanFactory::getBean($module, $id, [
            'disable_row_level_security' => true,
        ], $deleted);

        if (is_null($bean) && empty($bean->id)) {
            $exception = $this->getException('NotFound', [
                'moduleName' => $module,
                'data' => $id,
                'deleted' => $deleted,
            ]);
            throw $exception;
        }

        return $bean;
    }

    /**
     * Retrieves a module record with name $name and
     * returns a instance of the retrieved bean
     * (parent id can be empty)
     * @param array $args
     * @return bean
     * @throws CJException\NotFoundException
     */
    public function getByNameAndParent($args = [])
    {
        $this->requireArgs($args, ['table', 'name', 'module']);

        $query = $this->getBasicSugarQuery($args);
        $query->where()->equals('name', $args['name']);
        if ($args['module'] === 'DRI_SubWorkflow_Templates') {
            $query->where()->equals('dri_workflow_template_id', $args['parentId']);
        } elseif ($args['module'] === 'DRI_Workflow_Task_Templates') {
            $query->where()->equals('dri_subworkflow_template_id', $args['parentId']);
        }
        if (!empty($args['skipId'])) {
            $query->where()->notEquals('id', $args['skipId']);
        }
        $rows = $query->execute();
        if (empty($rows)) {
            $exception = $this->getException('NotFound', ['moduleName' => $args['module'], 'data' => $args['name']]);
            throw $exception;
        }
        return $this->getById($args['module'], $rows[0]['id']);
    }

    /**
     * Retrieves a module with name $name and
     * returns a instance of the retrieved bean
     * @param array $args
     * @return bean
     * @throws CJException\NotFoundException
     */
    public function getByName($args = [])
    {
        $this->requireArgs($args, ['table', 'name', 'module']);

        $query = $this->getBasicSugarQuery($args);
        $query->where()->equals('name', $args['name']);
        if (!empty($args['skipId'])) {
            $query->where()->notEquals('id', $args['skipId']);
        }
        $rows = $query->execute();
        if (empty($rows)) {
            $exception = $this->getException('NotFound', ['moduleName' => $args['module'], 'data' => $args['name']]);
            throw $exception;
        }

        return $this->getById($args['module'], $rows[0]['id']);
    }

    /**
     * Check if bean's particular field value is changed or not
     * @param mixed $bean
     * @param string $name
     * @return bool
     */
    public function isFieldChanged($bean, $name)
    {
        $value = $bean->{$name};

        if (!$bean->isUpdate()) {
            $def = $bean->getFieldDefinition($name);
            return isset($def['default']) ? $def['default'] !== $value : !empty($value);
        }

        return is_array($bean->fetched_row) && isset($bean->fetched_row[$name]) && $bean->fetched_row[$name] !== $value;
    }

    /**
     * Retrieves a module record with name $sortOrder and
     * returns a instance of the retrieved bean
     * (parent id can be empty)
     * @param array $args
     * @return bean
     * @throws CJException\NotFoundException
     */
    public function getByOrderAndParent($args = [])
    {
        $this->requireArgs($args, ['table', 'module']);

        $query = $this->getBasicSugarQuery($args);
        $query->where()->equals('sort_order', $args['sortOrder']);
        if ($args['module'] === 'DRI_SubWorkflow_Templates') {
            $query->where()->equals('dri_workflow_template_id', $args['parentId']);
        } elseif ($args['module'] === 'DRI_Workflow_Task_Templates') {
            $query->where()->equals('dri_subworkflow_template_id', $args['parentId']);
        }
        if (!empty($args['skipId'])) {
            $query->where()->notEquals('id', $args['skipId']);
        }
        $rows = $query->execute();
        if (empty($rows)) {
            $exception = $this->getException('NotFound', ['moduleName' => $args['module'], 'data' => $args['name']]);
            throw $exception;
        }

        return $this->getById($args['module'], $rows[0]['id']);
    }

    /**
     * Re-order the sort orders and labels of all the stages
     * @param object $bean
     * @param array $stages
     * @param string $defaultSortOrderOperation
     * @param string $table
     */
    public function reorderSortOrdersAndLabels($bean, $stages, $defaultSortOrderOperation, $table)
    {
        if (empty($bean) || empty($stages) || empty($table)) {
            return;
        }
        if ($defaultSortOrderOperation === 'update_order') {
            //assign the last stage's order +1 to this stage now
            $lastStageSortOrder = $stages[safeCount($stages) - 1]->sort_order;
            $bean->sort_order = $lastStageSortOrder + 1;
            $bean->label = (($bean->sort_order < 10) ? '0' : '') . $bean->sort_order . '. ' . $bean->name;
        } else {
            foreach ($stages as $stage) {
                if ($stage->sort_order >= $bean->sort_order) {
                    if ($defaultSortOrderOperation === 'minus') {
                        $stage->sort_order = $stage->sort_order - 1;
                    } else {
                        $stage->sort_order = $stage->sort_order + 1;
                    }
                    $stage->label = (($stage->sort_order < 10) ? '0' : '') . $stage->sort_order . '. ' . $stage->name;

                    $this->updateSortOrderAndLabel($table, $stage->sort_order, $stage->label, $stage->id);
                }
            }
        }
    }

    /**
     * Update the sort order and label of the stage
     * @param string $table
     * @param string $sort_order
     * @param string $label
     * @param string $id
     */
    public function updateSortOrderAndLabel($table, $sort_order, $label, $id)
    {
        if (empty($table) || empty($sort_order) || empty($label) || empty($id)) {
            return;
        }
        global $db;
        $sql = <<<SQL
                UPDATE
                    {$table}
                SET
                    sort_order = ? , label = ?
                WHERE
                    id = ?
SQL;
        $db->getConnection()->executeUpdate($sql, [$sort_order, $label, $id]);
    }

    /**
     * Get stage by cycle id and name
     * @param array $args
     * @return DRI_SubWorkflow
     * @throws CJException\NotFoundException
     */
    public function getByCycleIdAndName($args = [])
    {
        $this->requireArgs($args, ['name', 'module']);
        $query = $this->getBasicSugarQuery($args);
        $query->where()->equals('name', $args['name']);
        $query->where()->equals('dri_workflow_id', $args['cycleId']);
        if (!empty($args['skipId'])) {
            $query->where()->notEquals('id', $args['skipId']);
        }
        $rows = $query->execute();
        if (empty($rows)) {
            $exception = $this->getException('NotFound', ['moduleName' => $args['module'], 'data' => $args['name']]);
            throw $exception;
        }

        return $this->getById($args['module'], $rows[0]['id']);
    }

    /**
     * Get stage by cycle id and order
     * @param array $args
     * @return DRI_SubWorkflow
     * @throws CJException\NotFoundException
     */
    public function getByCycleIdAndOrder($args = [])
    {
        $this->requireArgs($args, ['module']);

        $query = $this->getBasicSugarQuery($args);
        $query->where()->equals('sort_order', $args['order']);
        $query->where()->equals('dri_workflow_id', $args['cycleId']);
        if (!empty($args['skipId'])) {
            $query->where()->notEquals('id', $args['skipId']);
        }
        $rows = $query->execute();
        if (empty($rows)) {
            $exception = $this->getException('NotFound', ['moduleName' => $args['module'], 'data' => $args['name']]);
            throw $exception;
        }

        return $this->getById($args['module'], $rows[0]['id']);
    }

    /**
     * Get basic sugar query
     * @param array $args
     * @return SugarQuery $query
     */
    private function getBasicSugarQuery(array $args = [])
    {
        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean($args['module']), ['team_security' => false]);
        $query->select(['id']);
        $query->where()->equals('deleted', 0);
        return $query;
    }
}
