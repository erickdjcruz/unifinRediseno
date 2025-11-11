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

use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionDisabledExport;
use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionAdminExportOnly;
use SugarBean;

class ConfigExportRule extends BaseRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($data): bool
    {
        global $sugar_config;
        $disableExport = false;

        if (isset($sugar_config['disable_export'])) {
            $disableExport = $sugar_config['disable_export'];
        }

        if ($disableExport) {
            throw new SugarReportsExceptionDisabledExport();
        }

        if (!isset($sugar_config['admin_export_only']) ||
            ($sugar_config['admin_export_only'] && !is_admin($this->user))) {
            throw new SugarReportsExceptionAdminExportOnly();
        }

        $isValid = !$disableExport;

        return $isValid;
    }
}
