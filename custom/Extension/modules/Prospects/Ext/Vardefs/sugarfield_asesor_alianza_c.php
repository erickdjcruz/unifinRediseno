<?php
 // created: 2025-01-09 17:24:26
$dictionary['Prospect']['fields']['asesor_alianza_c']['labelValue']='Asesor de la Alianza';
$dictionary['Prospect']['fields']['asesor_alianza_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['asesor_alianza_c']['enforced']='';
$dictionary['Prospect']['fields']['asesor_alianza_c']['dependency']='and(equal($origen_c,"12"),or(equal($detalle_origen_c,"12"),equal($detalle_origen_c,"13")))';
$dictionary['Prospect']['fields']['asesor_alianza_c']['required_formula']='';
$dictionary['Prospect']['fields']['asesor_alianza_c']['readonly_formula']='';

 ?>