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

namespace Sugarcrm\Sugarcrm\SugarConnect\Event;

use Sugarcrm\Sugarcrm\SugarConnect\Publisher;
use Sugarcrm\Sugarcrm\SugarConnect\Configuration\ConfigurationAwareInterface;
use Sugarcrm\Sugarcrm\SugarConnect\Configuration\ConfigurationAwareTrait;

/**
 * @deprecated Will be removed in the next release.
 */
class AfterSave implements Publisher, ConfigurationAwareInterface
{
    use ConfigurationAwareTrait;

    /**
     * Sends an after_save event to the Sugar Connect webhook.
     *
     * @param \SugarBean $bean The bean that was changed.
     * @param string $event The type of event.
     * @param array $args Additional arguments.
     *
     * @return void
     */
    public function publish(\SugarBean $bean, string $event, array $args): void
    {
        $user = \BeanFactory::newBean('Users');
        $user->getSystemUser();

        $api = new \RestService();
        $api->user = $user;

        $fields = Event::getFields($bean);
        $data = \ApiHelper::getHelper($api, $bean)->formatForApi(
            $bean,
            $fields,
            [
                'fields' => $fields,
            ]
        );

        Event::publish(
            $this->getConfiguration(),
            [
                'module' => $bean->getModuleName(),
                'id' => $bean->id,
                // Use isUpdate if it is in args. Default to an update because
                // after_delete and after_restore are technically updates.
                'is_update' => $args['isUpdate'] ?? true,
                'change_type' => $event,
                'data' => $data,
            ]
        );
    }
}
