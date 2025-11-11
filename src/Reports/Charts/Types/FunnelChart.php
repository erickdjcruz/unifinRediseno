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

class FunnelChart extends BaseChart
{
    /**
     * @inheritdoc
     */
    public function transformData()
    {
        $labels = array_reverse($this->getLabels($this->data));
        $data = array_reverse($this->getData($this->data['values']));
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => $this->buildBackgroundColors(),
                    'data' => $data,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $defaultAspectRatio = 2;

        $props = $this->getProperties();
        $options = parent::getOptions();
        $options['sort'] = 'desc';
        $options['maintainAspectRation'] = $props['maintainAspectRatio'] ?? false;
        $options['aspectRatio'] = $props['aspectRatio'] ?? $defaultAspectRatio;

        return $options;
    }
}
