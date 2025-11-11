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

// A simple example class
class PingApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'ping' => [
                'reqType' => 'GET',
                'path' => ['ping'],
                'pathVars' => [''],
                'method' => 'ping',
                'shortHelp' => 'An example API only responds with pong',
                'longHelp' => 'include/api/help/ping_get_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'pingWithTime' => [
                'reqType' => 'GET',
                'path' => ['ping', 'whattimeisit'],
                'pathVars' => ['', 'sub_method'],
                'method' => 'ping',
                'shortHelp' => 'An example API only responds with the current time in server format.',
                'longHelp' => 'include/api/help/ping_whattimeisit_get_help.html',
            ],
        ];
    }

    public function registerApiSoap()
    {
        return [
            'functions' => [
                'ping' => [
                    'methodName' => 'ping',
                    'requestVars' => [
                    ],
                    'returnVars' => [
                        'xsd:string',
                    ],
                    'method' => 'ping',
                    'shortHelp' => 'Sample/test API that only responds with pong',
                ],
                'pingWithTime' => [
                    'methodName' => 'pingTime',
                    'requestVars' => [
                    ],
                    'extraVars' => [
                        'sub_method' => 'whattimeisit',
                    ],
                    'returnVars' => [
                        'xsd:string',
                    ],
                    'method' => 'ping',
                    'shortHelp' => 'Sample/test API that responds with the curernt date/time',
                ],
            ],
            'types' => [],
        ];
    }

    public function ping(ServiceBase $api, array $args)
    {
        if (isset($args['sub_method']) && $args['sub_method'] == 'whattimeisit') {
            $dt = new SugarDateTime();
            $td = new TimeDate();
            return $td->asIso($dt);
        }

        // Just a normal ping request
        return 'pong';
    }
}
