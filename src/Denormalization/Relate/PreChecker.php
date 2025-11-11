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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate;

use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\Db;

final class PreChecker
{
    private const ALTER_TABLE_TIME_INTERPOLATION_STATISTICS = [
        'row_count' => [0, 1000, 5000, 50000, 500000, 1000000, 2000000, 5000000, 20000000],
        'seconds_required' => [0, 0.6, 1.7, 12, 150, 320, 800, 2400, 7900],
    ];

    /** @var Db */
    protected $db;

    /** @var SynchronizationManager */
    protected $synchronizationJob;

    /** @var FieldConfig */
    protected $fieldConfig;

    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->synchronizationJob = new SynchronizationManager();
        $this->fieldConfig = new FieldConfig();
    }

    /**
     * Check if a field is able to be dernomalized
     *
     * @return array with empty 'validation_error' key if success, otherwise the key will contain a message
     * @throws Doctrine\DBAL\Exception
     */
    public function validateDenormalization(Entity $entity): array
    {
        if ($entity->fieldDef['type'] !== 'relate') {
            return ['validation_error' => 'LBL_MANAGE_RELATE_DENORMALIZATION_PRECHECK_SUPPORTED_TYPES'];
        }

        $fieldList = $this->getFieldList();
        $fieldList = $fieldList[$entity->getTargetModuleName()] ?? [];

        if (!empty($fieldList[$entity->fieldName])) {
            return ['validation_error' => 'LBL_MANAGE_RELATE_DENORMALIZATION_PRECHECK_ALREADY_DENORMALIZED'];
        }
        if ($this->synchronizationJob->isJobInProgress()) {
            return ['validation_error' => 'LBL_MANAGE_RELATE_DENORMALIZATION_PRECHECK_PREV_JOB_IN_PROGRESS'];
        }

        $details = [];

        if ($entity->relationship) {
            $relTable = $entity->relationship->join_table;
            $lhsTable = $entity->relationship->lhs_table;
            $rhsTable = $entity->relationship->rhs_table;
            $sourceColumnName = $entity->sourceFieldName;
            $tableDescription = $this->db->getTableDescription($entity->getTargetTableName());
            $details = $tableDescription[$sourceColumnName] ?? [];
            $details['table_rel'] = $relTable ?? '';
            $details['table_lhs'] = $lhsTable;
            $details['table_rhs'] = $rhsTable;

            if ($relTable) {
                $details['count_rel'] = $this->db->getTableRowCount($relTable);
            } else {
                $details['count_rel'] = 0;
            }

            $details['count_lhs'] = $this->db->getTableRowCount($lhsTable);

            $details['count_rhs'] = $this->db->getTableRowCount($rhsTable);

            $details['update_count'] = max(
                $details['count_rel'],
                $details['count_lhs'],
                $details['count_rhs']
            );

            $details['estimated_time'] = $this->estimateAlterTime($details['update_count']);

            $details['sql'] = $this->getAlterSql($entity);
        }

        return $details;
    }

    /**
     *
     * Check if a field is able to returned to default state after denormalization
     *
     * @return array with empty 'validation_error' key if success, otherwise the key will contain a message
     */
    public function validateNormalization(Entity $entity): array
    {
        $details = [];
        if ($entity->fieldDef['type'] !== 'relate') {
            return ['validation_error' => 'LBL_MANAGE_RELATE_DENORMALIZATION_PRECHECK_SUPPORTED_TYPES'];
        }

        $fieldList = $this->getFieldList();
        $fieldList = $fieldList[$entity->getTargetModuleName()] ?? [];

        if (empty($fieldList[$entity->fieldName])) {
            return ['validation_error' => 'LBL_MANAGE_RELATE_DENORMALIZATION_PRECHECK_FIELD_IS_NOT_DENORMALIZED'];
        }

        $details['sql'] = $this->getAlterSql($entity);

        return $details;
    }

    protected function getFieldList(): array
    {
        return $this->fieldConfig->getList();
    }

    protected function estimateAlterTime(int $rowCount): float
    {
        $seconds = $this->interpolate(
            $rowCount,
            self::ALTER_TABLE_TIME_INTERPOLATION_STATISTICS['row_count'],
            self::ALTER_TABLE_TIME_INTERPOLATION_STATISTICS['seconds_required']
        );

        if ($seconds >= 10) {
            $seconds = round($seconds);
        } else {
            $seconds = round($seconds, 2);
        }

        return $seconds;
    }

    protected function interpolate($val, $xValues, $yValues)
    {
        if ($val <= $xValues[0]) {
            return $yValues[0];
        }

        $size = safeCount($xValues);
        $minDelta = null;
        $firstI = 0;
        $secondI = 0;
        for ($i = 0; $i < $size; $i++) {
            $delta = abs($xValues[$i] - $val);
            if ($delta < $minDelta || is_null($minDelta) || $secondI === $firstI) {
                $minDelta = $delta;
                $secondI = $firstI;
                $firstI = $i;
            }
        }

        $fX = $xValues[$firstI];
        $sX = $xValues[$secondI];
        $fY = $yValues[$firstI];
        $sY = $yValues[$secondI];

        $dX = ($sX - $fX) === 0 ? 1 : $sX - $fX;
        $x = $fY + (($sY - $fY) * ($val - $fX) / $dX);

        return $x;
    }

    protected function getAlterSql(Entity $entity): ?string
    {
        return $this->db->getAlterSql(
            $entity->getTargetTableName(),
            $entity->getTargetFieldDef()
        );
    }
}
