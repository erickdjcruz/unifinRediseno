<?php
$popupMeta = array (
    'moduleMain' => 'pr_Procesos_Robina',
    'varName' => 'pr_Procesos_Robina',
    'orderBy' => 'pr_procesos_robina.name',
    'whereClauses' => array (
  'name' => 'pr_procesos_robina.name',
),
    'searchInputs' => array (
  0 => 'pr_procesos_robina_number',
  1 => 'name',
  2 => 'priority',
  3 => 'status',
),
    'listviewdefs' => array (
  'NAME' => 
  array (
    'type' => 'name',
    'readonly' => false,
    'default' => true,
    'label' => 'LBL_NAME',
    'width' => 10,
  ),
  'RFC' => 
  array (
    'readonly' => false,
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_RFC',
    'width' => 10,
  ),
  'TICKET' => 
  array (
    'readonly' => false,
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_TICKET',
    'width' => 10,
  ),
  'ESTATUS_PROCESADO' => 
  array (
    'readonly' => false,
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_ESTATUS_PROCESADO',
    'width' => 10,
  ),
  'ESTATUS_ROBINA' => 
  array (
    'readonly' => false,
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_ESTATUS_ROBINA',
    'width' => 10,
  ),
  'FECHA_EMISION' => 
  array (
    'readonly' => false,
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_FECHA_EMISION',
    'width' => 10,
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'link' => true,
    'type' => 'relate',
    'related_fields' => 
    array (
      0 => 'assigned_user_id',
    ),
    'label' => 'LBL_ASSIGNED_TO',
    'id' => 'ASSIGNED_USER_ID',
    'width' => 10,
    'default' => true,
  ),
  'DATE_MODIFIED' => 
  array (
    'type' => 'datetime',
    'studio' => 
    array (
      'portaleditview' => false,
    ),
    'readonly' => true,
    'label' => 'LBL_DATE_MODIFIED',
    'width' => 10,
    'default' => true,
  ),
),
);
