<?php
// Eduardo Carrasco Beltrán 2025-10-20 Actualiza correo en Onboarding
$hook_array['before_save'][] = Array(
    16,
    'Consume servicio de onboarding para actualziar correo',
    'custom/modules/Prospects/onboarding_email.php',
    'onboarding_c',
    'onboarding_f'
);