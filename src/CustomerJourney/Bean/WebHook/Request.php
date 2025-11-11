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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\WebHook;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\ParseData as ParseData;
use Sugarcrm\Sugarcrm\Security\HttpClient\ExternalResourceClient;
use Sugarcrm\Sugarcrm\Security\HttpClient\RequestException;

class Request
{
    /**
     * @var \CJ_WebHook
     */
    private $webHook;

    /**
     * @param CJ_WebHook $webHook
     */
    public function __construct(\CJ_WebHook $webHook)
    {
        $this->webHook = $webHook;
    }

    /**
     * @param array $request
     * @throws \SugarApiException
     */
    public function send(array $request)
    {
        $this->debug("Sending request to {$this->webHook->url}");

        $client = new ExternalResourceClient();
        try {
            $this->debug('Sending request');
            if ($this->webHook->request_method !== 'GET') {
                $response = $client->post($this->webHook->url, $this->configureRequestBody($request), $this->configureHeaders());
            } else {
                $response = $client->get($this->webHook->url, $this->configureHeaders());
            }

            $statusCode = $response->getStatusCode();
            $this->debug("Response status code: $statusCode");

            if ($statusCode === 0 || $statusCode >= 400) {
                $jsonResponse = json_decode(preg_replace('/\s+/', ' ', $response->getBody()->getContents()), true);
                $this->handleError($jsonResponse, $statusCode);
            }
        } catch (RequestException $e) {
            $this->debug($e->getMessage());
            $this->handleCurlError($e->getCode(), $e->getMessage());
            return;
        }
        return $response->getBody()->getContents();
    }

    /**
     * @param string $message
     */
    private function debug($message)
    {
        $GLOBALS['log']->debug("CJ_WebHook\\Request: $message");
    }

    /**
     * @param string $message
     */
    private function fatal($message)
    {
        $GLOBALS['log']->fatal("CJ_WebHook\\Request: $message");
    }

    /**
     * @param $response
     * @return array
     */
    private function parseResponse(array $response)
    {
        switch ($this->webHook->response_format) {
            case \CJ_WebHook::RESPONSE_FORMAT_JSON:
                return $response;
            case \CJ_WebHook::RESPONSE_FORMAT_HTTP_QUERY:
            case \CJ_WebHook::RESPONSE_FORMAT_TEXT:
                return $response;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private function configureRequestBody(array $data)
    {
        if ($this->webHook->request_format === \CJ_WebHook::REQUEST_FORMAT_JSON) {
            if ($this->webHook->request_body === \CJ_WebHook::REQUEST_BODY_JOURNEY) {
                return json_encode($data);
            } elseif ($this->webHook->request_body === \CJ_WebHook::REQUEST_BODY_CUSTOM) {
                return $this->parseCustomPostBody($data);
            }
        } elseif ($this->webHook->request_format === \CJ_WebHook::REQUEST_FORMAT_HTTP_QUERY) {
            if ($this->webHook->request_body === \CJ_WebHook::REQUEST_BODY_JOURNEY) {
                return http_build_query($data);
            } elseif ($this->webHook->request_body === \CJ_WebHook::REQUEST_BODY_CUSTOM) {
                return $this->parseCustomPostBody($data);
            }
        }

        return '';
    }

    /**
     * @param array $data
     * @return string
     */
    private function parseCustomPostBody(array $data)
    {
        if (empty($this->webHook->custom_post_body)) {
            return;
        }

        $result = ParseData::parseVariables($this->webHook->custom_post_body);

        if (empty($result[0])) { //If there is no variable in custom post body then return it as it is
            return $this->webHook->custom_post_body;
        } else {
            $postBody = new CustomPostBodyVariables($this->webHook);
            $info = $postBody->parseModule($result, $data);
            return ParseData::replaceVariablesWithValues($info, $this->webHook->custom_post_body);
        }
    }

    /**
     * @return array
     */
    private function configureHeaders()
    {
        $returnHeaders = [];

        if ($this->webHook->request_format === \CJ_WebHook::REQUEST_FORMAT_JSON) {
            $returnHeaders['Content-Type'] = 'application/json';
        } elseif ($this->webHook->request_format === \CJ_WebHook::REQUEST_FORMAT_HTTP_QUERY) {
            $returnHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if (!empty($this->webHook->headers)) {
            $headers = explode("\n", trim($this->webHook->headers));
            foreach ($headers as $header) {
                $headerParts = explode(':', trim($header));
                if (is_array($headerParts)) {
                    $returnHeaders[$headerParts[0]] = $headerParts[1];
                }
            }
        }

        return $returnHeaders;
    }

    /**
     * @param array $response
     * @param int $httpCode
     * @throws \SugarApiException
     */
    private function handleError(array $response, int $httpCode)
    {
        $response = $this->parseResponse($response);

        if (is_string($response)) {
            $message = $response;
        } elseif (is_array($response)) {
            $message = $response['error']['message'];
            $status = $response['error']['status'];
        } else {
            $message = 'EXCEPTION_UNKNOWN_EXCEPTION';
        }

        $this->fatal("Error message: $message");

        if ($this->webHook->ignore_errors) {
            $this->debug('Error ignored');
            return;
        }

        $this->debug('Throwing error');
        throw new \SugarApiException(
            $status . ' Check Sugar Logs for more details',
            null,
            null,
            $httpCode ?: 500
        );
    }

    /**
     * @param int $errorNo
     * @param string $error
     * @throws \SugarApiExceptionError
     */
    private function handleCurlError($errorNo, $error)
    {
        $this->fatal("curl error ($errorNo): $error");

        if ($this->webHook->ignore_errors) {
            $this->debug('Error ignored');
            return;
        }

        throw new \SugarApiExceptionError("curl error ($errorNo): $error");
    }
}
