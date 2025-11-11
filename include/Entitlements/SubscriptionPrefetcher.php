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

namespace Sugarcrm\Sugarcrm\Entitlements;

use Psr\Log\LoggerInterface;
use Sugarcrm\Sugarcrm\inc\Entitlements\Exception\SubscriptionException;
use Sugarcrm\Sugarcrm\SystemProcessLock\SystemProcessLock;

/**
 * A part of license polling mechanism. This makes subscription updates to appear quickly on app instances without
 * need of manual revalidation.
 * @see SubscriptionManager::applyDownloadedLicense()
 */
class SubscriptionPrefetcher
{
    public const PREFETCH_INTERVAL = 'PT5M';

    /**
     * timeout for the request
     */
    public const REQUEST_TIMEOUT = 10;

    public const ENDPOINT = 'rest/subscription/';
    public const KEY_LICENSE = 'key';
    public const KEY_LAST_CHECK = 'subscription_checked_at';
    public const KEY_DOWNLOADED_CONTENT = 'subscription_downloaded';
    public const KEY_STORED_CONTENT = 'subscription';

    public const ERR_IN_PROGRESS = 'SUBSCRIPTION_LOADING_IN_PROGRESS';
    public const ERR_EMPTY_LICENSE_KEY = 'LICENSE_KEY_IS_EMPTY';

    /** @var \Administration */
    private $adminBean;
    /** @var LoggerInterface|null */
    private $logger;

    public function __construct(\Administration $adminBean, LoggerInterface $logger)
    {
        $this->adminBean = $adminBean;
        $this->logger = $logger;
    }

    /**
     * Register prefetcher to run as shutdown function to avoid slowdown of web request
     * @return void
     */
    public function register(): void
    {
        register_shutdown_function(function () {
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();
            $this->run();
        });
    }

    public function run(): bool
    {
        $pollingConfig = \SugarConfig::getInstance()->get('license_server_polling', true);
        if ($pollingConfig !== true) {
            return false;
        }
        $downloadedAt = $this->getSetting(self::KEY_LAST_CHECK);
        if ($downloadedAt !== null) {
            $threshold = \DateTime::createFromFormat('Y-m-d H:i:s', $downloadedAt)
                ->add(new \DateInterval(self::PREFETCH_INTERVAL));
            if (new \DateTime() < $threshold) {
                return false;
            }
        }

        $lock = new SystemProcessLock(__METHOD__, '', [
            'iteration_wait_microseconds' => 0,
            'iterations_before_fault' => 1,
        ]);

        $fetchLicense = function () {
            $licenseKey = $this->getSetting('key');
            if (empty($licenseKey)) {
                return self::ERR_EMPTY_LICENSE_KEY;
            }
            return $this->fetchLicenseContent($licenseKey);
        };

        $response = $lock->isolatedCall(
            function () {
                return true;
            },
            $fetchLicense,
            function () {
                return self::ERR_IN_PROGRESS;
            }
        );

        $this->saveSetting(self::KEY_LAST_CHECK, date('Y-m-d H:i:s'));

        if ($response === self::ERR_IN_PROGRESS) {
            $this->logger->info('SubscriptionPrefetcher: subscription loading is in progress already');
            return false;
        }
        if ($response === self::ERR_EMPTY_LICENSE_KEY) {
            $this->logger->info('SubscriptionPrefetcher: license key is empty');
            return false;
        }

        if ($response === false || $response === '' || $response === null) {
            $this->logger->info('SubscriptionPrefetcher: cannot load subscription content');
            return false;
        }

        if ($response === $this->getSetting(self::KEY_STORED_CONTENT)) {
            return false;
        }

        try {
            $subscription = new Subscription($response);
        } catch (SubscriptionException $e) {
            $this->logger->error('Cannot parse subscription content');
            return false;
        }
        if (!$subscription->getSubscriptions()) {
            return false;
        }

        $this->saveSetting(self::KEY_DOWNLOADED_CONTENT, $response);
        return true;
    }

    protected function getSetting(string $key): ?string
    {
        $value = $this->adminBean->settings['license_' . $key] ?? null;
        return is_array($value) ? json_encode($value) : $value;
    }

    /**
     * @param string $licenseKey
     * @return array|false
     */
    protected function fetchLicenseContent(string $licenseKey)
    {
        $endpoint = self::ENDPOINT . $licenseKey;
        $subscriptionClient = new \SugarLicensing();
        return $subscriptionClient->request($endpoint, [], false, self::REQUEST_TIMEOUT);
    }

    protected function saveSetting(string $key, string $value): void
    {
        $this->adminBean->saveSetting('license', $key, $value);
    }
}
