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
{strip}
    <table width="80%" border="1" cellpadding="0" cellspacing="0">
        {foreach from=$data key=k item=items}
            {if $k eq 'header'}
                <tr>
                    {foreach from=$items key=k item=value}
                        <th>{$value.vname}</th>
                    {/foreach}
                </tr>
            {/if}
        {/foreach}

        {foreach from=$data key=key item=items}
            {if $key eq 'records'}
                {foreach from=$items key=k item=records}
                    <tr>
                        {foreach from=$records key=k item=record}
                            <td>{$record}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            {/if}

            {if $key eq 'grandTotal'}
                {foreach from=$items key=k item=grandTotal}
                    <tfoot>
                        <td colspan="100%" cellspacing="0">{$grandTotal.vname}: {$grandTotal.value}</td>
                    </tfoot>
                {/foreach}
            {/if}
        {/foreach}
    </table>
{/strip}
