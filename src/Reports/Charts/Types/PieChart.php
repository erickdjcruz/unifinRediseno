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

namespace Sugarcrm\Sugarcrm\Reports\Charts\Types;

class PieChart extends BaseChart
{
    /**
     * @inheritdoc
     */
    public function transformData(): array
    {
        $defaultBorderWidth = 2;
        $labels = $this->getLabels($this->data);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => $this->buildBackgroundColors(),
                    'borderColor' => $this->getBorderColor(),
                    'borderWidth' => $defaultBorderWidth,
                    'data' => $this->getData($this->data['values']),
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        $options['cutout'] = '0';

        return $options;
    }
}
