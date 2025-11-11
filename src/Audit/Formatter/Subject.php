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

namespace Sugarcrm\Sugarcrm\Audit\Formatter;

use Sugarcrm\Sugarcrm\Audit\Formatter;
use Sugarcrm\Sugarcrm\Security\Subject\Formatter as SubjectFormatter;

class Subject implements Formatter
{
    private $formatter;

    public function __construct(SubjectFormatter $formatter = null)
    {
        $this->formatter = $formatter;
    }

    public function formatRows(array &$rows)
    {
        $subjects = [];
        $impersonations = [];
        // gather all subjects
        foreach ($rows as $k => $v) {
            if (!empty($v['source']['subject'])) {
                $subjects[$k] = $v['source']['subject'];
                if ($this->isImpersonated($v)) {
                    $impersonations[$k] = [
                        '_module' => $v['source']['subject']['_module'],
                        'id' => $v['impersonated_by'],
                    ];
                }
            }
        }

        $formattedSubjects = $this->formatter->formatBatch($subjects);
        if (safeCount($impersonations) > 0) {
            $formattedImpersonations = $this->formatter->formatBatch($impersonations);
        }

        // merge formatted subjects into rows
        foreach ($formattedSubjects as $k => $v) {
            $rows[$k]['source']['subject'] = $v;
            if (isset($formattedImpersonations[$k])) {
                $rows[$k]['source']['subject']['impersonated'] = $formattedImpersonations[$k];
            }
        }

        return $rows;
    }

    /**
     * @param array $row
     * @return bool
     */
    private function isImpersonated(array $row): bool
    {
        return isset($row['impersonated_by'])
            && $row['source']['subject']['_module'] === 'Users'
            && isset($row['source']['attributes']['platform'])
            // Should not display integrations impersonations (SugarConnect for example)
            && $row['source']['attributes']['platform'] === 'base';
    }
}
