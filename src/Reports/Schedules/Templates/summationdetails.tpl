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
            {foreach from=$items key=k item=data}
                {if $k eq 'headers'}
                    {foreach from=$data key=k item=header}
                        {foreach $header as $values}
                            <thead>
                                <tr>
                                    {if empty($values@key)}
                                        <th style="text-align:left" colspan="100%" cellspacing="0">{$values}</th>
                                    {else}
                                        <th style="text-align:left" colspan="100%" cellspacing="0">{$values@key} = {$values}</th>
                                    {/if}
                                </tr>
                            </thead>
                        {/foreach}
                    {/foreach}
                {/if}

                {if $k eq 'header'}
                    <tr>
                        {foreach from=$data key=k item=headerValue}
                            <th>{$headerValue.vname}</th>
                        {/foreach}
                    </tr>
                {/if}

                {if $k eq 'data'}
                    {foreach from=$data key=k item=recordsArray}
                        <tr>
                            {foreach from=$recordsArray key=k item=records}
                                <td>{$records}</td>
                            {/foreach}
                        </tr>
                    {/foreach}
                {/if}

                {if $k eq 'grandTotal'}
                    {foreach from=$data key=k item=grandTotal}
                        <tfoot>
                            <td colspan="100%" cellspacing="0">{$grandTotal.vname}: {$grandTotal.value}</td>
                        </tfoot>
                    {/foreach}
                {/if}
            {/foreach}
        {/foreach}
    </table>
{/strip}
