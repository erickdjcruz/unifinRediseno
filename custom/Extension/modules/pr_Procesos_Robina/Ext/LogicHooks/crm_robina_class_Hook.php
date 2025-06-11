<?php

$hook_array['after_save'][] = Array(
    1,
    'Envía documentos de Robina a Alfresco y Quantico',//Just a quick comment about the logic of it
    'custom/modules/pr_Procesos_Robina/crm_robina_class.php', //path to the logic hook
    'crm_robina_class', // name of the class
    'procesa_ticket_robina_function' // name of the function.
);