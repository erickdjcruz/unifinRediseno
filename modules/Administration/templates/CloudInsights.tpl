{*
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
*}


<form method="post" action="{$insights.url}" target="_blank">
    <input name="key" value="{$insights.key}" type="hidden">
    <input name="login" value="{$mod.LBL_FORM_ACCESS_CLOUD_INSIGHT}" type="submit">
</form>
