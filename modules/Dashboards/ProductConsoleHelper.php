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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

/**
 * Class ProductConsoleHelper
 *
 * Helper methods for Product Console manipulation and retrieval.
 */
class ProductConsoleHelper
{
    public static $renewalsConsoleId = 'da438c86-df5e-11e9-9801-3c15c2c53980';

    /**
     * Check if RLI is enabled.
     *
     * @return bool
     */
    public function useRevenueLineItems(): bool
    {
        return Opportunity::usingRevenueLineItems();
    }

    /**
     * Remove renewals console for ops only.
     * @param SugarBean $bean
     * @param string $event
     * @param array $args
     */
    public function removeRenewalsConsole(SugarBean $bean, string $event, array $args)
    {
        if (!$this->useRevenueLineItems() && isset($args[0]) && $args[0] instanceof SugarQuery) {
            $args[0]->where()->notEquals('id', self::$renewalsConsoleId);

            // Adjust the limit of the query to compensate for the removed records
            if (!empty($args[1]['id_query']) && $args[1]['id_query'] instanceof SugarQuery) {
                $oldLimit = $args[1]['id_query']->limit;
                if (isset($oldLimit)) {
                    $args[1]['id_query']->limit($oldLimit + 1);
                }
            } else {
                $oldLimit = $args[0]->limit;
                if (isset($oldLimit)) {
                    $args[0]->limit($oldLimit + 1);
                }
            }
        }
    }

    /**
     * Checks if it's a renewals console for ops only.
     * @param SugarBean $bean
     * @param string $event
     * @param array $args
     * @throws SugarApiExceptionNotAuthorized
     */
    public function checkRenewalsConsole(SugarBean $bean, string $event, array $args)
    {
        if ($this->isAuthorized() === false && !empty($args['id']) && $args['id'] === self::$renewalsConsoleId) {
            throw new SugarApiExceptionNotAuthorized('SUGAR_API_EXCEPTION_RECORD_NOT_AUTHORIZED', ['view']);
        }
    }

    /**
     * Checks to see if this console is loadable. Checks adminWork setting for cases like upgrade.
     * @return boolean
     */
    private function isAuthorized(): bool
    {
        // Some console require certain setups on the system
        return $this->useRevenueLineItems() || $this->isAdminWork();
    }

    /**
     * Determines if we are in an admin only process to allow consumption of the dashboard
     * @return boolean
     */
    public function isAdminWork(): bool
    {
        return AccessControlManager::instance()->getAdminWork() === true;
    }
}
