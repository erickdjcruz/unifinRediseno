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
{* && $vardef.type != 'date' && $vardef.type != 'datetimecombo' *}
{if $vardef.type != 'address'
 && $vardef.type != 'multienum' && $vardef.type != 'radioenum'
 && $vardef.type != 'html' && $vardef.type != 'relate'
 && $vardef.type != 'url' && $vardef.type != 'iframe' && $vardef.type != 'parent'  && $vardef.type != 'image'
 && $vardef.type != 'autoincrement'
 && empty($vardef.function) && (!isset($vardef.studio.calculated) || $vardef.studio.calculated != false)
}

<tr><td class='mbLBL'>{sugar_translate module="DynamicFields" label="LBL_CALCULATED"}:</td>
    <td style="line-height:1em"><input type="checkbox" name="calculated" id="calculated" value="1" onclick ="ModuleBuilder.toggleCF()"
        {if !empty($vardef.calculated) && !empty($vardef.formula)}CHECKED{/if} {if $hideLevel > 5}disabled{/if}/>
		{if $hideLevel > 5}
            <input type="hidden" name="calculated" value="{$vardef.calculated}">
        {/if}
		{sugar_help text=$mod_strings.LBL_POPHELP_CALCULATED FIXX=250 FIXY=80}
		<input type="hidden" name="enforced" id="enforced" value="{$vardef.enforced}">
    </td>
</tr>
<tr id='formulaRow' {if empty($vardef.formula)}style="display:none"{/if}>
	<td class='mbLBL'>{sugar_translate module="DynamicFields" label="LBL_FORMULA"}:</td>
    <td>
        <input id="formula" type="hidden" name="formula" value="{$vardef.formula}" onchange="document.getElementById('formula_display').value = this.value"/>
        <input id="formula_display" type="text" name="formula_display" value="{$vardef.formula}" readonly="1"/>
	    <input type="button" class="button"  name="editFormula" style="margin-top: -2px"
		      value="{sugar_translate label="LBL_BTN_EDIT_FORMULA"}"
            onclick="ModuleBuilder.moduleLoadFormula(YAHOO.util.Dom.get('formula').value, 'formula', '{$calcFieldType}')"/>
    </td>
</tr>
{/if}
{if $vardef.type != 'address' && !$hideDependent}
<tr id='depCheckboxRow'><td class='mbLBL'>{sugar_translate module="DynamicFields" label="LBL_DEPENDENT"}:</td>
    <td><input type="checkbox" name="dependent" id="dependent" value="1" onclick ="ModuleBuilder.toggleDF(null, '#popup_form_id .toggleDep')"
        {if !empty($vardef.dependency)}CHECKED{/if} {if $hideLevel > 5}disabled{/if}/>
        {sugar_help text=$mod_strings.LBL_POPHELP_DEPENDENT FIXX=250 FIXY=80}
    </td>
</tr>
{if $vardef.type !== 'enum' && $vardef.type !== 'multienum'}
    <tr id='visFormulaRow' {if empty($vardef.dependency)}style="display:none"{/if}><td class='mbLBL'>{sugar_translate module="DynamicFields" label="LBL_VISIBLE_IF"}:</td>
        <td>
            <input id="dependency" type="hidden" name="dependency" value="{$vardef.dependency|escape:'html'}" onchange="document.getElementById('dependency_display').value = this.value"/>
            <input id="dependency_display" type="text" name="dependency_display" value="{$vardef.dependency|escape:'html'}" readonly="1"/>
              <input class="button" type=button name="editFormula" value="{sugar_translate label="LBL_BTN_EDIT_FORMULA"}"
                onclick="ModuleBuilder.moduleLoadFormula(YAHOO.util.Dom.get('dependency').value, 'dependency', 'boolean')"/>
        </td>
    </tr>
{/if}
{/if}
{if $vardef.type != 'bool' && !$hideRequired}
    <tr>
        <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_REQUIRED_OPTION"}:</td>
        <td>
            <input type="checkbox" name="required" id="required" value="1" onclick ="ModuleBuilder.toggleRequiredFormula()"
                   {if !empty($vardef.required)}CHECKED{/if} {if $hideLevel > 5}disabled{/if}/>
            {if $hideLevel > 5}<input type="hidden" name="required" value="{$vardef.required}">{/if}
            {sugar_help text=$mod_strings.LBL_POPHELP_REQUIRED FIXX=250 FIXY=80}
        </td>
    </tr>
    <tr id='requiredFormulaRow' {if empty($vardef.required)}style="display:none"{/if}>
        <td class='mbLBL'>{sugar_translate module="DynamicFields" label="LBL_REQUIRED_IF"}:</td>
        <td>
            <input id="required_formula" type="hidden" name="required_formula"
                   value="{$vardef.required_formula|escape:'html'}"
                   onchange="document.getElementById('required_formula_display').value = this.value"/>
            <input id="required_formula_display" type="text" name="required_formula_display"
                   value="{$vardef.required_formula|escape:'html'}"
                   readonly="1"/>
            <input class="button" type=button name="editFormula" value="{sugar_translate label="LBL_BTN_EDIT_FORMULA"}"
                   onclick="ModuleBuilder.moduleLoadFormula(YAHOO.util.Dom.get('required_formula').value, 'required_formula', 'boolean')"/>
        </td>
    </tr>
{/if}
{if !$hideReadOnly}
    <tr id='readonlyRow' {if $vardef.calculated || $vardef.massupdate}style="display:none"{/if}>
        <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_READONLY_OPTION"}:</td>
        <td>
            <input type="checkbox" name="readonly" id="readonly" value="1" onclick="ModuleBuilder.handleFieldInteractions('readonly')"
                   {if !empty($vardef.readonly)}CHECKED{/if} {if $hideLevel > 5}disabled{/if}/>
            {if $hideLevel > 5}<input type="hidden" name="readonly" value="{$vardef.readonly}">{/if}
            {sugar_help text=$mod_strings.LBL_POPHELP_READONLY FIXX=250 FIXY=80}
        </td>
    </tr>
    <tr id='readonlyFormulaRow' {if !$vardef.readonly || $vardef.calculated || $vardef.massupdate}style="display:none"{/if}>
        <td class='mbLBL'>{sugar_translate module="DynamicFields" label="LBL_READONLY_IF"}:</td>
        <td>
            <input id="readonly_formula" type="hidden" name="readonly_formula"
                   value="{$vardef.readonly_formula|escape:'html'}"
                   onchange="document.getElementById('readonly_formula_display').value = this.value"/>
            <input id="readonly_formula_display" type="text" name="readonly_formula_display"
                   value="{$vardef.readonly_formula|escape:'html'}"
                   readonly="1"/>
            <input class="button" type=button name="editFormula" value="{sugar_translate label="LBL_BTN_EDIT_FORMULA"}"
                   onclick="ModuleBuilder.moduleLoadFormula(YAHOO.util.Dom.get('readonly_formula').value, 'readonly_formula', 'boolean')"/>
        </td>
    </tr>
{/if}
