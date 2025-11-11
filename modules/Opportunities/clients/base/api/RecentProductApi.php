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

class RecentProductApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'postRecentRecords' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'recent-product'],
                'pathVars' => ['module', 'recent-product'],
                'minVersion' => '11.4',
                'method' => 'getRecentRecords',
                'shortHelp' => 'Get top 10 recently used items in reverse Chronological order',
                'longHelp' => 'modules/Opportunities/clients/base/api/help/recent_product_post_help.html',
            ],
            'getRecentRecords' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'recent-product'],
                'pathVars' => ['module', 'recent-product'],
                'minVersion' => '11.5',
                'method' => 'getRecentRecords',
                'shortHelp' => 'Get top 10 recently used items in reverse Chronological order',
                'longHelp' => 'modules/Opportunities/clients/base/api/help/recent_product_get_help.html',
            ],
        ];
    }

    /**
     * Get the recently used products from the RLI table in reverse chronological order and
     * picks top 10 from them
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     */
    public function getRecentRecords(ServiceBase $api, array $args)
    {
        $this->checkAccess();
        $this->requireArgs($args, ['module']);
        $returnMostRecentRecords = [];

        try {
            $mostRecentQuery = new SugarQuery();
            $productTemplateNamesQuery = new SugarQuery();

            $beanName = ($args['module'] === 'Opportunities') ? 'RevenueLineItems' : 'Products';
            $bean = BeanFactory::newBean($beanName);

            $mostRecentQuery->select(['product_template_id', 'date_entered']);
            // here the team security can be omitted as each RLI/Product was created by current user once,
            // and the return result is not linked to RLI/Product visibility
            $mostRecentQuery->from($bean, ['add_deleted' => true, 'team_security' => false]);
            $mostRecentQuery->where()
                ->equals('created_by', "{$GLOBALS['current_user']->id}")
                ->notNull('product_template_id');
            $mostRecentQuery->orderBy('date_entered');

            $mostRecentQueryResult = $mostRecentQuery->execute();

            //Getting ten most recent unique records
            foreach ($mostRecentQueryResult as $res) {
                $foundProductId = false;
                foreach ($returnMostRecentRecords as $ret) {
                    if ($ret['product_template_id'] === $res['product_template_id']) {
                        $foundProductId = true;
                        break;
                    }
                }
                if (!$foundProductId) {
                    $returnMostRecentRecords[] = $res;
                }
                if (safeCount($returnMostRecentRecords) === 10) {
                    break;
                }
            }

            $productTemplateIds = array_column($returnMostRecentRecords, 'product_template_id');

            $productTemplatesBean = BeanFactory::newBean('ProductTemplates');

            $productTemplateNamesQuery->select(['*']);
            $productTemplateNamesQuery->from($productTemplatesBean, ['add_deleted' => true]);
            $productTemplateNamesQuery->where()->queryAnd()
                ->in('id', $productTemplateIds)
                ->equals('active_status', 'Active');

            $productTemplateNamesResult = $productTemplateNamesQuery->execute();

            $returnList = [];

            $count = safeCount($returnMostRecentRecords);
            $len = $count > 10 ? 10 : $count;
            for ($recentRecordCount = 0; $recentRecordCount < $len; $recentRecordCount++) {
                foreach ($productTemplateNamesResult as $pt) {
                    if ($returnMostRecentRecords[$recentRecordCount]['product_template_id'] === $pt['id']) {
                        $returnList[] = $pt;
                        break;
                    }
                }
            }

            return [
                'next_offset' => -1,
                'records' => $returnList,
            ];
        } catch (SugarQueryException $e) {
            // Swallow the exception.
            $GLOBALS['log']->warn(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    /**
     * Checks if the user has access to ProductTemplates
     */
    protected function checkAccess()
    {
        $ptBean = BeanFactory::newBean('ProductTemplates');
        if (!$ptBean->aclAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to view Product Catalog records');
        }
    }
}
