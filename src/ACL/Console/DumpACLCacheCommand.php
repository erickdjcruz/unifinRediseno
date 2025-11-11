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

namespace Sugarcrm\Sugarcrm\ACL\Console;

use Symfony\Component\Console\Command\Command as Command;
use Psr\Log\LoggerInterface;
use Sugarcrm\Sugarcrm\Console\CommandRegistry\Mode\InstanceModeInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sugarcrm\Sugarcrm\ACL\Cache as AclCacheInterface;
use Sugarcrm\Sugarcrm\ACL\Backend\Redis as RedisBackend;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\ACL\MultitenantValueSerializer;
use Sugarcrm\Sugarcrm\ACL\PhpValueSerializer;
use Sugarcrm\Sugarcrm\ACL\InstanceKeyPrefix;
use Sugarcrm\Sugarcrm\ACL\EncryptionKey;
use Sugarcrm\Sugarcrm\Cache\Middleware\MultiTenant\KeyStorage\Configuration as ConfigurationKeyStorage;

class DumpACLCacheCommand extends Command implements InstanceModeInterface
{
    protected function configure()
    {
        $this
            ->setName('aclcache:dump')
            ->setDescription('Dumps ACL cache contents into cache/acldumps folder.')
            ->addOption(
                'user',
                null,
                InputOption::VALUE_OPTIONAL,
                'User ID to dump the ACLCache. If skipped - will try to dump the ACLCaches for all users.'
            )
            ->setHelp('
            
                Dumps ACL Cache content for a specific user or all users.
            ');
    }

    public function isEnabled()
    {
        $aclCache = Container::getInstance()->get(AclCacheInterface::class);
        return $aclCache instanceof RedisBackend;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userID = $input->getOption('user');
        $redisClient = Container::getInstance()->get(\Redis::class);
        $keyConverter = new InstanceKeyPrefix(
            Container::getInstance()->get(\SugarConfig::class)->get('unique_key')
        );
        if (is_null($userID)) {
            $allACLs = $redisClient->hGetAll($keyConverter->convert('ACL'));
            foreach ($allACLs as $userID => $serializedHashes) {
                $this->dumpUserACLs($userID, $serializedHashes, $output);
            }
        } else {
            $serializedHashes = $redisClient->hGet($keyConverter->convert('ACL'), $userID);
            if ($serializedHashes !== false) {
                $this->dumpUserACLs($userID, $serializedHashes, $output);
            } else {
                $output->writeln(
                    'No ACLs for user ' . $userID . ' found.'
                );
            }
        }
        return 0;
    }

    /**
     * @param string $userID
     * @param string $serializedHashes
     * @param OutputInterface $output
     */
    private function dumpUserACLs(string $userID, string $serializedHashes, OutputInterface $output): void
    {
        $valueSerializer = new PhpValueSerializer();
        $aclCache = Container::getInstance()->get(AclCacheInterface::class);
        if (Container::getInstance()->get(\SugarConfig::class)->get('cache.multi_tenant')) {
            $encryptionKey = (
            new EncryptionKey(
                new ConfigurationKeyStorage(
                    Container::getInstance()->get(\SugarConfig::class)
                )
            )
            )->get();
            $valueSerializer = new MultitenantValueSerializer(
                $valueSerializer,
                $encryptionKey,
                Container::getInstance()->get(LoggerInterface::class)
            );
        }
        $hashes = $valueSerializer->unserialize($serializedHashes);
        if (empty($hashes)) {
            return;
        }
        if (sugar_mkdir(sugar_cached('acldumps'))) {
            $dumpPath = sugar_cached('acldumps') . DIRECTORY_SEPARATOR . $userID . '.php';
            file_put_contents($dumpPath, '<?php ' . PHP_EOL);
            foreach ($hashes as $type => $hash) {
                $aclSubtypeDump = $aclCache->retrieve($userID, $type);
                $out = '$ACL[\'' . $type . '\'] = ' .
                    var_export($aclSubtypeDump, true) . ';' . PHP_EOL;
                file_put_contents($dumpPath, $out, FILE_APPEND);
                $output->writeln(
                    $userID . '\'s ' . $type . ' ACLs are dumped into ' . $dumpPath
                );
            }
        } else {
            $output->writeln(
                'Could not create ' . sugar_cached('acldumps') . ' folder.'
            );
        }
    }
}
