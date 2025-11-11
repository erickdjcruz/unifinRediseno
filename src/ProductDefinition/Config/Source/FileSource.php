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

namespace Sugarcrm\Sugarcrm\ProductDefinition\Config\Source;

/**
 * Read source file and return product definition
 */
class FileSource implements SourceInterface
{
    /**
     * path to source file
     * @var string
     */
    protected $sourceFile;

    /**
     * constructor.
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (empty($options['source'])) {
            throw new \InvalidArgumentException('source file should not be empty');
        }
        $this->sourceFile = $options['source'];
    }

    /**
     * read file and return product definition array
     * @return string|null
     */
    public function getDefinition(): ?string
    {
        if (!file_exists($this->sourceFile)) {
            $this->getLogger()->error(sprintf('product definition file %s doesn\'t exist', $this->sourceFile));
            return null;
        }

        $raw = file_get_contents($this->sourceFile);
        return (string)$raw;
    }

    /**
     * @return \LoggerManager
     */
    protected function getLogger(): \LoggerManager
    {
        global $log;
        return $log;
    }
}
