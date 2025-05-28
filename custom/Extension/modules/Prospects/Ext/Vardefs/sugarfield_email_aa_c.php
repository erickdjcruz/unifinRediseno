<?php
 // created: 2025-04-19 20:26:42
$dictionary['Prospect']['fields']['email_aa_c']['labelValue']='Email del Asesor de Alianza';
$dictionary['Prospect']['fields']['email_aa_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['email_aa_c']['enforced']='';
$dictionary['Prospect']['fields']['email_aa_c']['dependency']='and(equal($origen_c,"12"),or(equal($detalle_origen_c,"12"),equal($detalle_origen_c,"13"),equal($detalle_origen_c,"114"),equal($detalle_origen_c,"115")))';
$dictionary['Prospect']['fields']['email_aa_c']['required_formula']='';
$dictionary['Prospect']['fields']['email_aa_c']['readonly_formula']='';

 ?>