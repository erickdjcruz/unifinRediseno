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

namespace Sugarcrm\IdentityProvider\Authentication\UserMapping;

use Sugarcrm\IdentityProvider\Authentication\User;

class OIDCUserMapping implements MappingInterface
{
    use FieldMapper;
    /**
     * IdP to App mapping fields.
     * @var array
     */
    protected $mapping = [];

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @inheritDoc
     */
    public function map($response)
    {
        $responseData = $this->prepareResponseData($response);
        return $this->mapEntry($responseData, $this->mapping);
    }

    /**
     * @param $response
     * @return array
     */
    protected function prepareResponseData($response): array
    {
        $responseData = [];
        foreach ($this->mapping as $idpKey => $appKey) {
            $searchKey = $idpKey;
            if ($this->isComplexAttribute($idpKey)) {
                $key = $this->getComplexAttributeKey($idpKey);
                if (array_key_exists($key, $response)) {
                    $responseData = array_merge($responseData, $this->prepareResponseData($response[$key]));
                }

                $searchKey = $this->getComplexAttributeName($idpKey);
            }
            if (array_key_exists($searchKey, $response)) {
                $responseData[$idpKey] = $response[$searchKey];
            }
        }

        return $responseData;
    }

    /**
     * @inheritDoc
     */
    public function mapIdentity($response)
    {
        $identityField = $this->getIdentityField();
        return [
            'field' => $identityField,
            'value' => $response[$identityField],
        ];
    }

    /**
     * @return string
     */
    protected function getIdentityField()
    {
        return 'sub';
    }

    /**
     * @inheritDoc
     */
    public function getIdentityValue(User $user)
    {
        $identityField = $this->getIdentityField();
        return $user->hasAttribute($identityField) ? $user->getAttribute($identityField) : null;
    }
}
