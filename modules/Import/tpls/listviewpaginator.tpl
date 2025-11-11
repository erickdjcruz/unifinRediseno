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

<tr class='pagination' role='presentation'>
    <td colspan='{$colCount|escape:'html':'UTF-8'}'>
        <table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
            <tr>
                <td  nowrap='nowrap' width='1%' align="left" class='paginationChangeButtons'>
                {if $pageData.offsets.current != 0}
                    <button type='button' id='listViewStartButton' name='listViewStartButton' title='{$navStrings.start|escape:'html':'UTF-8'}' class='button' onClick='SUGAR.IV.getTable("{$tableID|escape:javascript}",0);'>
                        <img src='{sugar_getimagepath file='start.png'}' alt='{$navStrings.start|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                    {else}
                    <button type='button' id='listViewStartButton' name='listViewStartButton' title='{$navStrings.start|escape:'html':'UTF-8'}' class='button' disabled='disabled'>
                        <img src='{sugar_getimagepath file='start_off.png'}' alt='{$navStrings.start|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                {/if}
                {if $pageData.offsets.current != 0 }
                    <button type='button' id='listViewPrevButton' name='listViewPrevButton' title='{$navStrings.previous|escape:'html':'UTF-8'}' class='button' onClick='SUGAR.IV.getTable("{$tableID|escape:javascript}", {$pageData.offsets.previous|escape:javascript});'>
                        <img src='{sugar_getimagepath file='previous.png'}' alt='{$navStrings.previous|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                    {else}
                    <button type='button' id='listViewPrevButton' name='listViewPrevButton' class='button' title='{$navStrings.previous|escape:'html':'UTF-8'}' disabled='disabled'>
                        <img src='{sugar_getimagepath file='previous_off.png'}' alt='{$navStrings.previous|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                {/if}
                    <span class='pageNumbers'>({if $pageData.offsets.lastOffsetOnPage == 0}0{else}{$pageData.offsets.current+1}{/if} - {$pageData.offsets.lastOffsetOnPage} {$navStrings.of} {$pageData.offsets.total})</span>
                {if $pageData.offsets.next > 0}
                    <button type='button' id='listViewNextButton' name='listViewNextButton' title='{$navStrings.next|escape:'html':'UTF-8'}' class='button' onClick='SUGAR.IV.getTable("{$tableID|escape:javascript}", {$pageData.offsets.next|escape:javascript});'>
                        <img src='{sugar_getimagepath file='next.png'}' alt='{$navStrings.next|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                {else}
                    <button type='button' id='listViewNextButton' name='listViewNextButton' class='button' title='{$navStrings.next|escape:'html':'UTF-8'}' disabled='disabled'>
                        <img src='{sugar_getimagepath file='next_off.png'}' alt='{$navStrings.next|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                {/if}
                {if $pageData.offsets.next > 0}
                    <button type='button' id='listViewEndButton' name='listViewEndButton' title='{$navStrings.end|escape:'html':'UTF-8'}' class='button' onClick='SUGAR.IV.getTable("{$tableID|escape:javascript}", {$pageData.offsets.last|escape:javascript});' >
                        <img src='{sugar_getimagepath file='end.png'}' alt='{$navStrings.end|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                {else}
                    <button type='button' id='listViewEndButton' name='listViewEndButton' title='{$navStrings.end|escape:'html':'UTF-8'}' disabled='disabled' class='button' onClick='SUGAR.IV.getTable("{$tableID|escape:javascript}", {$pageData.offsets.last|escape:javascript});' >
                        <img src='{sugar_getimagepath file='end_off.png'}' alt='{$navStrings.end|escape:'html':'UTF-8'}' align='absmiddle' border='0'>
                    </button>
                {/if}
                </td>
                <td nowrap="nowrap" width='2%' class='paginationActionButtons'></td>
            </tr>
        </table>
    </td>
</tr>
