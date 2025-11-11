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

use Sugarcrm\Sugarcrm\MetaData\ViewdefManager;

/**
 * Class LeadViews
 */
class LeadViews
{
    public const PRODUCT_DASHLETS = ['product-catalog', 'product-quick-picks'];

    /**
     * Toggles the Product Catalog and Product Catalog Quick Picks dashlets within the
     * Base Leads Convert Dashboard layout
     *
     * @param bool $enableDashlets true if the dashlets should be added;
     *                              false if they should be removed
     */
    public function toggleConvertDashboardProductDashlets(bool $enableDashlets)
    {
        // Get the metadata for the convert-dashboard layout
        $viewdefManager = $this->getViewdefManager();
        $convertDashboardMeta = $this->getConvertDashboardMeta($viewdefManager);

        // Alter the convert-dashboard layout to add or remove the dashlets
        if ($enableDashlets) {
            $convertDashboardMeta = $this->addProductDashlets($convertDashboardMeta);
        } else {
            $convertDashboardMeta = $this->removeProductDashlets($convertDashboardMeta);
        }

        // Update the convert-dashboard layout with the new components and save it
        $this->saveConvertDashboardMeta($viewdefManager, $convertDashboardMeta);
    }

    /**
     * Filters the convert dashboard components to remove the PC and PQP dashlets
     *
     * @param array $convertDashboardMeta the convert-dashboard metadata
     * @return array the resulting convert-dashboard metadata
     */
    protected function removeProductDashlets($convertDashboardMeta)
    {
        $convertDashboardMeta['components'] = array_filter($convertDashboardMeta['components'], function ($component) {
            return (empty($component['view']) || !in_array($component['view'], self::PRODUCT_DASHLETS));
        });
        return $convertDashboardMeta;
    }

    /**
     * Adds the PC and PQP dashlets to the convert dashboard
     *
     * @param array $convertDashboardMeta the convert-dashboard metadata
     * @return array the resulting convert-dashboard metadata
     */
    protected function addProductDashlets($convertDashboardMeta)
    {
        $dashletsToAdd = array_flip(self::PRODUCT_DASHLETS);

        // Check to make sure we don't duplicate dashlets
        foreach ($convertDashboardMeta['components'] as $component) {
            if (!empty($component['view']) && array_key_exists($component['view'], $dashletsToAdd)) {
                unset($dashletsToAdd[$component['view']]);
            }
        }

        // Add the dashlets that are missing
        foreach ($dashletsToAdd as $dashletView => $value) {
            $convertDashboardMeta['components'][] = [
                'view' => $dashletView
            ];
        }

        return $convertDashboardMeta;
    }

    /**
     * Returns the metadata for the dashboard-pane component that houses
     * the convert-dashboard
     *
     * @return array the default dashboard-pane metadata
     */
    public static function getDefaultConvertDashboardMeta()
    {
        return [
            'layout' => [
                'type' => 'base',
                'name' => 'dashboard-pane',
                'css_class' => 'dashboard-pane',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'convert-dashboard-container',
                            'components' => [
                                [
                                    'layout' => 'convert-dashboard',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns an instance of ViewdefManager for getting/setting viewdefs
     *
     * @return ViewdefManager
     */
    protected function getViewdefManager()
    {
        return new ViewdefManager();
    }

    /**
     * Returns the current metadata for the convert-dashboard
     *
     * @param ViewdefManager $viewdefManager the ViewdefManager to use
     * @return array the fetched metadata
     */
    protected function getConvertDashboardMeta(ViewdefManager $viewdefManager)
    {
        return $viewdefManager->loadViewdef('base', 'Leads', 'convert-dashboard', false, true);
    }

    /**
     * Updates the convert-dashboard metadata in the system with the given metadata
     *
     * @param ViewdefManager $viewdefManager the ViewdefManager to use
     * @param array $convertDashboardMeta the convert-dashboard metadata to save
     */
    protected function saveConvertDashboardMeta(ViewdefManager $viewdefManager, $convertDashboardMeta)
    {
        $viewdefManager->saveViewdef($convertDashboardMeta, 'Leads', 'base', 'convert-dashboard', true);
    }
}
