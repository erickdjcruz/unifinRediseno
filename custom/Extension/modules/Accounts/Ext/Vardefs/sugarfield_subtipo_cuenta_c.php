<?php
 // created: 2025-09-24 12:21:46
$dictionary['Account']['fields']['subtipo_cuenta_c']['labelValue']='Subtipo de Cuenta';
$dictionary['Account']['fields']['subtipo_cuenta_c']['enforced']='';
$dictionary['Account']['fields']['subtipo_cuenta_c']['dependency']='';
$dictionary['Account']['fields']['subtipo_cuenta_c']['required_formula']='';
$dictionary['Account']['fields']['subtipo_cuenta_c']['readonly_formula']='';
$dictionary['Account']['fields']['subtipo_cuenta_c']['visibility_grid']=array (
  'trigger' => 'tipo_registro_c',
  'values' => 
  array (
    'Lead' => 
    array (
      0 => 'En Calificacion',
      1 => 'No Viable',
    ),
    'Prospecto' => 
    array (
      0 => 'Contactado',
      1 => 'Interesado',
      2 => 'Integracion de Expediente',
      3 => 'Credito',
      4 => 'Rechazado',
    ),
    'Cliente' => 
    array (
      0 => 'Venta Activo',
      1 => 'Linea',
      2 => 'Nuevo',
      3 => 'Unifin',
      4 => 'Inactivo',
      5 => 'Dormido',
      6 => 'Perdido',
      7 => 'Credito Simple',
      8 => 'Con Linea Vigente',
      9 => 'Con Linea Vencida',
      10 => 'Con mas de un ano sin Operar',
      11 => 'Con Linea No Vigente',
    ),
    'Persona' => 
    array (
    ),
    'Proveedor' => 
    array (
    ),
  ),
);

 ?>