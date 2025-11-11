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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\LoggerChain;
use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Sugarcrm\Sugarcrm\ACL\Backend\Redis as AclCacheRedis;
use Sugarcrm\Sugarcrm\ACL\EncryptionKey;
use Sugarcrm\Sugarcrm\ACL\InstanceKeyPrefix;
use Sugarcrm\Sugarcrm\ACL\MultitenantValueSerializer;
use Sugarcrm\Sugarcrm\ACL\PhpValueSerializer;
use Sugarcrm\Sugarcrm\Audit\EventRepository;
use Sugarcrm\Sugarcrm\Audit\Formatter as AuditFormatter;
use Sugarcrm\Sugarcrm\Audit\Formatter\CompositeFormatter;
use Sugarcrm\Sugarcrm\Cache\Backend\APCu as APCuCache;
use Sugarcrm\Sugarcrm\Cache\Backend\BackwardCompatible as BackwardCompatibleCache;
use Sugarcrm\Sugarcrm\Cache\Backend\InMemory as InMemoryCache;
use Sugarcrm\Sugarcrm\Cache\Backend\Memcached as MemcachedCache;
use Sugarcrm\Sugarcrm\Cache\Backend\Redis as RedisCache;
use Sugarcrm\Sugarcrm\Cache\Backend\WinCache;
use Sugarcrm\Sugarcrm\Cache\Exception as SugarCacheException;
use Sugarcrm\Sugarcrm\Cache\Middleware\DefaultTTL;
use Sugarcrm\Sugarcrm\Cache\Middleware\GZCompressed;
use Sugarcrm\Sugarcrm\Cache\Middleware\MultiTenant as MultiTenantCache;
use Sugarcrm\Sugarcrm\Cache\Middleware\MultiTenant\KeyStorage\Configuration as ConfigurationKeyStorage;
use Sugarcrm\Sugarcrm\Cache\Middleware\Replicate;
use Sugarcrm\Sugarcrm\Clock\Clock;
use Sugarcrm\Sugarcrm\Clock\Timer;
use Sugarcrm\Sugarcrm\CSP\AdministrationSettingsCSPStorage;
use Sugarcrm\Sugarcrm\CSP\CSPStorage;
use Sugarcrm\Sugarcrm\CSP\RefreshMetaDataCache;
use Sugarcrm\Sugarcrm\DataPrivacy\Erasure\Repository;
use Sugarcrm\Sugarcrm\Dbal\Logging\DebugLogger;
use Sugarcrm\Sugarcrm\Dbal\Logging\Formatter as DbalFormatter;
use Sugarcrm\Sugarcrm\Dbal\Logging\SlowQueryLogger;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Command\Rebuild;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Command\StateAwareRebuild;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Job\RebuildJob;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener\Builder\StateAwareBuilder;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener\Composite;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener\Logger;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener\PreFetch;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener\StateAwareListener;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\State;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\State\Storage\AdminSettingsStorage;
use Sugarcrm\Sugarcrm\DependencyInjection\Exception\ServiceUnavailable;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionPrefetcher;
use Sugarcrm\Sugarcrm\FeatureToggle\FeatureFlag;
use Sugarcrm\Sugarcrm\FeatureToggle\Features;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\StrictIncludes;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\TranslateMLPCode;
use Sugarcrm\Sugarcrm\FeatureToggle\FeaturesContext;
use Sugarcrm\Sugarcrm\FeatureToggle\FeatureToggler;
use Sugarcrm\Sugarcrm\Logger\Factory as LoggerFactory;
use Sugarcrm\Sugarcrm\PubSub\Buffer\PushSubscriptionBufferInterface as PubSubPushSubscriptionBufferInterface;
use Sugarcrm\Sugarcrm\PubSub\Buffer\InMemory\PushSubscriptionBuffer as PubSubPushSubscriptionBuffer;
use Sugarcrm\Sugarcrm\PubSub\Client\Batch\PushClient as PubSubBatchPushClient;
use Sugarcrm\Sugarcrm\PubSub\Client\Http\PushClient as PubSubHttpPushClient;
use Sugarcrm\Sugarcrm\PubSub\Client\PushClientInterface as PubSubPushClientInterface;
use Sugarcrm\Sugarcrm\PubSub\Module\Event\PushSubscriptionPublisher as PubSubModuleEventPushSubscriptionPublisher;
use Sugarcrm\Sugarcrm\PubSub\PublisherInterface as PubSubPublisherInterface;
use Sugarcrm\Sugarcrm\PubSub\Settings\PushSubscription as PubSubPushSubscriptionSettings;
use Sugarcrm\Sugarcrm\Security\Context;
use Sugarcrm\Sugarcrm\Security\HttpClient\ExternalResourceClient;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\BlacklistVisitor;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\CodeScanner;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\DynamicNameVisitor;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\IncludesVisitor;
use Sugarcrm\Sugarcrm\Security\Subject\Formatter as SubjectFormatter;
use Sugarcrm\Sugarcrm\Security\Subject\Formatter\BeanFormatter;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UltraLite\Container\Container;
use Sugarcrm\Sugarcrm\Performance\Dbal\XhprofLogger;
use Sugarcrm\Sugarcrm\ACL\Cache as AclCacheInterface;

