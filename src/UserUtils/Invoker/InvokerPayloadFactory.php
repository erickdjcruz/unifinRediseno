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

namespace Sugarcrm\Sugarcrm\UserUtils\Invoker;

use Sugarcrm\Sugarcrm\UserUtils\Constants\CommandType;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerBasePayload;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerDashboardsPayload;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerFiltersPayload;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerUserSettingsPayload;

/**
 * Factory for getting invoker payloads
 */
class InvokerPayloadFactory
{
    public static function getInvokerPayload(array $data): InvokerPayload
    {
        switch ($data['type']) {
            case CommandType::CloneDashboards:
            case CommandType::CopyDashboards:
            case CommandType::DeleteDashboards:
                return new InvokerDashboardsPayload($data);
            case CommandType::CloneFilters:
            case CommandType::CopyFilters:
            case CommandType::DeleteFilters:
                return new InvokerFiltersPayload($data);
            case CommandType::CloneUserSettings:
                return new InvokerUserSettingsPayload($data);
            case CommandType::CloneNavigationBar:
            case CommandType::CloneNotifyOnAssignment:
            case CommandType::CloneScheduledReporting:
            case CommandType::CloneRemindersOptions:
            case CommandType::CloneSugarEmailClient:
            case CommandType::BroadcastMessage:
            case CommandType::CloneFavoriteReports:
            case CommandType::CloneDefaultTeams:
                return new InvokerBasePayload($data);
        }

        return null;
    }
}
