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

use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionExportNotAllowed;
use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionExportOwner;
use ACLAction;
use SugarBean;

class ExportRightsRule extends BaseRule
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
        foreach ($fullTableList as $key => $moduleDetails) {
            $exportAction = ACLAction::getUserAccessLevel(
                $this->user->id,
                $moduleDetails['module'],
                'export',
                'module',
            );

            if ($exportAction === ACL_ALLOW_NONE) {
                throw new SugarReportsExceptionExportNotAllowed();
            }

            if ($exportAction === ACL_ALLOW_OWNER && !$bean->isOwner($this->user)) {
                throw new SugarReportsExceptionExportOwner();
            }
        }
        return true;
    }
}
