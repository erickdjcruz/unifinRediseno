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

namespace Sugarcrm\Sugarcrm\Dashboards\Validators;

class ReportDashletValidator
{
    /**
     * Validate a dashletMeta
     * Make sure the report used was not deleted
     *
     * @param mixed $dashletMeta
     *
     * @return bool
     */
    public function validate($dashletMeta)
    {
        $reportId = $dashletMeta->view->reportId;
        $params = [
            'disable_row_level_security' => true,
        ];
        $reportBean = \BeanFactory::retrieveBean('Reports', $reportId, $params);

        if (!$reportBean) {
            return false;
        }

        return true;
    }

    /**
     * Validate a dashletMeta
     *
     * @param mixed $dashletMeta
     * @param mixed $field
     *
     * @return bool
     */
    public function validateField($dashletMeta, $field)
    {
        $reportId = $dashletMeta->view->reportId;
        $dashletSpecificData = $field->dashletSpecificData;

        if ($dashletSpecificData->reportId !== $reportId) {
            return false;
        }

        return true;
    }
}
