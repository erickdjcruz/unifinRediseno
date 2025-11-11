<?php

namespace Sugarcrm\IdentityProvider\Tests\Unit\League\OAuth2\Client\Provider\HttpBasicAuth;

use Sugarcrm\IdentityProvider\League\OAuth2\Client\Provider\HttpBasicAuth\HttpBasicAuthOptionProviderUrlEncoded;

/**
 * @coversDefaultClass \Sugarcrm\IdentityProvider\League\OAuth2\Client\Provider\HttpBasicAuth\HttpBasicAuthOptionProviderUrlEncoded
 */
class HttpBasicAuthOptionProviderUrlEncodedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provides data for testGetAccessTokenOptions
     * @return array
     */
    public function getAccessTokenOptionsProvider(): array
    {
        return [
            'ParamsWithoutBodyMethodGet' => [
                'method' => 'GET',
                'params' => [
                    'client_id' => "test:1:2:3",
                    'client_secret' => '123'
                ],
                'expected' => [
                    'headers' => [
                        'Authorization' => 'Basic dGVzdCUzQTElM0EyJTNBMzoxMjM=',
                        'content-type' => 'application/x-www-form-urlencoded',
                    ]
                ]
            ],
            'ParamsWithBodyMethodPost' => [
                'method' => 'POST',
                'params' => [
                    'client_id' => "test:1:2:3",
                    'client_secret' => '123',
                    'a' => 1,
                    'b' => 'abc',
                ],
                'expected' => [
                    'headers' => [
                        'Authorization' => 'Basic dGVzdCUzQTElM0EyJTNBMzoxMjM=',
                        'content-type' => 'application/x-www-form-urlencoded',
                    ],
                    'body' => 'a=1&b=abc'
                ]
            ],
            'ParamsWithBodyMethodGet' => [
                'method' => 'GET',
                'params' => [
                    'client_id' => "test:1:2:3",
                    'client_secret' => '123',
                    'a' => 1,
                    'b' => 'abc',
                ],
                'expected' => [
                    'headers' => [
                        'Authorization' => 'Basic dGVzdCUzQTElM0EyJTNBMzoxMjM=',
                        'content-type' => 'application/x-www-form-urlencoded',
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string $method
     * @param array $params
     * @param array $expected
     * @return void
     *
     * @dataProvider getAccessTokenOptionsProvider
     *
     * @covers ::getAccessTokenOptions
     */
    public function testGetAccessTokenOptions(string $method, array $params, array $expected): void
    {
        $optionsProvider = new HttpBasicAuthOptionProviderUrlEncoded();
        $this->assertEquals($expected, $optionsProvider->getAccessTokenOptions($method, $params));
    }
}