return new Container([
    SugarConfig::class => function (): SugarConfig {
        return SugarConfig::getInstance();
    },
    Connection::class => function (): Connection {
        return DBManagerFactory::getConnection();
    },
    SQLLogger::class => function (ContainerInterface $container): SQLLogger {
        $config = $container->get(SugarConfig::class);

        $channel = LoggerFactory::getLogger('db');
        DbalFormatter::wrapLogger($channel);

        $loggers = [new DebugLogger($channel)];

        if ($config->get('dump_slow_queries')) {
            $loggers[] = new SlowQueryLogger(
                $channel,
                $config->get('slow_query_time_msec', 5000)
            );
        }

        if ($config->get('xhprof_config')
            && SugarXHprof::getInstance()->isEnabled()
            && empty($GLOBALS['installing'])) {
            $loggers[] = new XhprofLogger(SugarXHprof::getInstance());
        }

        if (safeCount($loggers) == 1) {
            return array_shift($loggers);
        }

        return new LoggerChain($loggers);
    },
    LoggerInterface::class => function (): LoggerInterface {
        return LoggerFactory::getLogger('default');
    },
    LoggerInterface::class . '-denorm' => function (): LoggerInterface {
        return LoggerFactory::getLogger('denorm');
    },
    LoggerInterface::class . '-security' => function (): LoggerInterface {
        return LoggerFactory::getLogger('security');
    },
    State::class => function (ContainerInterface $container): State {
        $config = $container->get(SugarConfig::class);

        $state = new State(
            $config,
            new AdminSettingsStorage(),
            $container->get(LoggerInterface::class . '-denorm')
        );
        $config->attach($state);

        return $state;
    },
    Listener::class => function (ContainerInterface $container): Listener {
        $conn = $container->get(Connection::class);
        $state = $container->get(State::class);
        $builder = new StateAwareBuilder(
            $container->get(Connection::class),
            $state
        );

        $logger = $container->get(LoggerInterface::class . '-denorm');

        $listener = new StateAwareListener($builder, $logger);
        $state->attach($listener);

        return new Composite(
            new Logger($logger),
            $listener,
            new PreFetch($conn)
        );
    },
    StateAwareRebuild::class => function (ContainerInterface $container): StateAwareRebuild {
        $logger = $container->get(LoggerInterface::class . '-denorm');

        return new StateAwareRebuild(
            $container->get(State::class),
            new Rebuild(
                $container->get(Connection::class),
                $logger
            ),
            $logger
        );
    },
    RebuildJob::class => function (ContainerInterface $container): RebuildJob {
        return new RebuildJob(
            $container->get(StateAwareRebuild::class)
        );
    },
    Context::class => function (ContainerInterface $container): Context {
        return new Context(
            $container->get(LoggerInterface::class . '-security')
        );
    },
    Localization::class => function (): Localization {
        return Localization::getObject();
    },
    SubjectFormatter::class => function (ContainerInterface $container): SubjectFormatter {
        return new BeanFormatter(
            $container->get(Localization::class)
        );
    },
    EventRepository::class => function (ContainerInterface $container): EventRepository {
        return new EventRepository(
            $container->get(Connection::class),
            $container->get(Context::class)
        );
    },
    Repository::class => function (ContainerInterface $container): Repository {
        return new Repository(
            $container->get(Connection::class)
        );
    },
    AuditFormatter::class => function (ContainerInterface $container): AuditFormatter {
        $class = SugarAutoLoader::customClass(CompositeFormatter::class);
        return new $class(
            new AuditFormatter\Date(),
            new AuditFormatter\Enum(),
            new AuditFormatter\Email(),
            new AuditFormatter\Subject($container->get(SubjectFormatter::class))
        );
    },
    Administration::class => function (): Administration {
        return BeanFactory::newBean('Administration');
    },
    CacheInterface::class => function (ContainerInterface $container): CacheInterface {
        $config = $container->get(SugarConfig::class);

        if ($config->get('external_cache_disabled')) {
            return new InMemoryCache();
        }

        $multiTenantCache = $container->get(MultiTenantCache::class);
        if ($multiTenantCache !== null) {
            $backend = $multiTenantCache;
            if ($config->get('cache.disable_gz', false) !== true) {
                $backend = new GZCompressed(
                    $backend,
                    $container->get(LoggerInterface::class),
                    $config->get('cache.gz_level')
                );
            }
        } else {
            $backend = $container->get($config->get('cache.backend') ?? BackwardCompatibleCache::class);
            $backend = new DefaultTTL($backend, $config->get('cache_expire_timeout') ?: 300);
        }

        return new Replicate(
            $backend,
            new InMemoryCache()
        );
    },
    MultiTenantCache::class => function (ContainerInterface $container): ?MultiTenantCache {
        $config = $container->get(SugarConfig::class);

        if ($config->get('external_cache_disabled') || !$config->get('cache.multi_tenant')) {
            return null;
        }
        $backend = $container->get($config->get('cache.backend') ?? BackwardCompatibleCache::class);
        $backend = new DefaultTTL($backend, $config->get('cache_expire_timeout') ?: 300);

        return new MultiTenantCache(
            substr($config->get('unique_key'), 0, -1)
            . (int)$config->get('cache.disable_gz', 0),
            new ConfigurationKeyStorage($config),
            $backend,
            $container->get(LoggerInterface::class)
        );
    },
    BackwardCompatibleCache::class => function (): BackwardCompatibleCache {
        return new BackwardCompatibleCache(SugarCache::electBackend());
    },
    ApcuCache::class => function (): ApcuCache {
        try {
            return new ApcuCache();
        } catch (CacheException $e) {
            throw new ServiceUnavailable($e->getMessage(), 0, $e);
        }
    },
    RedisCache::class => function (ContainerInterface $container): RedisCache {
        try {
            return new RedisCache($container->get(Redis::class));
        } catch (CacheException $e) {
            throw new ServiceUnavailable($e->getMessage(), 0, $e);
        }
    },
    MemcachedCache::class => function (ContainerInterface $container): MemcachedCache {
        $config = $container->get(SugarConfig::class)->get('external_cache.memcache');

        try {
            if (!extension_loaded('memcached')) {
                throw new SugarCacheException('The memcached extension is not loaded');
            }
            return new MemcachedCache($config['host'] ?? null, $config['port'] ?? null);
        } catch (CacheException|SugarCacheException $e) {
            throw new ServiceUnavailable($e->getMessage(), 0, $e);
        }
    },
    WinCache::class => function (): WinCache {
        try {
            return new WinCache();
        } catch (CacheException $e) {
            throw new ServiceUnavailable($e->getMessage(), 0, $e);
        }
    },
    TimeDate::class => function (): TimeDate {
        return TimeDate::getInstance();
    },
    Validator::class => function (): ValidatorInterface {
        return Validator::getService();
    },
    AclCacheInterface::class => function (ContainerInterface $container): AclCacheInterface {
        if (SugarCache::electBackend() instanceof SugarCacheRedis) {
            $config = $container->get(SugarConfig::class);

            $valueSerializer = new PhpValueSerializer();
            if ($config->get('cache.multi_tenant')) {
                $encryptionKey = (new EncryptionKey(new ConfigurationKeyStorage($config)))->get();
                $valueSerializer = new MultitenantValueSerializer(
                    $valueSerializer,
                    $encryptionKey,
                    $container->get(LoggerInterface::class)
                );
            }
            $keyConverter = new InstanceKeyPrefix($config->get('unique_key'));

            $backend = new AclCacheRedis(
                $container->get(Redis::class),
                $keyConverter,
                $valueSerializer
            );

            return $backend;
        }
        return AclCache::getInstance();
    },
    Redis::class => function (ContainerInterface $container): Redis {
        if (!extension_loaded('redis')) {
            throw new SugarCacheException('Redis extension is not loaded');
        }

        $client = new Redis();

        $config = $container->get(SugarConfig::class)->get('external_cache.redis');
        $persistentId = $container->get(SugarConfig::class)->get('unique_key');

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 6379;
        $timeout = $config['timeout'] ?? 0;
        $persistentId = $persistentId ?? '';
        $persistent = $config['persistent'] ?? false;

        if (version_compare(phpversion('redis'), '4.0.0') >= 0) {
            try {
                if ($persistent) {
                    $client->pconnect($host, $port, $timeout, $persistentId);
                } else {
                    $client->connect($host, $port, $timeout);
                }
            } catch (RedisException $e) {
                throw new SugarCacheException($e->getMessage(), 0, $e);
            }
        } else {
            if (!@$client->connect($host, $port, $timeout)) {
                throw new SugarCacheException(error_get_last()['message']);
            }
        }

        return $client;
    },
    CSPStorage::class => function (ContainerInterface $container): CSPStorage {
        return new AdministrationSettingsCSPStorage(new RefreshMetaDataCache());
    },
    SubscriptionPrefetcher::class => function (ContainerInterface $container): SubscriptionPrefetcher {
        return new SubscriptionPrefetcher(
            \Administration::getSettings('license'),
            $container->get(LoggerInterface::class)
        );
    },
    FeatureFlag::class => function (ContainerInterface $container): FeatureFlag {
        return $container->get(Features::class);
    },
    FeatureToggler::class => function (ContainerInterface $container): FeatureToggler {
        return $container->get(Features::class);
    },
    FeaturesContext::class => function (ContainerInterface $container): FeaturesContext {
        return $container->get(Features::class);
    },
    Features::class => function (): Features {
        $sugar_version = $GLOBALS['sugar_config']['sugar_version'] ?? null;
        if (null === $sugar_version) {
            require 'sugar_version.php';
        }
        /**
         * @var string $sugar_version
         */
        $features = new Features($sugar_version);
        $configuredFeatures = array_merge(
            SugarConfig::getInstance()->get('moduleInstaller.features', []),
            SugarConfig::getInstance()->get('features', []),
        );
        foreach ($configuredFeatures as $feature => $state) {
            if ($state === true) {
                $features->enable($feature);
            } else {
                $features->disable($feature);
            }
        }
        return $features;
    },
    Clock::class => function (ContainerInterface $container): Clock {
        return new Clock();
    },
    PubSubModuleEventPushSubscriptionPublisher::class => function (ContainerInterface $container): PubSubPublisherInterface {
        $client = $container->get(PubSubPushClientInterface::class);
        $logger = $container->get(LoggerInterface::class);

        $publisher = new PubSubModuleEventPushSubscriptionPublisher($client);
        $publisher->setLogger($logger);

        return $publisher;
    },
    PubSubPushClientInterface::class => function (ContainerInterface $container): PubSubPushClientInterface {
        $httpClient = $container->get(PubSubHttpPushClient::class);
        $buffer = $container->get(PubSubPushSubscriptionBufferInterface::class);
        $logger = $container->get(LoggerInterface::class);

        $batchClient = new PubSubBatchPushClient($httpClient);
        $batchClient->setBuffer($buffer);
        $batchClient->setLogger($logger);

        return $batchClient;
    },
    PubSubHttpPushClient::class => function (ContainerInterface $container): PubSubHttpPushClient {
        $config = $container->get(SugarConfig::class);
        $settings = new PubSubPushSubscriptionSettings($config);
        $maxRetries = $settings->getSetting('max_retries');
        $timeout = $settings->getSetting('request_timeout');

        $client = new ExternalResourceClient();
        $client->setMaxRetries($maxRetries);
        $client->setTimeout($timeout);
        $client->useStrictRedirects();

        return new PubSubHttpPushClient($client);
    },
    PubSubPushSubscriptionBufferInterface::class => function (ContainerInterface $container): PubSubPushSubscriptionBufferInterface {
        $config = $container->get(SugarConfig::class);
        $settings = new PubSubPushSubscriptionSettings($config);
        $bufferCapacity = $settings->getSetting('buffer_capacity');
        $bufferTimeout = $settings->getSetting('buffer_timeout');

        $clock = $container->get(Clock::class);
        $timer = new Timer();
        $timer->setClock($clock);

        $buffer = new PubSubPushSubscriptionBuffer();
        $buffer->setCapacity($bufferCapacity);
        $buffer->setTimeout($bufferTimeout);
        $buffer->setTimer($timer);

        return $buffer;
    },
    CodeScanner::class => function (ContainerInterface $container): CodeScanner {
        $features = $container->get(FeatureFlag::class);
        $codeScanner = new CodeScanner();
        $codeScanner->registerVisitor(new DynamicNameVisitor());
        if ($features->isEnabled(StrictIncludes::getName())) {
            $codeScanner->registerVisitor(new IncludesVisitor());
        }
        $denyLists = (new \ModuleScanner())->getEffectiveDenyLists();
        extract($denyLists);
        $codeScanner->registerVisitor(
            new BlacklistVisitor($classes, $functions, $methods)
        );
        return $codeScanner;
    },
]);
