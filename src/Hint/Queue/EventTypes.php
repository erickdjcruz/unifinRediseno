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

namespace Sugarcrm\Sugarcrm\Hint\Queue;

class EventTypes
{
    public const MIXED = 'mixed';

    public const INSTANCE_INIT = 'recordNewInstance';
    public const INSTANCE_INIT_CLONE = 'cloneInstance';
    public const INSTANCE_RESYNC = 'synchronizeInstance';
    public const INSTANCE_DELETE = 'deleteInstance';

    public const INSTANCE_INIT_COMPLETED = 'recordNewInstanceCompleted';
    public const INSTANCE_INIT_CLONE_COMPLETED = 'cloneInstanceCompleted';
    public const INSTANCE_RESYNC_COMPLETED = 'synchronizeInstanceCompleted';

    public const INSTANCE_DISABLE_NOTIFICATIONS = 'disableNotifications';
    public const INSTANCE_ENABLE_NOTIFICATIONS = 'enableNotifications';

    public const FAVORITE_ADD = 'favoriteAdd'; // before 5.1
    public const FAVORITE_DELETE = 'favoriteDelete'; // before 5.1

    public const ACCOUNT_ADD_ONE = 'accountAdd';
    public const ACCOUNT_DELETE = 'accountDelete'; // before 5.1
    public const ACCOUNT_DELETE_ONE = 'accountDeleteOne';
    public const ACCOUNT_DELETE_ALL = 'accountDeleteAll';
    public const ACCOUNT_UPDATE = 'accountUpdate';

    public const ACCOUNT_OWNER_ADD = 'accountOwnerAdd'; // before 5.1
    public const ACCOUNT_OWNER_DELETE = 'accountOwnerDelete'; // before 5.1

    public const ACCOUNT_TAG_ADD = 'accountTagAdd'; // before 5.1
    public const ACCOUNT_TAG_DELETE = 'accountTagDelete'; // before 5.1

    public const USER_DELETE = 'userDelete'; // before 5.1
    public const USER_EMAIL_UPDATE = 'userEmailUpdate'; // before 5.1

    public const UPDATE_LICENSE = 'updateLicense'; // 5.4.0

    public const TARGET_ADD = 'targetAdd';
    public const TARGET_DELETE = 'targetDelete'; // not used
    public const TARGET_DELETE_ALL = 'targetDeleteAll'; // batch
    public const TARGET_UPDATE = 'targetUpdate';

    public const ACCOUNTSET_ADD = 'accountsetAdd'; // before 5.1
    public const ACCOUNTSET_ADD_ONE = 'accountsetAddOne';
    public const ACCOUNTSET_DELETE = 'accountsetDelete';
    public const ACCOUNTSET_DELETE_ALL = 'accountsetDeleteAll'; // batch
    public const ACCOUNTSET_UPDATE = 'accountsetUpdate';
    public const ACCOUNTSET_TYPE_UPDATE = 'accountsetTypeUpdate'; // before 5.1

    public const ACCOUNTSET_ADD_TARGET = 'accountsetAddTarget';
    public const ACCOUNTSET_DELETE_TARGET = 'accountsetDeleteTarget';

    public const ACCOUNTSET_ADD_TAG = 'accountsetAddTag'; // before 5.1
    public const ACCOUNTSET_DELETE_TAG = 'accountsetDeleteTag'; // before 5.1
}
