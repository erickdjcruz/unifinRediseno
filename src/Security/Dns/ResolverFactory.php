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

declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\Security\Dns;

use Psr\SimpleCache\CacheInterface;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Logger\Factory as LoggerFactory;
use Administration;

class ResolverFactory
{
    public static function getInstance(): Resolver
    {
        if (\SugarConfig::getInstance()->get('security.use_doh', false)) {
            $proxy_config = Administration::getSettings('proxy');
            $options = GoogleResolver::DEFAULT_OPTIONS;
            if (!empty($proxy_config->settings['proxy_auth'])) {
                $auth = base64_encode($proxy_config->settings['proxy_username'] . ':' . $proxy_config->settings['proxy_password']);
                $options['http']['header'][] = "Proxy-Authorization: Basic {$auth}";
                $proxyHost = $proxy_config->settings['proxy_host'];
                $proxyPort = $proxy_config->settings['proxy_port'];
                $options['https']['proxy'] = 'tcp://' . $proxyHost . ':' . $proxyPort;
                $options['https']['request_fulluri'] = true;
            }
            $cache = Container::getInstance()->get(CacheInterface::class);
            $resolver = new CachedResolver($cache, new ChainResolver(new GoogleResolver($options), new NativeResolver()));
        } else {
            $resolver = new NativeResolver();
        }
        $logger = LoggerFactory::getLogger('dns_resolver');
        $loggableResolver = new ResolverLogger($resolver);
        $loggableResolver->setLogger($logger);
        return $loggableResolver;
    }
}
