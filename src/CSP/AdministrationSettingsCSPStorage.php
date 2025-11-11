<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\CSP;

use Administration;
use BeanFactory;

class AdministrationSettingsCSPStorage implements CSPStorage
{
    /** @var callable */
    private $refreshMetaDataCache;

    public function __construct(callable $refreshMetaDataCache)
    {
        $this->refreshMetaDataCache = $refreshMetaDataCache;
    }

    public function save(ContentSecurityPolicy $csp, string $platform = 'base'): void
    {
        $administration = BeanFactory::getBean('Administration');
        $changes = [];
        foreach ($csp->getDirectives() as $key => $value) {
            if ($administration->saveSetting('csp', str_replace('-', '_', $key), $value->source(), $platform)) {
                $changes[$key] = $value->source();
            }
        }
        foreach ($csp->getDirectivesHidden() as $key => $value) {
            if ($administration->saveSetting('csphidden', str_replace('-', '_', $key), $value->source(), $platform)) {
                $changes[$key] = $value->source();
            }
        }
        if (!empty($changes)) {
            ($this->refreshMetaDataCache)();
        }
    }

    public function get(): ContentSecurityPolicy
    {
        $settings = self::getAdministrationSettings();
        $settings['csp_child_src'] = $settings['csp_frame_src'] ?? '';
        $csp = ContentSecurityPolicy::fromDirectivesList(...[]);

        foreach ($settings as $k => $v) {
            if (strpos((string)$k, 'csp_') !== 0 || empty($v)) {
                continue;
            }
            $directiveName = str_replace(['csp_', '_'], ['', '-'], $k);
            $directive = Directive::create($directiveName, $v);
            $csp->setDirective($directive);
        }

        $settings = self::getAdministrationSettingsHiddenCSP();
        foreach ($settings as $k => $v) {
            if (strpos((string)$k, 'csphidden_') !== 0 || empty($v)) {
                continue;
            }
            $directiveName = str_replace(['csphidden_', '_'], ['', '-'], $k);
            if (!empty($csp->getDirective($directiveName))) {
                $csp->appendDirective(Directive::createHidden($directiveName, $v));
            } else {
                $csp->setDirective(Directive::createHidden($directiveName, $v));
            }
        }
        return $csp;
    }

    private static function getAdministrationSettings(): array
    {
        try {
            $administration = Administration::getSettings('csp');
            return $administration->settings;
        } catch (\Exception $e) {
            return [];
        }
    }

    private static function getAdministrationSettingsHiddenCSP(): array
    {
        try {
            $administration = Administration::getSettings('csphidden');
            return $administration->settings;
        } catch (\Exception $e) {
            return [];
        }
    }
}
