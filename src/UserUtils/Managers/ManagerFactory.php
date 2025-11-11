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

use Sugarcrm\Sugarcrm\UserUtils\Constants\CommandType;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\InvokerPayload;

/**
 * Factory for managers
 */
class ManagerFactory
{
    /**
     * Gets the receiver for each command type
     *
     * @param string $commandClass
     * @param InvokerPayload $payload
     * @return Manager
     */
    public static function getManager(string $type, InvokerPayload $payload): Manager
    {
        switch ($type) {
            case CommandType::CloneDashboards:
            case CommandType::CopyDashboards:
            case CommandType::DeleteDashboards:
                return new DashboardManager($payload);
            case CommandType::CloneFilters:
            case CommandType::CopyFilters:
            case CommandType::DeleteFilters:
                return new FiltersManager($payload);
            case CommandType::CloneFavoriteReports:
            case CommandType::CloneDefaultTeams:
            case CommandType::CloneRemindersOptions:
            case CommandType::CloneNavigationBar:
            case CommandType::CloneNotifyOnAssignment:
            case CommandType::CloneSugarEmailClient:
                return new GeneralManager($payload);
            case CommandType::CloneScheduledReporting:
                return new ScheduledReportingManager($payload);
            case CommandType::CloneUserSettings:
                return new UserSettingsManager($payload);
        }

        return null;
    }
}
