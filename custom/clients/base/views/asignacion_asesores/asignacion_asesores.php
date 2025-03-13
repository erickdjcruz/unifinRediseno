<?php
$viewdefs['base']['view']['asignacion_asesores'] = array(
    'panels' => array(
        array(
            'fields' => array(
                array(
                    'name' => 'users_accounts_1_name',
                    'label' => 'Asesor Actual',
                    'type' => 'relate',
                    'view' => 'edit',
                ),
            ),
        ),
    ),
    'panelsTo' => array(
    array(
        'fields' => array(
            array(
                'name' => 'asignar_a_promotor',
                'label' => 'Reasignar a: ',
                'type' => 'relate',
                'view' => 'edit',
            ),
        ),
    ),
)
);