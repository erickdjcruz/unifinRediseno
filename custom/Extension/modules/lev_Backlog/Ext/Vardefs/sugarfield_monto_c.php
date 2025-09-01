<?php
 // created: 2025-08-30 05:36:36
$dictionary['lev_Backlog']['fields']['monto_c']['duplicate_merge_dom_value']=0;
$dictionary['lev_Backlog']['fields']['monto_c']['labelValue']='Monto';
$dictionary['lev_Backlog']['fields']['monto_c']['calculated']='1';
$dictionary['lev_Backlog']['fields']['monto_c']['formula']='related($lev_backlog_opportunities,"monto_c")';
$dictionary['lev_Backlog']['fields']['monto_c']['enforced']='1';
$dictionary['lev_Backlog']['fields']['monto_c']['dependency']='';
$dictionary['lev_Backlog']['fields']['monto_c']['related_fields']=array (
  0 => 'currency_id',
  1 => 'base_rate',
);
$dictionary['lev_Backlog']['fields']['monto_c']['required_formula']='';
$dictionary['lev_Backlog']['fields']['monto_c']['readonly_formula']='';
 ?>