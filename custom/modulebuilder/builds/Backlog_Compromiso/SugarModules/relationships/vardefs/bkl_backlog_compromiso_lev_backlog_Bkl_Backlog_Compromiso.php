<?php
// created: 2025-11-04 19:20:40
$dictionary["Bkl_Backlog_Compromiso"]["fields"]["bkl_backlog_compromiso_lev_backlog"] = array (
  'name' => 'bkl_backlog_compromiso_lev_backlog',
  'type' => 'link',
  'relationship' => 'bkl_backlog_compromiso_lev_backlog',
  'source' => 'non-db',
  'module' => 'lev_Backlog',
  'bean_name' => 'lev_Backlog',
  'side' => 'right',
  'vname' => 'LBL_BKL_BACKLOG_COMPROMISO_LEV_BACKLOG_FROM_BKL_BACKLOG_COMPROMISO_TITLE',
  'id_name' => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
  'link-type' => 'one',
);
$dictionary["Bkl_Backlog_Compromiso"]["fields"]["bkl_backlog_compromiso_lev_backlog_name"] = array (
  'name' => 'bkl_backlog_compromiso_lev_backlog_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_BKL_BACKLOG_COMPROMISO_LEV_BACKLOG_FROM_LEV_BACKLOG_TITLE',
  'save' => true,
  'id_name' => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
  'link' => 'bkl_backlog_compromiso_lev_backlog',
  'table' => 'lev_backlog',
  'module' => 'lev_Backlog',
  'rname' => 'name',
);
$dictionary["Bkl_Backlog_Compromiso"]["fields"]["bkl_backlog_compromiso_lev_backloglev_backlog_ida"] = array (
  'name' => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
  'type' => 'id',
  'source' => 'non-db',
  'vname' => 'LBL_BKL_BACKLOG_COMPROMISO_LEV_BACKLOG_FROM_BKL_BACKLOG_COMPROMISO_TITLE_ID',
  'id_name' => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
  'link' => 'bkl_backlog_compromiso_lev_backlog',
  'table' => 'lev_backlog',
  'module' => 'lev_Backlog',
  'rname' => 'id',
  'reportable' => false,
  'side' => 'right',
  'massupdate' => false,
  'duplicate_merge' => 'disabled',
  'hideacl' => true,
);
