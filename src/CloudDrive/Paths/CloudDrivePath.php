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

namespace Sugarcrm\Sugarcrm\CloudDrive\Paths;

use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\CloudDrivePathFactory;

class CloudDrivePath
{
    private $model;

    /**
     * @constructor
     *
     * @param mixed $type
     * @return void
     */
    public function __construct(string $type)
    {
        $this->model = CloudDrivePathFactory::getPathModel($type);
    }

    /**
     * Returns the drive path
     *
     * @param mixed $options
     * @return null|array
     */
    public function getDrivePath(array $options): ?array
    {
        if ($this->model) {
            return $this->model->getDrivePath($options);
        }
        return null;
    }
}
