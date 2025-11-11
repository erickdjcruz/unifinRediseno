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
<form name = 'editlabels' id = 'editlabels' onsubmit='return false;'>
    {sugar_csrf_form_token}
    <input type='hidden' name='module' value='ModuleBuilder'>
    <input type='hidden' name='action' value='saveLabels'>
    <input type='hidden' name='view_module' value='{$view_module}'>
    {if $view_package}
        <input type='hidden' name='view_package' value='{$view_package}'>
    {/if}
    {if $inPropertiesTab}
        <input type='hidden' name='editLayout' value='{$editLayout}'>
    {elseif $mb}
        <input class='button' name = 'saveBtn' id = "saveBtn" type='button' value='{$mod_strings.LBL_BTN_SAVE}' onclick='ModuleBuilder.handleSave("editlabels" );'>
    {else}
        <input class='button' name = 'publishBtn' id = "publishBtn" type='button' value='{$mod_strings.LBL_BTN_SAVEPUBLISH}' onclick='ModuleBuilder.handleSave("editlabels" );'>
        <input class='button' name = 'renameModBtn' id = "renameModBtn" type='button' value='{$mod_strings.LBL_BTN_RENAME_MODULE}'
               onclick='javascript:parent.SUGAR.App.router.navigate(`Administration/module-names-and-icons`, { trigger: true });'>
    {/if}
    <div style="float: right">
        {html_options name='labels' options=$labels_choice selected=$labels_current onchange='this.form.action.value="EditLabels";ModuleBuilder.handleSave("editlabels")'}
    </div>
    <hr >
    <input type='hidden' name='to_pdf' value='1'>

    <table class='mbLBL'>
        <thead>
            <tr>
                <th class="labels-cell align-right">
                    {$mod_strings.LBL_LANGUAGE}
                </th>
                <th class="labels-cell align-left">
                    {html_options name='selected_lang' options=$available_languages selected=$selected_lang onchange='this.form.action.value="EditLabels";ModuleBuilder.handleSave("editlabels")'}
                </th>

                {if $showCompareLanguage}
                    <th class="labels-cell align-left">
                        {$mod_strings.LBL_COMPARISON_LANGUAGE}:
                    </th>
                    <th class="labels-cell align-left">
                        {html_options name='comparison_lang' options=$availableCompareLanguages selected=$comparisonLang onchange='this.form.action.value="EditLabels";ModuleBuilder.handleSave("editlabels")'}
                    </th>
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach from=$MOD item='label' key='key'}
                <tr>
                    <td class="labels-cell align-right">
                        {$key|escape:'html'}:
                    </td>
                    <td class="labels-cell align-left">
                        <input type='hidden' name="label_{$key|escape:'html'}" id="label_{$key|escape:'html'}" value='no_change'><input id="input_{$key|escape:'html'}" onchange='document.getElementById("label_{$key|escape:'javascript'}").value = this.value; ModuleBuilder.state.markAsDirty();' value="{$label|escape:'html'}" size="60">
                    </td>

                    {if $showCompareLanguage}
                        <td class="labels-cell align-left">
                            {if $showCompareLanguage && $matchingLabels.$key}
                                {$matchingLabelHelp}
                            {/if}
                        </td>
                        <td class="labels-cell align-left">
                            {if $comparisonLangStrings.$key}
                                {$comparisonLangStrings.$key}
                            {/if}
                        </td>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>

    {if $inPropertiesTab}
        <input class='button' type='button' value='{$APP.LBL_CANCEL_BUTTON_LABEL}' onclick="ModuleBuilder.hidePropertiesTab();">
    {/if}
</form>
<script>
    ModuleBuilder.helpRegister('editlabels');
    ModuleBuilder.helpSetup('labelsHelp','default');
</script>
