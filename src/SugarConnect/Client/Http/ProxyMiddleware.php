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

namespace Sugarcrm\Sugarcrm\SugarConnect\Client\Http;

use Psr\Http\Message\RequestInterface;

/**
 * @deprecated Will be removed in the next release.
 */
class ProxyMiddleware
{
    /**
     * Guzzle middleware invocation to add a proxy url.
     *
     * @param callable $handler The next handler to invoke from the middleware
     *                          chain.
     *
     * @return \Closure
     */
    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $url = $this->getHttpClientProxy();

            if (!empty($url)) {
                $options['proxy'] = $url;
            }

            return $handler($request, $options);
        };
    }

    /**
     * Returns HTTP proxy URL if one is configured.
     *
     * @return string
     */
    private function getHttpClientProxy(): string
    {
        $url = '';

        if (!empty(\BeanFactory::getBeanClass('Administration'))) {
            $config = \Administration::getSettings('proxy');

            if (!empty($config->settings)
                && !empty($config->settings['proxy_on'])
                && !empty($config->settings['proxy_host'])
            ) {
                $url = $config->settings['proxy_host'] . ':' . $config->settings['proxy_port'];

                if (!empty($config->settings['proxy_auth'])) {
                    $url = $config->settings['proxy_username'] . ':' . $config->settings['proxy_password'] . '@' . $url;
                }
            }
        }

        return $url;
    }
}
