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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

/**
 * OpportunitiesCurrencyRateUpdate
 *
 * A class for updating currency rates on specified database table columns
 * when a currency conversion rate is updated by the administrator.
 *
 */
class RevenueLineItemsCurrencyRateUpdate extends CurrencyRateUpdateAbstract
{
    /**
     * @const CHUNK_SIZE
     * number of SQL queries to group together for SQLRunner
     */
    public const CHUNK_SIZE = 100;

    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        // set rate field definitions
        $this->addRateColumnDefinition('revenue_line_items', 'base_rate');
        // set usdollar field definitions
        $this->addUsDollarColumnDefinition('revenue_line_items', 'discount_amount', 'discount_amount_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'discount_price', 'discount_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'list_price', 'list_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'deal_calc', 'deal_calc_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'book_value', 'book_value_usdollar');
    }

    /**
     * doCustomUpdateRate
     *
     * Return true to skip updates for this module.
     * Return false to do default update of base_rate column.
     * To custom processing, do here and return true.
     *
     * @access public
     * @param string $table
     * @param string $column
     * @param string $currencyId
     * @return boolean true if custom processing was done
     * @throws DBALException
     */
    public function doCustomUpdateRate($table, $column, $currencyId)
    {
        // get the conversion rate
        $rate = $this->db->getConnection()
            ->executeQuery(
                'SELECT conversion_rate FROM currencies WHERE id = ?',
                [$currencyId]
            )->fetchOne();

        $stages = $this->getClosedStages();

        $query = <<<SQL
UPDATE {$table} SET {$column} = ?
WHERE sales_stage NOT IN (?)
AND currency_id = ?
SQL;
        $this->db->getConnection()
            ->executeUpdate(
                $query,
                [$rate, $stages, $currencyId],
                [null, Connection::PARAM_STR_ARRAY, null]
            );
        return true;
    }

    /**
     * doCustomUpdateUsDollarRate
     *
     * Return true to skip updates for this module.
     * Return false to do default update of amount * base_rate = usdollar
     * To custom processing, do here and return true.
     *
     * @access public
     * @param string $tableName
     * @param string $usDollarColumn
     * @param string $amountColumn
     * @param string $currencyId
     * @return boolean true if custom processing was done
     * @throws DBALException
     */
    public function doCustomUpdateUsDollarRate($tableName, $usDollarColumn, $amountColumn, $currencyId)
    {

        $stages = $this->getClosedStages();

        $query = <<<SQL
UPDATE {$tableName} SET {$usDollarColumn} = {$amountColumn} / base_rate
WHERE sales_stage NOT IN (?)
AND currency_id = ?
SQL;

        $this->db->getConnection()
            ->executeUpdate(
                $query,
                [$stages, $currencyId],
                [Connection::PARAM_STR_ARRAY, null]
            );
        return true;
    }

    /**
     * do post update process to update the opportunity RLIs, ENT only
     */
    public function doPostUpdateAction()
    {
        $oppConfig = $this->getOpportunityConfig();
        if ($oppConfig['opps_view_by'] === 'RevenueLineItems') {
            $stages = $this->getClosedStages();

            $conn = $this->db->getConnection();
            $selectQuery = <<<SQL
SELECT T1.opportunity_id,
    Sum(T1.likely_case) AS likely,
    Sum(T1.worst_case) AS worst,
    Sum(T1.best_case) AS best
FROM (
    SELECT rli.opportunity_id,
        (rli.likely_case / rli.base_rate) AS likely_case,
        (rli.worst_case / rli.base_rate) AS worst_case,
        (rli.best_case / rli.base_rate) AS best_case
    FROM revenue_line_items rli
    WHERE rli.deleted = 0) T1
INNER JOIN (
        SELECT DISTINCT opportunity_id
        FROM revenue_line_items
        WHERE currency_id = ?
        AND sales_stage NOT IN (?)) T2
    ON T2.opportunity_id = T1.opportunity_id
GROUP BY T1.opportunity_id
SQL;

            $rows = $conn->executeQuery(
                $selectQuery,
                [$this->currencyId, $stages],
                [null, Connection::PARAM_STR_ARRAY],
            );

            $queryParams = [];
            foreach ($rows->iterateAssociative() as $row) {
                $queryParams[] = [
                    $row['likely'],
                    $row['best'],
                    $row['worst'],
                    $row['opportunity_id'],
                    $stages,
                ];
            }

            if (safeCount($queryParams) < self::CHUNK_SIZE) {
                $sql = <<<SQL
UPDATE opportunities
SET amount = ? * base_rate,
    best_case = ? * base_rate,
    worst_case = ? * base_rate
WHERE id = ?
  AND sales_status NOT IN (?)
SQL;

                // do queries in this process
                foreach ($queryParams as $params) {
                    $conn->executeUpdate(
                        $sql,
                        $params,
                        [null, null, null, null, Connection::PARAM_STR_ARRAY]
                    );
                }
            } else {
                // schedule queries to SQLRunner job scheduler
                $chunks = array_chunk($queryParams, self::CHUNK_SIZE);
                global $timedate, $current_user;
                foreach ($chunks as $chunk) {
                    $job = BeanFactory::newBean('SchedulersJobs');
                    $job->name = 'SugarJobOpportunitiesCurrencyRateBatchUpdate: ' . $timedate->getNow()->asDb();
                    $job->target = 'class::SugarJobOpportunitiesCurrencyRateBatchUpdate';
                    $job->data = json_encode($chunk);
                    $job->retry_count = 0;
                    $job->assigned_user_id = $current_user->id;
                    $jobQueue = new SugarJobQueue();
                    $jobQueue->submitJob($job);
                }
            }
        }

        return true;
    }

    /**
     * getClosedStages
     *
     * Return an array of closed stage names from the opportunity bean.
     *
     * @access public
     * @return array array of closed stage values
     */
    public function getClosedStages()
    {
        static $rli;
        if (!isset($rli)) {
            $rli = BeanFactory::newBean('RevenueLineItems');
        }
        return $rli->getClosedStages();
    }


    /**
     * Returns the Opportunities module configuration
     *
     * @return array
     */
    protected function getOpportunityConfig()
    {
        return Opportunity::getSettings();
    }
}
