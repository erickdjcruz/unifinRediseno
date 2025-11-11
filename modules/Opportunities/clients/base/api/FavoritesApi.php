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

class FavoritesApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'postFavoriteRecords' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'favorites'],
                'pathVars' => ['module', 'favorites'],
                'minVersion' => '11.4',
                'method' => 'getFavoriteRecords',
                'shortHelp' => 'Get all the favorite items in alphabetical order',
                'longHelp' => 'modules/Opportunities/clients/base/api/help/favorites_post_help.html',
            ],
            'getFavoriteRecords' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'favorites'],
                'pathVars' => ['module', 'favorites'],
                'minVersion' => '11.5',
                'method' => 'getFavoriteRecords',
                'shortHelp' => 'Get all the favorite items in alphabetical order',
                'longHelp' => 'modules/Opportunities/clients/base/api/help/favorites_get_help.html',
            ],
        ];
    }

    /**
     * Gets the favorite products from SugarFavorites table
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getFavoriteRecords(ServiceBase $api, array $args)
    {
        $max_num = 8;
        $pageNum = $args['pageNum'] ?? 0;

        try {
            $favoriteProductIdQuery = new SugarQuery();
            $productTemplateNamesQuery = new SugarQuery();

            //Getting product template ids in reverse chronological order
            $sugarFavoritesBean = BeanFactory::newBean('SugarFavorites');

            $favoriteProductIdQuery->select(['record_id']);
            $favoriteProductIdQuery->from($sugarFavoritesBean, ['add_deleted' => true]);
            $favoriteProductIdQuery->where()
                ->queryAnd()
                ->equals('module', 'ProductTemplates')
                ->equals('assigned_user_id', "{$GLOBALS['current_user']->id}");

            $favoriteProductIdResult = $favoriteProductIdQuery->execute();

            $totalPages = ceil(safeCount($favoriteProductIdResult) / $max_num);
            $pageNum = ($pageNum >= $totalPages ? $pageNum - 1 : $pageNum);

            $productTemplateIds = array_column($favoriteProductIdResult, 'record_id');
            $productTemplatesBean = BeanFactory::newBean('ProductTemplates');

            $productTemplateNamesQuery->select(['*']);
            $productTemplateNamesQuery->from($productTemplatesBean, ['add_deleted' => true]);
            $productTemplateNamesQuery->where()->queryAnd()
                ->in('id', $productTemplateIds)
                ->equals('active_status', 'Active');
            $productTemplateNamesQuery->orderBy('name', 'ASC');

            if ($pageNum === -1) {
                $productTemplateNamesQuery->offset(0)
                    ->limit($max_num);
            } else {
                $productTemplateNamesQuery->offset($pageNum * $max_num)
                    ->limit($max_num);
            }

            $productTemplateNamesResult = $productTemplateNamesQuery->execute();

            return [
                'totalPages' => $totalPages,
                'pageNum' => $pageNum,
                'max_num' => $max_num,
                'records' => $productTemplateNamesResult,
            ];
        } catch (SugarQueryException $e) {
            // Swallow the exception.
            $GLOBALS['log']->warn(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
