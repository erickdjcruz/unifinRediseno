<?php
 // created: 2025-11-04 19:20:40
$layout_defs["lev_Backlog"]["subpanel_setup"]['bkl_backlog_compromiso_lev_backlog'] = array (
  'order' => 100,
  'module' => 'Bkl_Backlog_Compromiso',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_BKL_BACKLOG_COMPROMISO_LEV_BACKLOG_FROM_BKL_BACKLOG_COMPROMISO_TITLE',
  'get_subpanel_data' => 'bkl_backlog_compromiso_lev_backlog',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);
