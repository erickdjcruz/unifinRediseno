<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/customer_journey_parent.php

// created: 2025-11-11 16:17:54
VardefManager::createVardef('Opportunities', 'Opportunity', [
                                'customer_journey_parent',
                        ]);
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_amount.php

 // created: 2025-11-11 16:18:07
$dictionary['Opportunity']['fields']['amount']['required']=false;
$dictionary['Opportunity']['fields']['amount']['audited']=false;
$dictionary['Opportunity']['fields']['amount']['massupdate']=false;
$dictionary['Opportunity']['fields']['amount']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['amount']['comments']='Unconverted amount of the opportunity';
$dictionary['Opportunity']['fields']['amount']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['amount']['duplicate_merge_dom_value']='1';
$dictionary['Opportunity']['fields']['amount']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['amount']['calculated']=true;
$dictionary['Opportunity']['fields']['amount']['formula']='rollupConditionalSum($revenuelineitems, "likely_case", "sales_stage", forecastSalesStages(true, false))';
$dictionary['Opportunity']['fields']['amount']['enforced']=true;
$dictionary['Opportunity']['fields']['amount']['readonly']=true;
$dictionary['Opportunity']['fields']['amount']['related_fields']=array (
  0 => 'currency_id',
  1 => 'base_rate',
);

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_probability.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['probability']['audited']=false;
$dictionary['Opportunity']['fields']['probability']['massupdate']=false;
$dictionary['Opportunity']['fields']['probability']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['probability']['comments']='The probability of closure';
$dictionary['Opportunity']['fields']['probability']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['probability']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['probability']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['probability']['reportable']=false;
$dictionary['Opportunity']['fields']['probability']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['probability']['min']=false;
$dictionary['Opportunity']['fields']['probability']['max']=false;
$dictionary['Opportunity']['fields']['probability']['disable_num_format']='';
$dictionary['Opportunity']['fields']['probability']['studio']=false;

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_closed_revenue_line_items.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['audited']=false;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['massupdate']=false;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['reportable']=true;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['min']=false;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['max']=false;
$dictionary['Opportunity']['fields']['closed_revenue_line_items']['disable_num_format']='';

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_sales_stage.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['sales_stage']['required']=false;
$dictionary['Opportunity']['fields']['sales_stage']['massupdate']=false;
$dictionary['Opportunity']['fields']['sales_stage']['hidemassupdate']=true;
$dictionary['Opportunity']['fields']['sales_stage']['comments']='Indication of progression towards closure';
$dictionary['Opportunity']['fields']['sales_stage']['importable']='false';
$dictionary['Opportunity']['fields']['sales_stage']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['sales_stage']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['sales_stage']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['sales_stage']['reportable']=false;
$dictionary['Opportunity']['fields']['sales_stage']['calculated']=false;
$dictionary['Opportunity']['fields']['sales_stage']['dependency']=false;
$dictionary['Opportunity']['fields']['sales_stage']['visibility_grid']=array (
);

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_worst_case.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['worst_case']['audited']=false;
$dictionary['Opportunity']['fields']['worst_case']['massupdate']=false;
$dictionary['Opportunity']['fields']['worst_case']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['worst_case']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['worst_case']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['worst_case']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['worst_case']['calculated']=true;
$dictionary['Opportunity']['fields']['worst_case']['formula']='rollupConditionalSum($revenuelineitems, "worst_case", "sales_stage", forecastSalesStages(true, false))';
$dictionary['Opportunity']['fields']['worst_case']['enforced']=true;
$dictionary['Opportunity']['fields']['worst_case']['readonly']=true;
$dictionary['Opportunity']['fields']['worst_case']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['worst_case']['related_fields']=array (
  0 => 'currency_id',
  1 => 'base_rate',
);

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/dupe_check.ext.php

