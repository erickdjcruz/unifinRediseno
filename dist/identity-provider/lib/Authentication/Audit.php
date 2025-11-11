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

namespace Sugarcrm\IdentityProvider\Authentication;

use Psr\Log\LoggerInterface;
use Sugarcrm\IdentityProvider\Srn;

class Audit
{
    const auditTag = 'audit';

    /**
     * @var string
     */
    private $applicationSRN;

    /**
     * @var string
     */
    private $tenant;

    /**
     * @var Srn\Srn
     */
    private $tenantSRN;

    /**
     * @var Srn\Manager
     */
    private $managerSRN;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, string $tenantSRN, string $applicationSRN)
    {
        $this->logger = $logger;
        $this->tenant = $tenantSRN;
        $this->applicationSRN = $applicationSRN;
    }

    /**
     * @return Srn\Srn
     */
    private function getTenantSRN(): Srn\Srn
    {
        if (is_null($this->managerSRN)) {
            $this->tenantSRN = Srn\Converter::fromString($this->tenant);
        }
        return $this->tenantSRN;
    }

    /**
     * @return Srn\Manager
     */
    private function getManagerSRN(): Srn\Manager
    {
        if (is_null($this->managerSRN)) {
            $this->managerSRN = new Srn\Manager([
                'partition' => $this->getTenantSRN()->getPartition(),
                'region' => $this->getTenantSRN()->getRegion(),
            ]);
        }
        return $this->managerSRN;
    }

    /**
     * Audit user changes
     * @param string $msg
     * @param string $userId
     * @param array $from
     * @param array $to
     */
    public function audit(string $msg, string $userId, array $from, array $to): void
    {
        $changes = array_merge(
            $this->changes('', $this->filterUserKeys($from), $this->filterUserKeys($to)),
            $this->changes('Attributes', $from['attributes'] ?? [], $to['attributes'] ?? []),
            $this->changes('CustomAttributes', $from['custom_attributes'] ?? [], $to['custom_attributes'] ?? [])
        );

        $userName = Srn\Converter::toString(
            $this->getManagerSRN()->createUserSrn($this->getTenantSRN()->getTenantId(), $userId)
        );
        foreach ($changes as $change) {
            $context = array_merge(
                $change,
                [
                    "action" => $msg,
                    "userName" => $userName,
                    "subject" => $this->applicationSRN,
                    "client_id" => $this->applicationSRN,
                    "tags" => [self::auditTag],
                ]
            );
            $this->logger->info($msg, $context);
        }
    }

    private function filterUserKeys(array $user): array
    {
        return [
            'status' => key_exists('status', $user) ? $user['status'] === User::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE' : '',
            'user_type' => key_exists('user_type', $user) ? $user['user_type'] === User::USER_TYPE_ADMINISTRATOR ? 'ADMINISTRATOR' : 'REGULAR_USER' : '',
        ];
    }

    private function changes(string $path, array $from, array $to): array
    {
        $listChanges = [];
        $fromScalar = array_filter($from, 'is_scalar');
        $toScalar = array_filter($to, 'is_scalar');

        foreach (array_unique(array_merge(array_keys($fromScalar), array_keys($toScalar))) as $key) {
            $fromValue = $from[$key] ?? '';
            $toValue = $to[$key] ?? '';
            if (!array_key_exists($key, $from) || !array_key_exists($key, $to) || $fromValue != $toValue) {
                $listChanges[] = [
                    'field' => ltrim($path . '.' . $key, '.'),
                    'form' => $fromValue,
                    'to' => $toValue,
                ];
            }
        }

        $arrKeys = array_unique(array_merge(
            array_keys(array_filter($from, 'is_array')),
            array_keys(array_filter($to, 'is_array'))
        ));

        foreach ($arrKeys as $key) {
            $listChanges = array_merge(
                $listChanges,
                $this->changes($path . '.' . $key, $from[$key]??[], $to[$key]??[])
            );
        }
        return $listChanges;
    }
}
