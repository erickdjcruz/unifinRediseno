<?php

$dependencies['lev_Backlog']['monto_c_Visibility'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('id','name'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetVisibility',
            'params' => array(
                'target' => 'monto_c',
                'value' => 'false',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['aprueba_reactivacion_c_Visibility'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('id','name'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetVisibility',
            'params' => array(
                'target' => 'aprueba_reactivacion_c',
                'value' => 'false',
            ),
        ),
    ),
);
