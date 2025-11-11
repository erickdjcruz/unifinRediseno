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

namespace Sugarcrm\Sugarcrm\UserUtils\Managers;

use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerBasePayload;
use Sugarcrm\Sugarcrm\Util\Uuid as UtilUuid;
use SugarQuery;

/**
 * Manager for handling schedule reporting commands
 */
class ScheduledReportingManager extends Manager
{
    /**
     * The source user
     * @var string
     */
    private $sourceUser;

    /**
     * The destination users
     *
     * @var array
     */
    private $destinationUsers;

    /**
     * Shows if should use a schedule job
     *
     * @var bool
     */
    protected $useScheduledJob = false;

    /**
     * Defs for the report schedules
     *
     * @var bool
     */
    private $reportSchedulesUserDefs = [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'reportschedule_id' => [
            'name' => 'reportschedule_id',
            'type' => 'id',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
        ],
    ];

    /**
     * Constructor
     *
     * @param InvokerBasePayload $payload
     */
    public function __construct(InvokerBasePayload $payload)
    {
        $this->payload = $payload;
        $this->sourceUser = $payload->getSourceUser();
        $this->destinationUsers = $payload->getDestinationUsers();

        if (safeCount($this->destinationUsers) > self::MAX_USER) {
            $this->useScheduledJob = true;
        }
    }

    /**
     * Clone scheduled reporting
     */
    public function cloneScheduledReporting(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destUserId) {
            $this->deleteScheduledReports($destUserId);
            $this->insertScheduledReports($this->sourceUser, $destUserId);
        }
    }

    /**
     * Delete scheduled reports for a user
     *
     * @param string $userId
     */
    private function deleteScheduledReports(string $userId): void
    {
        $db = \DBManagerFactory::getInstance();

        $reportSchedulesBean = \BeanFactory::newBean('ReportSchedules');
        $fieldDefs = $reportSchedulesBean->getFieldDefinitions();
        $db->updateParams($reportSchedulesBean->getTableName(), $fieldDefs, [
            'deleted' => '1',
        ], [
            'assigned_user_id' => $userId,
        ]);
    }

    /**
     * Insert scheduled reports from a user to another user
     *
     * @param string $sourceUserId
     * @param string $destUserId
     */
    private function insertScheduledReports(string $sourceUserId, string $destUserId): void
    {
        $db = \DBManagerFactory::getInstance();

        $reportSchedulesBean = \BeanFactory::newBean('ReportSchedules');
        $fieldDefs = $reportSchedulesBean->getFieldDefinitions();

        $scheduledReports = $this->getScheduledReports($sourceUserId);

        foreach ($scheduledReports as $scheduleReport) {
            $reportScheduleUsers = $this->getReportUsers($scheduleReport['id']);

            $newReportId = UtilUuid::uuid4();
            $scheduleReport['id'] = $newReportId;
            $scheduleReport['assigned_user_id'] = $destUserId;
            $scheduleReport['user_id'] = $destUserId;
            $scheduleReport['modified_user_id'] = $destUserId;
            $scheduleReport['created_by'] = $destUserId;

            $db->insertParams($reportSchedulesBean->getTableName(), $fieldDefs, $scheduleReport);
            $this->insertReportScheduledUsers($newReportId, $reportScheduleUsers);
        }
    }

    /**
     * Retrieve scheduled reports for a user
     *
     * @param string $userId
     * @return array
     */
    public function getScheduledReports(string $userId): array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('ReportSchedules'), ['team_security' => false]);
        $sugarQuery->where()->equals('assigned_user_id', $userId);
        $result = $sugarQuery->execute();

        return $result;
    }

    /**
     * Retrieve users in a scheduled report
     *
     * @param string $reportId
     * @return array
     */
    private function getReportUsers(string $reportId): array
    {
        $bean = new \SugarBean();
        $bean->module_name = 'reportschedules_users';
        $bean->table_name = 'reportschedules_users';
        $bean->field_defs = $this->reportSchedulesUserDefs;

        $sugarQuery = new SugarQuery();
        $sugarQuery->from($bean);
        $sugarQuery->where()->equals('reportschedule_id', $reportId);
        $result = $sugarQuery->execute();

        return $result;
    }

    /**
     * Add users to a scheduled report
     *
     * @param string $newReportId
     * @param array $reportScheduleUsers
     */
    private function insertReportScheduledUsers(string $newReportId, array $reportScheduleUsers): void
    {
        $db = \DBManagerFactory::getInstance();

        $bean = new \SugarBean();
        $bean->module_name = 'reportschedules_users';
        $bean->table_name = 'reportschedules_users';
        $bean->field_defs = $this->reportSchedulesUserDefs;

        foreach ($reportScheduleUsers as $reportScheduleUser) {
            $reportScheduleUser['id'] = UtilUuid::uuid4();
            $reportScheduleUser['reportschedule_id'] = $newReportId;
            $bean->fromArray($reportScheduleUser);
            $db->insert($bean);
        }
    }
}
