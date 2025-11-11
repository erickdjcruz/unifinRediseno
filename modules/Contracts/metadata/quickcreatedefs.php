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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs ['Contracts'] =
    [
        'QuickCreate' => [
            'templateMeta' => [
                'form' => [
                    'buttons' => [
                        'SAVE',
                        'CANCEL',
                    ],
                ],
                'maxColumns' => '2',
                'widths' => [
                    [
                        'label' => '10',
                        'field' => '30',
                    ],
                    [
                        'label' => '10',
                        'field' => '30',
                    ],
                ],
                'javascript' => '<script type="text/javascript" language="javascript">
		function setvalue(source)  {ldelim} 
			src= new String(source.value);
			target=new String(source.form.name.value);
	
			if (target.length == 0)  {ldelim} 
				lastindex=src.lastIndexOf("\"");
				if (lastindex == -1)  {ldelim} 
					lastindex=src.lastIndexOf("\\\\\"");
				 {rdelim}  
				if (lastindex == -1)  {ldelim} 
					source.form.name.value=src;
					source.form.escaped_name.value = src;
				 {rdelim}  else  {ldelim} 
					source.form.name.value=src.substr(++lastindex, src.length);
					source.form.escaped_name.value = src.substr(lastindex, src.length);
				 {rdelim} 	
			 {rdelim} 			
		 {rdelim} 
	
		function set_expiration_notice_values(form)  {ldelim} 
			if (form.expiration_notice_flag.checked)  {ldelim} 
				form.expiration_notice_flag.value = "on";
				form.expiration_notice_date.value = "";
				form.expiration_notice_time.value = "";
				form.expiration_notice_date.readonly = true;
				form.expiration_notice_time.readonly = true;
				if(typeof(form.due_meridiem) != \'undefined\')  {ldelim} 
					form.due_meridiem.disabled = true;
				 {rdelim} 
				
			 {rdelim}  else  {ldelim} 
				form.expiration_notice_flag.value="off";
				form.expiration_notice_date.readOnly = false;
				form.expiration_notice_time.readOnly = false;
				
				if(typeof(form.due_meridiem) != \'undefined\')  {ldelim} 
					form.due_meridiem.disabled = false;
				 {rdelim} 
				
			 {rdelim} 
		 {rdelim} 
	</script>',
            ],
            'panels' => [
                'lbl_contract_information' => [

                    [
                        'name',
                        'status',
                    ],

                    [
                        'reference_code',
                        ['name' => 'start_date', 'displayParams' => ['showFormats' => true]],
                    ],

                    [
                        'account_name',
                        ['name' => 'end_date', 'displayParams' => ['showFormats' => true]],
                    ],

                    [
                        'opportunity_name',
                    ],

                    [
                        'type',
                        ['name' => 'customer_signed_date', 'displayParams' => ['showFormats' => true]],
                    ],

                    [
                        ['name' => 'currency_id', 'label' => 'LBL_CURRENCY'],
                        ['name' => 'company_signed_date', 'displayParams' => ['showFormats' => true]],
                    ],

                    [
                        ['name' => 'total_contract_value', 'displayParams' => ['size' => 15, 'maxlength' => 25]],
                        ['name' => 'expiration_notice', 'type' => 'datetimecombo', 'displayParams' => ['showFormats' => true]],
                    ],

                    [
                        ['name' => 'description'],
                    ],
                ],
                'LBL_PANEL_ASSIGNMENT' => [
                    [
                        'assigned_user_name',
                        ['name' => 'team_name', 'displayParams' => ['required' => true]],
                    ],
                ],
            ],
        ],
    ];