$dictionary['Opportunity']['fields']['revenuelineitems']['workflow'] = true;
$dictionary['Opportunity']['duplicate_check']['FilterDuplicateCheck']['filter_template'][0]['$and'][1] = array('sales_status' => array('$not_equals' => 'Closed Lost'));
$dictionary['Opportunity']['duplicate_check']['FilterDuplicateCheck']['filter_template'][0]['$and'][2] = array('sales_status' => array('$not_equals' => 'Closed Won'));
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_commit_stage.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['commit_stage']['audited']=false;
$dictionary['Opportunity']['fields']['commit_stage']['massupdate']=false;
$dictionary['Opportunity']['fields']['commit_stage']['hidemassupdate']=true;
$dictionary['Opportunity']['fields']['commit_stage']['options']='';
$dictionary['Opportunity']['fields']['commit_stage']['comments']='Forecast commit ranges: Include, Likely, Omit etc.';
$dictionary['Opportunity']['fields']['commit_stage']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['commit_stage']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['commit_stage']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['commit_stage']['calculated']=false;
$dictionary['Opportunity']['fields']['commit_stage']['formula']='';
$dictionary['Opportunity']['fields']['commit_stage']['dependency']=false;
$dictionary['Opportunity']['fields']['commit_stage']['related_fields']=array (
);
$dictionary['Opportunity']['fields']['commit_stage']['visibility_grid']=array (
);

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_service_start_date.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['service_start_date']['audited']=false;
$dictionary['Opportunity']['fields']['service_start_date']['massupdate']=false;
$dictionary['Opportunity']['fields']['service_start_date']['hidemassupdate']=true;
$dictionary['Opportunity']['fields']['service_start_date']['comments']='Service start date field.';
$dictionary['Opportunity']['fields']['service_start_date']['importable']='false';
$dictionary['Opportunity']['fields']['service_start_date']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['service_start_date']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['service_start_date']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['service_start_date']['calculated']=false;
$dictionary['Opportunity']['fields']['service_start_date']['related_fields']=array (
);
$dictionary['Opportunity']['fields']['service_start_date']['enable_range_search']=false;

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_total_revenue_line_items.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['total_revenue_line_items']['audited']=false;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['massupdate']=false;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['total_revenue_line_items']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['total_revenue_line_items']['reportable']=true;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['min']=false;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['max']=false;
$dictionary['Opportunity']['fields']['total_revenue_line_items']['disable_num_format']='';

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_best_case.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['best_case']['audited']=false;
$dictionary['Opportunity']['fields']['best_case']['massupdate']=false;
$dictionary['Opportunity']['fields']['best_case']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['best_case']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['best_case']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['best_case']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['best_case']['calculated']=true;
$dictionary['Opportunity']['fields']['best_case']['formula']='rollupConditionalSum($revenuelineitems, "best_case", "sales_stage", forecastSalesStages(true, false))';
$dictionary['Opportunity']['fields']['best_case']['enforced']=true;
$dictionary['Opportunity']['fields']['best_case']['readonly']=true;
$dictionary['Opportunity']['fields']['best_case']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['best_case']['related_fields']=array (
  0 => 'currency_id',
  1 => 'base_rate',
);

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_sales_status.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['sales_status']['massupdate']=true;
$dictionary['Opportunity']['fields']['sales_status']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['sales_status']['importable']=true;
$dictionary['Opportunity']['fields']['sales_status']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['sales_status']['reportable']=true;
$dictionary['Opportunity']['fields']['sales_status']['calculated']=false;
$dictionary['Opportunity']['fields']['sales_status']['dependency']=false;
$dictionary['Opportunity']['fields']['sales_status']['visibility_grid']=array (
);
$dictionary['Opportunity']['fields']['sales_status']['studio']=true;

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_closed_won_revenue_line_items.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['audited']=false;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['massupdate']=false;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['hidemassupdate']=false;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['reportable']=true;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['min']=false;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['max']=false;
$dictionary['Opportunity']['fields']['closed_won_revenue_line_items']['disable_num_format']='';

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/sugarfield_date_closed.php

 // created: 2025-11-11 16:18:08
$dictionary['Opportunity']['fields']['date_closed']['required']=false;
$dictionary['Opportunity']['fields']['date_closed']['audited']=false;
$dictionary['Opportunity']['fields']['date_closed']['massupdate']=false;
$dictionary['Opportunity']['fields']['date_closed']['hidemassupdate']=true;
$dictionary['Opportunity']['fields']['date_closed']['comments']='Expected or actual date the oppportunity will close';
$dictionary['Opportunity']['fields']['date_closed']['importable']='false';
$dictionary['Opportunity']['fields']['date_closed']['duplicate_merge']='enabled';
$dictionary['Opportunity']['fields']['date_closed']['duplicate_merge_dom_value']=1;
$dictionary['Opportunity']['fields']['date_closed']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['date_closed']['calculated']=false;
$dictionary['Opportunity']['fields']['date_closed']['related_fields']=array (
);

 
?>
<?php
// Merged from custom/Extension/modules/Opportunities/Ext/Vardefs/rli_link_workflow.php

$dictionary['Opportunity']['fields']['revenuelineitems']['workflow'] = true;
?>
