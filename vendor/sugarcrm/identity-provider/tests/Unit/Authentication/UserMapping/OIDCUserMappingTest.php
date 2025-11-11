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

namespace Sugarcrm\IdentityProvider\Tests\Unit\Authentication\UserMapping;

use PHPUnit_Framework_TestCase;
use Sugarcrm\IdentityProvider\Authentication\UserMapping\OIDCUserMapping;

/**
 * @coversDefaultClass \Sugarcrm\IdentityProvider\Authentication\UserMapping\OIDCUserMapping
 */
class OIDCUserMappingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getMappingDataProvider(): array
    {
        return [
            'emptyMappingAndAttributes' => [
                'mappingConfig' => [],
                'attributes' => [],
                'expected' => [],
            ],
            'emptyMappingSomeAttributes' => [
                'mappingConfig' => [],
                'attributes' => ['attr1' => 'foo', 'attr2' => 'bar'],
                'expected' => [],
            ],
            'someMappingSomeAttributes' => [
                'mappingConfig' => ['attr1' => 'email', 'attr2' => 'title'],
                'attributes' => ['attr1' => 'foo', 'attr2' => 'bar'],
                'expected' => ['email' => 'foo', 'title' => 'bar'],
            ],
            'nonMappedAttributesAreIgnored' => [
                'mappingConfig' => ['attr1' => 'email'],
                'attributes' => ['attr1' => 'foo', 'attr2' => 'bar'],
                'expected' => ['email' => 'foo'],
            ],
            'missingAttributesCanNotBePresentInResult' => [
                'mappingConfig' => ['attr1' => 'email', 'attr2' => 'title'],
                'attributes' => ['attr1' => 'foo', 'attr2' => 'bar', 'attr3' => 'baz'],
                'expected' => ['email' => 'foo', 'title' => 'bar'],
            ],
            'moreThenOneDepth' => [
                'mappingConfig' => [
                    'email' => 'email',
                    'attr1' => 'title',
                    'ln' => 'attr.last_name',
                    'fn' => 'attr.first_name',
                ],
                'attributes' => [
                    'attr1' => 'foo',
                    'ln' => 'Bobby',
                    'fn' => 'Foo',
                ],
                'expected' => [
                    'title' => 'foo',
                    'attr' => [
                        'last_name' => 'Bobby',
                        'first_name' => 'Foo',
                    ],
                ],
            ],
            'moreThenOneDepthWithCustomAttributes' => [
                'mappingConfig' => [
                    'email' => 'email',
                    'attr1' => 'title',
                    'ln' => 'attr.last_name',
                    'fn' => 'attr.first_name',
                    'title' => 'custom_attributes.title',
                    'customAddr' => 'custom_attributes.addr.street',
                ],
                'attributes' => [
                    'attr1' => 'foo',
                    'ln' => 'Bobby',
                    'fn' => 'Foo',
                    'title' => 'Senior Account Rep',
                    'customAddr' => 'Wall Street',
                ],
                'expected' => [
                    'title' => 'foo',
                    'attr' => [
                        'last_name' => 'Bobby',
                        'first_name' => 'Foo',
                    ],
                    'custom_attributes' => [
                        ['name' => 'title', 'value' => 'Senior Account Rep'],
                        ['name' => 'addr.street', 'value' => 'Wall Street'],
                    ],
                ],
            ],
            'moreThenOneDepthWithCustomAttributes' => [
                'mappingConfig' => [
                    'email' => 'email',
                    'attr1' => 'title',
                    'ln' => 'attr.last_name',
                    'fn' => 'attr.first_name',
                    'title' => 'custom_attributes.title',
                    'customAddr' => 'custom_attributes.addr.street',
                ],
                'attributes' => [
                    'attr1' => 'foo',
                    'ln' => 'Bobby',
                    'fn' => 'Foo',
                    'title' => 'Senior Account Rep',
                    'customAddr' => 'Wall Street',
                ],
                'expected' => [
                    'title' => 'foo',
                    'attr' => [
                        'last_name' => 'Bobby',
                        'first_name' => 'Foo',
                    ],
                    'custom_attributes' => [
                        ['name' => 'title', 'value' => 'Senior Account Rep'],
                        ['name' => 'addr.street', 'value' => 'Wall Street'],
                    ],
                ],
            ],
            'complexAttributes' => [
                'mappingConfig' => [
                    'given_name' => 'attributes.given_name',
                    'family_name' => 'attributes.family_name',
                    'email' => 'attributes.email',
                    'phone_number' => 'attributes.phone_number',
                    'address.street_address' => 'attributes.address.street_address',
                    'address.locality' => 'attributes.address.locality',
                    'address.region' => 'attributes.address.region',
                    'address.postal_code' => 'attributes.address.postal_code',
                    'address.country' => 'attributes.address.country',
                ],
                'attributes' => [
                    'given_name' => 'Bobby',
                    'address' => [
                        'street_address' => 'str',
                        'locality' => 'loc',
                    ],
                ],
                'expected' => [
                    'attributes' => [
                        'given_name' => 'Bobby',
                        'address' => [
                            'street_address' => 'str',
                            'locality' => 'loc',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @covers ::map
     * @dataProvider getMappingDataProvider
     *
     * @param array $mappingConfig App mappings from configuration.
     * @param array $attributes Attributes from OIDC IdP response.
     * @param array $expected Expected map result.
     */
    public function testMap(array $mappingConfig, array $attributes, array $expected): void
    {
        $mapping = new OIDCUserMapping($mappingConfig);
        $result = $mapping->map($attributes);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::mapIdentity
     */
    public function testMapIdentity(): void
    {
        $mapping = new OIDCUserMapping([]);
        $result = $mapping->mapIdentity(['sub' => 'identity']);
        $this->assertEquals('sub', $result['field']);
        $this->assertEquals('identity', $result['value']);
    }
}
