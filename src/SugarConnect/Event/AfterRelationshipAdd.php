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
class AfterRelationshipAdd implements Publisher, ConfigurationAwareInterface
{
    use ConfigurationAwareTrait;

    /**
     * Sends an after_relationship_add event to the Sugar Connect webhook.
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

        // GetBean is used over RetrieveBean to guarantee that there is
        // something in the way of data to publish. Even if the bean can't be
        // found, a payload will be published that contains at least the
        // record's ID.
        //
        // Deleted records are also loaded in case the after_relationship_delete
        // event was in response to the related record getting deleted.
        $rbean = \BeanFactory::getBean(
            $args['related_module'],
            $args['related_id'],
            [
                'disable_row_level_security' => true,
            ],
            false
        );
        $fields = Event::getFields($rbean);
        $data = \ApiHelper::getHelper($api, $rbean)->formatForApi(
            $rbean,
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
                'is_update' => $bean->isUpdate(),
                'change_type' => $event,
                'related_module' => $args['related_module'],
                'related_id' => $args['related_id'],
                'link' => $args['link'],
                'data' => $data,
            ]
        );
    }
}
