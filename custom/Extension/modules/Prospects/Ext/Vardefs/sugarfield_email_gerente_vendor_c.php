<?php
 // created: 2025-05-31 16:51:49
$dictionary['Prospect']['fields']['email_gerente_vendor_c']['labelValue']='Email del F&I o Gerente de crédito';
$dictionary['Prospect']['fields']['email_gerente_vendor_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['email_gerente_vendor_c']['enforced']='';
$dictionary['Prospect']['fields']['email_gerente_vendor_c']['dependency']='and(equal($origen_c,"12"),equal($detalle_origen_c,"116"))';
$dictionary['Prospect']['fields']['email_gerente_vendor_c']['required_formula']='';
$dictionary['Prospect']['fields']['email_gerente_vendor_c']['readonly_formula']='';

 ?>