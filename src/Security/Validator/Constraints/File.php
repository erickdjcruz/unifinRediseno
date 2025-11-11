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

namespace Sugarcrm\Sugarcrm\Security\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Sugarcrm\Sugarcrm\Security\Validator\ConstraintReturnValueInterface;
use Sugarcrm\Sugarcrm\Security\Validator\ConstraintReturnValueTrait;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 *
 * @see FileValidator
 *
 */
class File extends Constraint implements ConstraintReturnValueInterface
{
    use ConstraintReturnValueTrait;

    public const ERROR_NULL_BYTES = 1;
    public const ERROR_FILE_NOT_FOUND = 2;
    public const ERROR_OUTSIDE_BASEDIR = 3;
    public const ERROR_DIR_TRAVERSAL = 4;

    protected static $errorNames = [
        self::ERROR_NULL_BYTES => 'ERROR_NULL_BYTES',
        self::ERROR_FILE_NOT_FOUND => 'ERROR_FILE_NOT_FOUND',
        self::ERROR_OUTSIDE_BASEDIR => 'ERROR_OUTSIDE_BASEDIR',
        self::ERROR_DIR_TRAVERSAL => 'ERROR_DIR_TRAVERSAL',
    ];

    public $message = 'File name violation: %msg%';
    public $baseDirs = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        $instanceRealPath = realpath(SUGAR_BASE_DIR);
        if (empty($options['baseDirs'])) {
            $options['baseDirs'] = [$instanceRealPath];
        }

        if (!is_array($options['baseDirs'])) {
            throw new ConstraintDefinitionException('No basedirs defined');
        }

        $shadowInstancePaths = [];
        foreach ($options['baseDirs'] as $key => $baseDir) {
            $baseDir = realpath($baseDir);
            if ($baseDir === false) {
                throw new ConstraintDefinitionException('Cannot resolve base dir real path');
            }

            $options['baseDirs'][$key] = $baseDir;
            // add additional base directory when shadow is enabled
            if (defined('SHADOW_INSTANCE_DIR') && strpos($baseDir, (string) $instanceRealPath) === 0) {
                $shadowInstancePaths[] = str_replace($instanceRealPath, SHADOW_INSTANCE_DIR, $baseDir);
            }
        }
        $options['baseDirs'] = array_merge($options['baseDirs'], $shadowInstancePaths);

        parent::__construct($options);
    }
}
