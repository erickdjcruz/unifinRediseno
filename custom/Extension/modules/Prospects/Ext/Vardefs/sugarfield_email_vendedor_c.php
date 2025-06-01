<?php
 // created: 2025-05-31 16:54:10
$dictionary['Prospect']['fields']['email_vendedor_c']['labelValue']='Email del vendedor';
$dictionary['Prospect']['fields']['email_vendedor_c']['full_text_search']=array (
  'enabled' => '0',
  'boost' => '1',
  'searchable' => false,
);
$dictionary['Prospect']['fields']['email_vendedor_c']['enforced']='';
$dictionary['Prospect']['fields']['email_vendedor_c']['dependency']='and(equal($origen_c,"12"),equal($detalle_origen_c,"116"))';
$dictionary['Prospect']['fields']['email_vendedor_c']['required_formula']='';
$dictionary['Prospect']['fields']['email_vendedor_c']['readonly_formula']='';

 ?>