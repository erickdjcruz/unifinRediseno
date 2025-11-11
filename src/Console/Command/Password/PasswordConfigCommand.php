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

namespace Sugarcrm\Sugarcrm\Console\Command\Password;

use Sugarcrm\Sugarcrm\Console\CommandRegistry\Mode\InstanceModeInterface;
use Sugarcrm\Sugarcrm\Security\Password\Hash;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use SugarConfig;
use ReflectionObject;

/**
 *
 * Show password hash configuration
 *
 */
class PasswordConfigCommand extends Command implements InstanceModeInterface
{
    /**
     * @var Hash
     */
    protected $hash;

    /**
     * Password rules
     * @var array
     */
    protected $pwdRules = [
        'minpwdlength' => 'Minimum length',
        'maxpwdlength' => 'Maximum length',
        'onelower' => 'Require lowercase',
        'oneupper' => 'Require uppercase',
        'onenumber' => 'Require number',
        'onespecial' => 'Require special char',
        'customregex' => 'Custom regex',
    ];

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('password:config')
            ->setDescription('Show password hash configuration');
    }

    /**
     * {inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['', 'Password Configuration:']);
        $this->showPasswordSettings($output);

        $output->writeln(['', 'Hashing Configuration:']);
        $this->showHashingInfo($output);
        return 0;
    }

    /**
     * Output password settings
     * @param OutputInterface $output
     */
    protected function showPasswordSettings(OutputInterface $output)
    {
        $config = $this->getConfig();
        $table = new Table($output);

        foreach ($this->pwdRules as $key => $label) {
            $value = $config[$key] ?? 'unknown';
            if (is_bool($value)) {
                $value = $value ? 'yes' : 'no';
            }
            $table->addRow([$label, $value]);
        }

        $table->render();
    }

    /**
     * Get password settings configuration
     * @return array
     */
    protected function getConfig()
    {
        return SugarConfig::getInstance()->get('passwordsetting', []);
    }

    /**
     * Output hashing backend information
     * @param OutputInterface $output
     */
    protected function showHashingInfo(OutputInterface $output)
    {
        $hash = $this->getHashInstance();

        $table = new Table($output);
        //$table->setStyle('compact');

        // Rehash capability
        $rehash = $this->getProtectedValue($hash, 'rehash') ? '<info>yes</info>' : '<comment>no</comment>';
        $table->addRow(['Rehash enabled', $rehash]);

        // Hash backend
        $backend = $this->getProtectedValue($hash, 'backend');
        $table->addRow(['Backend class', get_class($backend)]);

        $table->render();
    }

    /**
     * Get password hash instance
     * @return Hash
     */
    protected function getHashInstance()
    {
        return Hash::getInstance();
    }

    /**
     * Reflection protected property helper
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected function getProtectedValue($object, $property)
    {
        $ro = new ReflectionObject($object);
        $rp = $ro->getProperty($property);
        $rp->setAccessible(true);
        return $rp->getValue($object);
    }
}
