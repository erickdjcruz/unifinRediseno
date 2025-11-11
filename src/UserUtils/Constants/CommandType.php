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

namespace Sugarcrm\Sugarcrm\UserUtils\Constants;

/**
 * The CommandType class contains all the command types
 */
abstract class CommandType
{
    public const CopyDashboards = 'CopyDashboards';
    public const DeleteDashboards = 'DeleteDashboards';
    public const CopyFilters = 'CopyFilters';
    public const DeleteFilters = 'DeleteFilters';
    public const CloneDashboards = 'CloneDashboards';
    public const CloneFilters = 'CloneFilters';
    public const CloneFavoriteReports = 'CloneFavoriteReports';
    public const CloneSugarEmailClient = 'CloneSugarEmailClient';
    public const CloneScheduledReporting = 'CloneScheduledReporting';
    public const CloneNotifyOnAssignment = 'CloneNotifyOnAssignment';
    public const CloneRemindersOptions = 'CloneRemindersOptions';
    public const CloneDefaultTeams = 'CloneDefaultTeams';
    public const CloneNavigationBar = 'CloneNavigationBar';
    public const CloneUserSettings = 'CloneUserSettings';
    public const BroadcastMessage = 'BroadcastMessage';
}
