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

/**
 * Class for separate storage of Email texts
 */
class EmailText extends SugarBean
{
    public $disable_row_level_security = true;
    public $table_name = 'emails_text';
    public $module_name = 'EmailText';
    public $module_dir = 'EmailText';
    public $object_name = 'EmailText';
    public $disable_custom_fields = true;
}
