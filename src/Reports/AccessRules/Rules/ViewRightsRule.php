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

namespace Sugarcrm\Sugarcrm\Reports\AccessRules\Rules;

use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionListNotAllowed;
use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionViewNotAllowed;
use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionAccessDisabled;
use SugarApiExceptionNotAuthorized;
use ACLAction;
use SugarBean;

class ViewRightsRule extends BaseRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($bean): bool
    {
        if (empty($bean->content)) {
            return true;
        }

        $reportContent = $bean->content;

        if (is_string($reportContent)) {
            $reportContent = json_decode($bean->content, true);

            if ($reportContent === null) {
                return true;
            }
        }

        $fullTableList = $reportContent['full_table_list'];
        foreach (safeIsIterable($fullTableList) ? $fullTableList : [] as $key => $moduleDetails) {
            if (!$bean->ACLAccess('view')) {
                throw new SugarApiExceptionNotAuthorized('No access to view records for module: Reports');
            }

            $accessAction = ACLAction::getUserAccessLevel(
                $this->user->id,
                $moduleDetails['module'],
                'access',
                'module',
            );

            if ($accessAction === ACL_ALLOW_DISABLED) {
                throw new SugarReportsExceptionAccessDisabled();
            }

            $listAction = ACLAction::getUserAccessLevel(
                $this->user->id,
                $moduleDetails['module'],
                'list',
                'module',
            );

            if ($listAction === ACL_ALLOW_NONE) {
                throw new SugarReportsExceptionListNotAllowed();
            }

            $viewAction = ACLAction::getUserAccessLevel(
                $this->user->id,
                $moduleDetails['module'],
                'view',
                'module',
            );

            if ($viewAction === ACL_ALLOW_NONE) {
                throw new SugarReportsExceptionViewNotAllowed();
            }
        }

        return true;
    }
}
