<?php
// created: 2025-11-04 19:44:42
$viewdefs['Bkl_Backlog_Compromiso']['base']['view']['subpanel-for-lev_backlog-bkl_backlog_compromiso_lev_backlog'] = array (
  'panels' => 
  array (
    0 => 
    array (
      'name' => 'panel_header',
      'label' => 'LBL_PANEL_1',
      'fields' => 
      array (
        0 => 
        array (
          'label' => 'LBL_NAME',
          'enabled' => true,
          'default' => true,
          'name' => 'name',
          'link' => true,
        ),
        1 => 
        array (
          'name' => 'fecha_compromiso',
          'label' => 'LBL_FECHA_COMPROMISO',
          'enabled' => true,
          'readonly' => false,
          'default' => true,
        ),
        2 => 
        array (
          'name' => 'monto_original',
          'label' => 'LBL_MONTO_ORIGINAL',
          'enabled' => true,
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'readonly' => false,
          'currency_format' => true,
          'default' => true,
        ),
        3 => 
        array (
          'name' => 'monto_modificado',
          'label' => 'LBL_MONTO_MODIFICADO',
          'enabled' => true,
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'readonly' => false,
          'currency_format' => true,
          'default' => true,
        ),
        4 => 
        array (
          'name' => 'monto_real',
          'label' => 'LBL_MONTO_REAL',
          'enabled' => true,
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'readonly' => false,
          'currency_format' => true,
          'default' => true,
        ),
        5 => 
        array (
          'name' => 'diferencia',
          'label' => 'LBL_DIFERENCIA',
          'enabled' => true,
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'readonly' => false,
          'currency_format' => true,
          'default' => true,
        ),
        6 => 
        array (
          'label' => 'LBL_DATE_MODIFIED',
          'enabled' => true,
          'default' => true,
          'name' => 'date_modified',
        ),
      ),
    ),
  ),
  'orderBy' => 
  array (
    'field' => 'date_modified',
    'direction' => 'desc',
  ),
  'type' => 'subpanel-list',
);