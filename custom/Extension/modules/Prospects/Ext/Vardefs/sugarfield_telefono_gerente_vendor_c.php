<?php
 // created: 2025-06-27 02:08:18
$dictionary['Prospect']['fields']['telefono_gerente_vendor_c']['labelValue']='Teléfono del F&I o Gerente de crédito';
$dictionary['Prospect']['fields']['telefono_gerente_vendor_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['telefono_gerente_vendor_c']['enforced']='';
$dictionary['Prospect']['fields']['telefono_gerente_vendor_c']['dependency']='and(equal($origen_c,"12"),equal($detalle_origen_c,"116"))';
$dictionary['Prospect']['fields']['telefono_gerente_vendor_c']['required_formula']='';
$dictionary['Prospect']['fields']['telefono_gerente_vendor_c']['readonly_formula']='';

 ?>