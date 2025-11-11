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

            {if $k eq 'records'}
                {foreach from=$items key=k item=records}
                    <tr>
                        {foreach from=$records key=k item=record}
                            {if $record.type eq 'datetimecombo' || $record.type eq 'datetime'}
                                <td>{$record.value|date_format:'%Y-%m-%d %H:%M:%S'}</td>
                            {else}
                                <td>{$record.value}</td>
                            {/if}
                        {/foreach}
                    </tr>
                {/foreach}
            {/if}
        {/foreach}
    </table>
{/strip}
