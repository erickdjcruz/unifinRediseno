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

class TreemapChart extends BaseChart
{
    /**
     * @inheritdoc
     */
    public function transformData(): array
    {
        $newData = $this->getNewData($this->data);

        return [
            'labels' => $this->getLabels($this->data),
            'datasets' => $this->buildDataSets($newData),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions(): ?array
    {
        $options = parent::getOptions();
        $options['plugins']['legend']['display'] = false;
        return $options;
    }

    /**
     * Transform data for treemap
     *
     * @param array $data
     * @return array
     */
    private function getNewData(array $data): array
    {
        $newData = [];
        foreach ($data['values'] as $value) {
            $newData [] = ['label' => $value['label'][0], 'value' => $value['values'][0]];
        }

        return $newData;
    }

    /**
     * Build datasets for treemap
     *
     * @param mixed $newData
     * @return array
     */
    private function buildDataSets($newData): array
    {
        $backgroundColors = $this->buildBackgroundColors();
        $dataSets = [
            [
                'backgroundColor' => $backgroundColors,
                'tree' => $newData,
                'key' => 'value',
                'groups' => ['label'],
                'spacing' => 1,
                'labels' => [
                    'display' => true,
                    'align' => 'center',
                    'position' => 'center',
                    'color' => '#ffffff',
                ],
            ],
        ];
        return $dataSets;
    }

    /**
     * @inheritdoc
     */
    protected function buildBackgroundColors(): array
    {
        $newData = $this->getNewData($this->data);
        $colors = [];

        foreach ($newData as $index => $data) {
            $colors[] = $this->getColor($index);
        }

        return $colors;
    }
}
