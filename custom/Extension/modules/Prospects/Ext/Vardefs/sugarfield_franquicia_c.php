<?php
 // created: 2025-02-05 13:22:36
$dictionary['Prospect']['fields']['franquicia_c']['labelValue']='Franquicia';
$dictionary['Prospect']['fields']['franquicia_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['franquicia_c']['enforced']='';
$dictionary['Prospect']['fields']['franquicia_c']['dependency']='and(equal($origen_c,"12"),or(equal($detalle_origen_c,"12"),equal($detalle_origen_c,"13"),equal($detalle_origen_c,"114")))';
$dictionary['Prospect']['fields']['franquicia_c']['required_formula']='';
$dictionary['Prospect']['fields']['franquicia_c']['readonly_formula']='';

 ?>