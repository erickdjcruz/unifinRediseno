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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;

class ContentSecurityPolicy
{
    /**
     * @var Directive[]
     */
    private $directives = [];

    /**
     * @var Directive[]
     */
    private $directivesHidden = [];

    /**
     * @return Directive[]
     */
    public function getDirectives(): array
    {
        return $this->directives ?: [];
    }

    /**
     * @return Directive[]
     */
    public function getDirectivesHidden(): array
    {
        return $this->directivesHidden ?: [];
    }

    private function __construct()
    {
    }

    public static function fromDirectivesList(Directive ...$directives): ContentSecurityPolicy
    {
        $csp = new self();
        foreach ($directives as $directive) {
            $csp->setDirective($directive);
        }
        return $csp;
    }

    public static function fromAdministrationSettings(): ContentSecurityPolicy
    {
        /** @var CSPStorage $storage */
        $storage = Container::getInstance()->get(CSPStorage::class);
        if ($storage) {
            return $storage->get();
        } else {
            new self();
        }
    }

    public function withAddedDefaults(): ContentSecurityPolicy
    {
        $new = clone $this;
        $defaults = self::getDefaults();
        foreach ($defaults as $directiveName => $directiveSource) {
            if (isset($this->directives[$directiveName]) || in_array($directiveName, ['default-src', 'connect-src'])) {
                $directive = Directive::create($directiveName, $directiveSource);
                $new->appendDirective($directive);
            }
        }

        $imgSrcDirective = Directive::create('img-src', 'data: http: https: blob:');
        $new->appendDirective($imgSrcDirective);
        $objectSrcDirective = Directive::create('object-src', "'self'");
        $new->appendDirective($objectSrcDirective);
        if (!isset($this->directives['frame-ancestors'])) {
            $frameAncestorsDirective = Directive::create('frame-ancestors', "'self'");
            $new->appendDirective($frameAncestorsDirective);
        }

        if (isset($defaults['font-src']) && !isset($this->directives['font-src'])) {
            $fontSrcDirective = Directive::create('font-src', $defaults['font-src']);
            $new->appendDirective($fontSrcDirective);
        }

        return $new;
    }

    public function getDirective(string $name): ?Directive
    {
        return $this->directives[$name] ?? null;
    }

    public function getDirectiveHidden(string $name): ?Directive
    {
        return $this->directivesHidden[$name] ?? null;
    }

    public function setDirective(Directive $directive): void
    {
        if ($directive->isHidden()) {
            $this->directivesHidden[$directive->name()] = $directive;
        } else {
            $this->directives[$directive->name()] = $directive;
        }
    }

    public function appendDirective(Directive $directive): void
    {
        if ($directive->isHidden()) {
            if (isset($this->directivesHidden[$directive->name()])) {
                $oldDirective = $this->directivesHidden[$directive->name()];
                $this->directivesHidden[$directive->name()] = Directive::createHidden($directive->name(), $oldDirective->source() . ' ' . $directive->source());
            } else {
                $this->directivesHidden[$directive->name()] = $directive;
            }
        } else {
            if (isset($this->directives[$directive->name()])) {
                $oldDirective = $this->directives[$directive->name()];
                $this->directives[$directive->name()] = Directive::create($directive->name(), $oldDirective->source() . ' ' . $directive->source());
            } else {
                $this->directives[$directive->name()] = $directive;
            }
        }
    }

    public function removeDirective(Directive $directive): void
    {
        $target = $directive->isHidden() ? $this->getDirectiveHidden($directive->name()) : $this->getDirective($directive->name());
        if (empty($target)) {
            return;
        }
        $sources = array_filter(explode(' ', $target->source()), function (string $source) use ($directive) {
            return $source !== $directive->source();
        });
        if ($directive->isHidden()) {
            $this->directivesHidden[$directive->name()] = empty($sources)
                ? Directive::createHiddenWithEmptySource($directive->name())
                : Directive::createHidden($directive->name(), implode(' ', $sources));
        } else {
            $this->directives[$directive->name()] = empty($sources)
                ? Directive::createWithEmptySource($directive->name())
                : Directive::create($directive->name(), implode(' ', $sources));
        }
    }

    public function saveToSettings(string $platform = 'base'): void
    {
        /** @var CSPStorage $storage */
        $storage = Container::getInstance()->get(CSPStorage::class);
        $storage->save($this, $platform);
    }

    public function asHeader(): string
    {
        return 'Content-Security-Policy: ' . $this->serializeDirectives();
    }

    public function asString(): string
    {
        return $this->serializeDirectives();
    }

    private function serializeDirectives(): string
    {
        if (!safeCount($this->directives) && !safeCount($this->directivesHidden)) {
            throw new \DomainException('No CSP directives defined');
        }
        $map = [];
        /** @var Directive[] $allDirectives */
        $allDirectives = array_merge(array_values($this->directives), array_values($this->directivesHidden));
        foreach ($allDirectives as $item) {
            if (empty($map[$item->name()])) {
                $map[$item->name()] = $item->value();
            } else {
                $map[$item->name()] .= ' ' . $item->source();
            }
        }
        return implode('; ', $map);
    }

    private static function getDefaults(): array
    {
        $sugarDomains = '*.sugarcrm.com *.salesfusion.com *.salesfusion360.com *.sugarapps.com *.sugarapps.eu *.sugarapps.com.au sugarcrm-release-archive.s3.amazonaws.com';
        $pendoDomains = 'https://*.pendo.io pendo-io-static.storage.googleapis.com pendo-static-5197307572387840.storage.googleapis.com pendo-eu-static.storage.googleapis.com pendo-eu-static-5197307572387840.storage.googleapis.com';
        $bingDomains = '*.bing.com *.virtualearth.net';
        $oktopostDomains = 'https://board.oktopost.com';
        $trustedDomains = $sugarDomains . ' ' . $pendoDomains . ' ' . $bingDomains . ' ' . $oktopostDomains;
        return [
            'default-src' => "'self' 'unsafe-inline' 'unsafe-eval'  " . $trustedDomains,
            //Advanced form defaults
            'style-src' => "'self' 'unsafe-inline' " . $trustedDomains,
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' " . $trustedDomains,
            'form-action' => "'self' " . $trustedDomains,
            'frame-src' => "'self' " . $trustedDomains,
            'connect-src' => "'self' wss://*.sugarapps.com wss://*.sugarapps.com.au wss://*.sugarapps.eu " . $trustedDomains,
            'font-src' => "'self' data: " . $trustedDomains,
            'frame-ancestors' => "'self' " . $sugarDomains,
        ];
    }
}
