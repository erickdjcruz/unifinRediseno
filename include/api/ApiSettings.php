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

/**
 * Wrapper for API settings
 */
class ApiSettings
{
    private $settings = [];

    public function __construct()
    {
        $apiSettings = [];
        require 'include/api/metadata.php';
        if (file_exists('custom/include/api/metadata.php')) {
            // Don't use requireWithCustom because we need the data out of it
            require 'custom/include/api/metadata.php';
        }
        $this->settings = $apiSettings;
    }

    /**
     * @return string|null
     */
    public function getMinVersion(): ?string
    {
        return $this->settings['minVersion'] ?? null;
    }

    /**
     * @return string|null
     */
    public function formatVersionForUrl(): ?string
    {
        if ($this->getMaxVersion() === null) {
            return null;
        }
        $parts = explode('.', $this->getMaxVersion());
        return 'v' . $parts[0] . ($parts[1] ? '_' . $parts[1] : '');
    }

    /**
     * @return string|null
     */
    public function getMaxVersion(): ?string
    {
        return $this->settings['maxVersion'] ?? null;
    }
}
