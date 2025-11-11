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

namespace Sugarcrm\IdentityProvider\Tests\Unit\Utils;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

use Sugarcrm\IdentityProvider\Utils\RetryHttpClientBuilder;

/**
 * @coversDefaultClass Sugarcrm\IdentityProvider\Utils\RetryHttpClientBuilder
 */
class RetryHttpClientBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createHttpClient
     */
    public function testCreateHttpClientDefault()
    {
        $client = RetryHttpClientBuilder::getClient([]);
        $this->assertInstanceOf(ClientInterface::class, $client);
        $config = $client->getConfig();
        $this->assertArrayNotHasKey('proxy', $config);
    }

    /**
     * @covers ::createHttpClient
     */
    public function testCreateHttpClientWithHeaders()
    {
        $client = RetryHttpClientBuilder::getClient(['headers' => ['User-Agent'=>'SugarCRM 12.0']]);
        $this->assertInstanceOf(ClientInterface::class, $client);
        $config = $client->getConfig();
        $this->assertArrayHasKey('headers', $config);
        $this->assertEquals('SugarCRM 12.0', $config['headers']['User-Agent']);
    }

    /**
     * @return array
     */
    public function delayExponentialProvider()
    {
        return [
            [0, 0],
            [1, 1000],
            [2, 2000],
            [3, 4000],
            [4, 8000],
            [5, 16000],
        ];
    }

    /**
     * @covers ::retryDelayExponential
     * @param int $attempt
     * @param int $delay
     *
     * @dataProvider delayExponentialProvider
     */
    public function testRetryDelayExponential($attempt, $delay)
    {
        $function = RetryHttpClientBuilder::retryDelayExponential();
        $this->assertEquals($delay, $function($attempt));
    }


    /**
     * @return array
     */
    public function delayLinearProvider()
    {
        return [
            [0, 0],
            [1, 1000],
            [2, 2000],
            [3, 3000],
            [4, 4000],
            [5, 5000],
        ];
    }

    /**
     * @covers ::retryDelayLinear
     * @param int $attempt
     * @param int $delay
     *
     * @dataProvider delayLinearProvider
     */
    public function testRetryDelayLinear($attempt, $delay)
    {
        $function = RetryHttpClientBuilder::retryDelayLinear();
        $this->assertEquals($delay, $function($attempt));
    }

    /**
     * @return array
     */
    public function getDelayStrategyProvider()
    {
        return [
            [[], 5000],
            [
                [
                    'delay_strategy' => '',
                ],
                5000,
            ],
            [
                [
                    'delay_strategy' => 'linear',
                ],
                5000,
            ],
            [
                [
                    'delay_strategy' => 'exponential',
                ],
                16000,
            ],
            [
                [
                    'delay_strategy' => 'some_weird_unknown_strategy',
                ],
                5000,
            ],
        ];
    }

    /**
     * @covers ::getDelayStrategy
     * @param array $config
     * @param int $expectedDelay for attempt = 5
     *
     * @dataProvider getDelayStrategyProvider
     */
    public function testGetDelayStrategy($config, $expectedDelay)
    {
        $function = RetryHttpClientBuilder::getDelayStrategy($config);
        $this->assertEquals($expectedDelay, $function(5));
    }

    /**
     * @return array
     */
    public function retryDeciderProvider()
    {
        return [
            'ZeroMaxAttemptsZeroAttempt' => [0, 0, 500, false],
            'ZeroMaxAttempts' => [0, 2, 500, false],
            'MaxAttemptsMoreThanCurrentAttempt' => [3, 2, 500, true],
            'MaxAttemptsLessThanCurrentAttempt' => [2, 3, 500, false],
            'MaxAttemptsEqualsCurrentAttempt' => [3, 3, 500, false], // it's a zero-based start
            'Code102' => [2, 0, 102, false],
            'Code10' => [2, 0, 200, false],
            'Code404' => [2, 0, 404, false],
            'Code302' => [2, 0, 302, false],
            'Code425TooEarly' => [2, 0, 425, true],
            'Code429TooManyRequests' => [2, 0, 429, true],
            'Code500' => [2, 0, 500, true],
            'Code502' => [2, 0, 502, true],
            'Code503' => [2, 0, 504, true],
        ];
    }

    /**
     * @covers ::retryDecider
     * @param int $attempts
     * @param int $currentAttempt
     * @param int $responseCode
     * @param bool $continueRetry
     *
     * @dataProvider retryDeciderProvider
     */
    public function testRetryDecider($attempts, $currentAttempt, $responseCode, $continueRetry)
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response->method('getStatusCode')->willReturn($responseCode);

        $decider = RetryHttpClientBuilder::retryDecider($attempts);
        $this->assertEquals($continueRetry, $decider($currentAttempt, $request, $response));
    }
}
