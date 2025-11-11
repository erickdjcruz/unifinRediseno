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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\ServiceAccount;

use Psr\Log\LoggerInterface;
use Sugarcrm\IdentityProvider\Srn\Converter;
use Sugarcrm\Sugarcrm\SugarCloud\AuthZ;

class Checker
{
    protected $allowedSAs = [];
    protected $ownTenantSRN = '';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    protected $serviceAccountPermissions = [];

    /**
     * @var AuthZ
     */
    private $authZ;

    public function __construct(array $idmModeConfig, AuthZ $authZ, LoggerInterface $logger)
    {
        // @deprecated: allowedSAs and check against it will be removed in the future versions.
        $this->allowedSAs = $idmModeConfig['allowedSAs'] ?? [];
        $this->ownTenantSRN = $idmModeConfig['tid'] ?? '';
        $this->serviceAccountPermissions = $idmModeConfig['serviceAccountPermissions'] ?? [];

        $this->authZ = $authZ;

        $this->logger = $logger;
    }

    /**
     * @param string $accessToken
     * @param array $accessTokenInfo
     * @return bool
     */
    public function isAllowed(string $accessToken, array $accessTokenInfo): bool
    {
        $subjectSRN = $accessTokenInfo['sub'] ?? '';

        $tenantID = Converter::fromString($this->ownTenantSRN)->getTenantId();
        $saTokenSubjectTenantID = Converter::fromString($subjectSRN)->getTenantId();

        $tidFromClaims = $accessTokenInfo['ext']['tid'] ?? null;
        $isTokenForThisTenant = $tenantID === $saTokenSubjectTenantID || $tidFromClaims === $this->ownTenantSRN;
        $isAllowedByAuthZAndTenant = $isTokenForThisTenant && $this->authZ->checkPermission(
            $accessToken,
            $this->ownTenantSRN,
            $this->serviceAccountPermissions
        );

        $isAllowed = $isAllowedByAuthZAndTenant || in_array($subjectSRN, $this->allowedSAs);

        if (!$isAllowed && !$isTokenForThisTenant) {
            $this->logger->error(
                sprintf(
                    'Service account with tenant (%s, %s) claims should be equal to %s',
                    $saTokenSubjectTenantID,
                    $tidFromClaims,
                    $tenantID
                )
            );
        }

        return $isAllowed;
    }
}
