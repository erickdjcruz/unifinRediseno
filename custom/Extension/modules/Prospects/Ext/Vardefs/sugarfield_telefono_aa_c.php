<?php
 // created: 2025-02-19 16:55:38
$dictionary['Prospect']['fields']['telefono_aa_c']['labelValue']='Teléfono del Asesor de Alianza';
$dictionary['Prospect']['fields']['telefono_aa_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['telefono_aa_c']['enforced']='';
$dictionary['Prospect']['fields']['telefono_aa_c']['dependency']='and(equal($origen_c,"12"),or(equal($detalle_origen_c,"12"),equal($detalle_origen_c,"13"),equal($detalle_origen_c,"114")))';
$dictionary['Prospect']['fields']['telefono_aa_c']['required_formula']='';
$dictionary['Prospect']['fields']['telefono_aa_c']['readonly_formula']='';

 ?>