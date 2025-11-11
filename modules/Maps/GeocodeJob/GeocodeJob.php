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

// @codingStandardsIgnoreLine
class GeocodeJob extends SugarBean
{
    public $new_schema = true;
    public $module_dir = 'Maps/GeocodeJob';
    public $module_name = 'GeocodeJob';

    public $object_name = 'GeocodeJob';
    public $table_name = 'geocode_job';
    public $importable = false;
    public $disable_custom_fields = true;
    public $tracker_visibility = false;
    public $disable_row_level_security = true;
    public $update_modified_by = false;
    public $set_created_by = false;

    public $id;
    public $date_entered;
    public $date_modified;
    public $deleted;
    public $status;
    public $processed_entity_success_count;
    public $processed_entity_failed_count;
    public $addresses_data;
    public $geocode_result;
}
