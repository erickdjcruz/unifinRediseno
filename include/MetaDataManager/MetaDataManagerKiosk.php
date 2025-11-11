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

class MetaDataManagerKiosk extends MetaDataManager
{
    /**
     * Get Kiosk specific metadata
     *
     * @param array $args
     * @param MetaDataContextInterface $context
     * @return array Kiosk metadata
     */
    public function getMetadata($args = [], MetaDataContextInterface $context = null)
    {
        $data = [
            'config' => [],
        ];
        $admin = $this->getAdministration();
        $admin->retrieveSettings(false, true);

        // need AWS Connnect settings only
        foreach ($admin->settings as $key => $value) {
            if (substr($key, 0, 4) === 'aws_') {
                $data['config'][$this->translateConfigProperty($key)] = $value;
            }
        }

        $data['_hash'] = $this->hashChunk($data);
        return $data;
    }

    /**
     * Get Administration
     *
     * @return Administration
     */
    public function getAdministration()
    {
        return new Administration();
    }
}
