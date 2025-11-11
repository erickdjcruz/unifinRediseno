<?php

$dictionary['system_process_lock'] = [
    'table' => 'system_process_lock',
    'archive' => false,
    'comment' => 'Stores isolated active processes. It helps to prevent race conditions',
    'fields' => [
        'unique_id' => [
            'name' => 'unique_id',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
            'default' => '',
            'isnull' => false,
            'comment' => 'Unique identifier',
        ],
        'additional_key' => [
            'name' => 'additional_key',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
            'default' => '',
            'isnull' => false,
            'comment' => 'May be used in combination with the Unique identifier',
        ],
        'date_expires' => [
            'name' => 'date_expires',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Record expiration date',
        ],
    ],
    'indices' => [
        [
            'name' => 'system_process_lock_pk',
            'type' => 'primary',
            'fields' => ['unique_id', 'additional_key'],
        ],
    ],
];
