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
$viewdefs['Campaigns']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'javascript' => '<script type="text/javascript" src="' . getJSPath('include/javascript/popup_parent_helper.js') . '"></script>
<script type="text/javascript">
function type_change() {ldelim}
	type = document.getElementsByName(\'campaign_type\');
	if(type[0].value==\'NewsLetter\') {ldelim}
		document.getElementById(\'freq_label\').style.display = \'\';
		document.getElementById(\'freq_field\').style.display = \'\';
	 {rdelim} else {ldelim}
		document.getElementById(\'freq_label\').style.display = \'none\';
		document.getElementById(\'freq_field\').style.display = \'none\';
	 {rdelim}
 {rdelim}
type_change();

function ConvertItems(id)  {ldelim}
	var items = new Array();

	//get the items that are to be converted
	expected_revenue = document.getElementById(\'expected_revenue\');
	budget = document.getElementById(\'budget\');
	actual_cost = document.getElementById(\'actual_cost\');
	expected_cost = document.getElementById(\'expected_cost\');

	//unformat the values of the items to be converted
	expected_revenue.value = unformatNumber(expected_revenue.value, num_grp_sep, dec_sep);
	expected_cost.value = unformatNumber(expected_cost.value, num_grp_sep, dec_sep);
	budget.value = unformatNumber(budget.value, num_grp_sep, dec_sep);
	actual_cost.value = unformatNumber(actual_cost.value, num_grp_sep, dec_sep);

	//add the items to an array
	items[items.length] = expected_revenue;
	items[items.length] = budget;
	items[items.length] = expected_cost;
	items[items.length] = actual_cost;

	//call function that will convert currency
	ConvertRate(id, items);

	//Add formatting back to items
	expected_revenue.value = formatNumber(expected_revenue.value, num_grp_sep, dec_sep);
	expected_cost.value = formatNumber(expected_cost.value, num_grp_sep, dec_sep);
	budget.value = formatNumber(budget.value, num_grp_sep, dec_sep);
	actual_cost.value = formatNumber(actual_cost.value, num_grp_sep, dec_sep);
 {rdelim}
</script>',
    ],
    'panels' => [
        'lbl_campaign_information' => [

            [
                ['name' => 'name'],
                ['name' => 'status'],
            ],

            [
                ['name' => 'start_date', 'displayParams' => ['required' => false, 'showFormats' => true]],
                ['name' => 'campaign_type',
                    'displayParams' => ['javascript' => 'onchange="type_change();"'],
                ],
            ],

            [
                ['name' => 'end_date', 'displayParams' => ['showFormats' => true]],
                [
                    'name' => 'frequency',
                    'customCode' => '<div style=\'none\' id=\'freq_field\'>{html_options name="frequency" options=$fields.frequency.options selected=$fields.frequency.value}</div></TD>',
                    'customLabel' => '<div style=\'none\' id=\'freq_label\'>{$MOD.LBL_CAMPAIGN_FREQUENCY}</div>',
                ],
            ],

            [
                'currency_id',
                'impressions',
            ],

            [
                'budget',
                'expected_cost',
            ],

            [
                'actual_cost',
                'expected_revenue',
            ],

            [
                ['name' => 'objective', 'displayParams' => ['rows' => 8, 'cols' => 80]],
            ],

            [
                ['name' => 'content', 'displayParams' => ['rows' => 8, 'cols' => 80]],
            ],

        ],
        'LBL_PANEL_ASSIGNMENT' => [
            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                    'displayParams' => ['htmlescape' => true],
                ],

                [
                    'name' => 'team_name', 'displayParams' => ['display' => true],
                ],
            ],
        ],
    ],


];
