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

namespace Sugarcrm\Sugarcrm\Hint\Iss;

class Commands
{
    public const ISS_ADD_ACCOUNT = 'addAccount';       // always within a single accountset
    public const ISS_DELETE_ACCOUNT = 'deleteAccount'; // always within a single accountset
    public const ISS_DELETE_ACCOUNT_ALL = 'deleteAccountAll';  // remove all references to the account; it was deleted
    public const ISS_UPDATE_ACCOUNT_ALL = 'updateAccountAll';  // update all references to the account

    public const ISS_ADD_TARGET = 'addTarget';         // define a new target
    public const ISS_UPDATE_TARGET = 'updateTarget';   // changing the credentials
    // Not sure if we need this one or not
    // const ISS_DELETE_TARGET = 'deleteTarget';

    public const ISS_ADD_TARGET_TO_ACCOUNTSET = 'addTargetToAccountset';
    public const ISS_DELETE_TARGET_FROM_ACCOUNTSET = 'deleteTargetFromAccountset';

    public const ISS_ADD_ACCOUNTSET = 'addAccountset';
    public const ISS_DELETE_ACCOUNTSET = 'deleteAccountset';
    public const ISS_UPDATE_ACCOUNTSET = 'updateAccountset';

    // "batch" operations
    public const ISS_DELETE_ACCOUNTSETS = 'deleteAccountsets';
    public const ISS_DELETE_TARGETS = 'deleteTargets';

    public const ISS_RECORD_NEW_INSTANCE = 'recordNewInstance';
    public const ISS_SYNCHRONIZE_INSTANCE = 'synchronizeInstance';
    public const ISS_DELETE_INSTANCE = 'deleteInstance';
    public const ISS_INIT_CLONE_INSTANCE = 'cloneInstance';

    public const ISS_SYNCHRONIZE_INSTANCE_COMPLETED = 'synchronizeInstanceCompleted';
    public const ISS_RECORD_NEW_INSTANCE_COMPLETED = 'recordNewInstanceCompleted';
    public const ISS_INIT_CLONE_INSTANCE_COMPLETED = 'cloneInstanceCompleted';

    public const ISS_DISABLE_NOTIFICATIONS = 'disableNotifications';
    public const ISS_ENABLE_NOTIFICATIONS = 'enableNotifications';

    public const ISS_UPDATE_LICENSE = 'updateLicense';
}
