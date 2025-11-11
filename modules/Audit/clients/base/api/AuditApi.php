<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */


class AuditApi extends FilterApi
{
    public function registerApiRest()
    {
        return [
            'export_audit' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'audit', 'export'],
                'pathVars' => ['module', '', ''],
                'method' => 'exportAudit',
                'shortHelp' => 'Export Audit records for module',
                'minVersion' => '11.11',
                'longHelp' => 'include/api/help/audit_export_help.html',
            ],
            'view_change_log' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'audit'],
                'pathVars' => ['module', 'record', 'audit'],
                'method' => 'viewChangeLog',
                'shortHelp' => 'View audit log in record view',
                'minVersion' => '11.11',
                'longHelp' => 'include/api/help/audit_get_help.html',
            ],
        ];
    }

    public function viewChangeLog(ServiceBase $api, array $args)
    {
        global $focus;

        $this->requireArgs($args, ['module', 'record']);

        $focus = BeanFactory::getBean($args['module'], $args['record']);

        if (!$focus->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('no access to the bean');
        }

        $auditBean = BeanFactory::newBean('Audit');

        if (!isset($args['max_num'])) {
            return [
                'next_offset' => -1,
                'records' => $auditBean->getAuditLog($focus),
            ];
        } else {
            $options = $this->parseArguments($api, $args, $auditBean);
            $records = $auditBean->getAuditLogChunk($focus, $options);
            if ($options['limit'] > 0 && safeCount($records) > $options['limit']) {
                $next_offset = $options['limit'] + $options['offset'];
                array_pop($records);
            } else {
                $next_offset = -1;
            }
            return [
                'next_offset' => $next_offset,
                'records' => $records,
            ];
        }
    }

    public function exportAudit(ServiceBase $api, array $args)
    {
        global $focus;
        $this->requireArgs($args, ['module']);
        $focus = BeanFactory::getBean($args['module']);
        if (!$focus->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('no access to the bean');
        }
        $auditBean = BeanFactory::newBean('Audit');
        $this->defaultLimit = -1;
        $options = $this->parseArguments($api, $args, $auditBean);

        $records = $auditBean->getAuditLogChunk($focus, $options);
        if ($options['limit'] > 0 && safeCount($records) > $options['limit']) {
            $next_offset = $options['limit'] + $options['offset'];
            array_pop($records);
        } else {
            $next_offset = -1;
        }
        return [
            'next_offset' => $next_offset,
            'records' => $records,
        ];
    }
}
