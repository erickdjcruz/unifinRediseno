<?php
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