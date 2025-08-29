<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class ProspectEmailValidationApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'validateEmailProspect' => [
                'reqType' => 'POST',
                'path' => ['Prospects', 'validateEmail'],
                'pathVars' => ['', ''],
                'method' => 'validateEmail',
                'shortHelp' => 'Valida si un correo ya existe en otro prospect',
                'longHelp' => '',
            ],
        ];
    }

    public function validateEmail($api, $args)
    {
        global $db;

        if (empty($args['emails']) || !is_array($args['emails'])) {
            throw new SugarApiExceptionMissingParameter('Parámetro "emails" requerido.');
        }

        $prospectId = !empty($args['record_id']) ? $db->quote($args['record_id']) : '';
        $emails = array_map(function ($e) use ($db) {
            return $db->quote(trim($e));
        }, $args['emails']);

        $emails_found = []; 

        foreach ($emails as $email) {
            $query = "SELECT p.id AS id_po, pc.clean_name_c AS nombre, ea.email_address
            FROM prospects p
            INNER JOIN prospects_cstm pc ON p.id = pc.id_c
            INNER JOIN email_addr_bean_rel eabr ON eabr.bean_id = p.id AND eabr.deleted = 0
            INNER JOIN email_addresses ea ON ea.id = eabr.email_address_id and ea.deleted =0
            WHERE ea.email_address = '{$email}' and pc.excluye_campana_c = 0 ; ";

            $result = $db->query($query);
            while ($row = $db->fetchByAssoc($result)) {
                if (empty($prospectId) || $row['id_po'] !== $prospectId) {
                    $correo = $row['email_address'];
                    if (!isset($emails_found[$correo])) {
                        $emails_found[$correo] = [];
                    }
                    $emails_found[$correo][] = [
                        'id' => $row['id_po'],
                        'nombre' => $row['nombre']
                    ];
                }
            }
        }

        return [
            'duplicate' => !empty($emails_found),
            'emails' => $emails_found
        ];
    }
}
