<?php
 // created: 2025-06-27 02:09:42
$dictionary['Prospect']['fields']['telefono_vendedor_c']['labelValue']='Teléfono del vendedor';
$dictionary['Prospect']['fields']['telefono_vendedor_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['telefono_vendedor_c']['enforced']='';
$dictionary['Prospect']['fields']['telefono_vendedor_c']['dependency']='and(equal($origen_c,"12"),equal($detalle_origen_c,"116"))';
$dictionary['Prospect']['fields']['telefono_vendedor_c']['required_formula']='';
$dictionary['Prospect']['fields']['telefono_vendedor_c']['readonly_formula']='';

 ?>