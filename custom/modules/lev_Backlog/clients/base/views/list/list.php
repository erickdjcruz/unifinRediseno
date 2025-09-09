<?php
$module_name = 'lev_Backlog';
$viewdefs[$module_name] = 
array (
  'base' => 
  array (
    'view' => 
    array (
      'list' => 
      array (
        'panels' => 
        array (
          0 => 
          array (
            'label' => 'LBL_PANEL_1',
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'name',
                'label' => 'LBL_NAME',
                'default' => true,
                'enabled' => true,
                'link' => true,
              ),
              1 => 
              array (
                'name' => 'assigned_user_name',
                'label' => 'LBL_ASSIGNED_TO_NAME',
                'default' => true,
                'enabled' => true,
                'link' => true,
                'width' => '9',
              ),
              2 => 
              array (
                'name' => 'producto_c',
                'label' => 'LBL_PRODUCTO_C',
                'enabled' => true,
                'readonly' => false,
                'default' => true,
              ),
              3 => 
              array (
                'name' => 'estatus_operacion_c',
                'label' => 'LBL_ESTATUS_OPERACION_C',
                'enabled' => true,
                'default' => true,
              ),
              4 => 
              array (
                'name' => 'estatus_backlog_c',
                'label' => 'LBL_ESTATUS_BACKLOG_C',
                'enabled' => true,
                'readonly' => false,
                'default' => true,
              ),
              5 => 
              array (
                'name' => 'progreso',
                'label' => 'LBL_PROGRESO',
                'enabled' => true,
                'default' => true,
              ),
              6 => 
              array (
                'label' => 'LBL_DATE_MODIFIED',
                'enabled' => true,
                'default' => true,
                'name' => 'date_modified',
                'readonly' => true,
              ),
              7 => 
              array (
                'name' => 'mes',
                'label' => 'LBL_MES',
                'enabled' => true,
                'default' => false,
              ),
              8 => 
              array (
                'name' => 'rango_bl_c',
                'label' => 'LBL_RANGO_BL',
                'enabled' => true,
                'readonly' => false,
                'default' => false,
              ),
              9 => 
              array (
                'name' => 'team_name',
                'label' => 'LBL_TEAM',
                'default' => false,
                'enabled' => true,
                'width' => '9',
              ),
            ),
          ),
        ),
        'orderBy' => 
        array (
          'field' => 'date_modified',
          'direction' => 'desc',
        ),
      ),
    ),
  ),
);
