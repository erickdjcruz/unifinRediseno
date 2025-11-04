<?php
// created: 2025-11-04 19:44:41
$subpanel_layout['list_fields'] = array (
  'name' => 
  array (
    'vname' => 'LBL_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'width' => 10,
    'default' => true,
  ),
  'fecha_compromiso' => 
  array (
    'readonly' => false,
    'type' => 'date',
    'vname' => 'LBL_FECHA_COMPROMISO',
    'width' => 10,
    'default' => true,
  ),
  'monto_original' => 
  array (
    'readonly' => false,
    'type' => 'currency',
    'default' => true,
    'related_fields' => 
    array (
      0 => 'currency_id',
      1 => 'base_rate',
    ),
    'vname' => 'LBL_MONTO_ORIGINAL',
    'currency_format' => true,
    'width' => 10,
  ),
  'monto_modificado' => 
  array (
    'readonly' => false,
    'type' => 'currency',
    'default' => true,
    'related_fields' => 
    array (
      0 => 'currency_id',
      1 => 'base_rate',
    ),
    'vname' => 'LBL_MONTO_MODIFICADO',
    'currency_format' => true,
    'width' => 10,
  ),
  'monto_real' => 
  array (
    'readonly' => false,
    'type' => 'currency',
    'default' => true,
    'related_fields' => 
    array (
      0 => 'currency_id',
      1 => 'base_rate',
    ),
    'vname' => 'LBL_MONTO_REAL',
    'currency_format' => true,
    'width' => 10,
  ),
  'diferencia' => 
  array (
    'readonly' => false,
    'type' => 'currency',
    'default' => true,
    'related_fields' => 
    array (
      0 => 'currency_id',
      1 => 'base_rate',
    ),
    'vname' => 'LBL_DIFERENCIA',
    'currency_format' => true,
    'width' => 10,
  ),
  'date_modified' => 
  array (
    'vname' => 'LBL_DATE_MODIFIED',
    'width' => 10,
    'default' => true,
  ),
);