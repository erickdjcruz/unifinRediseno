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

namespace Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Console;

use Sugarcrm\Sugarcrm\Console\CommandRegistry\Mode\InstanceModeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TeamSetPruneRestoreFromBackupCommand extends TeamSetPruneCommand implements InstanceModeInterface
{
    protected function configure()
    {
        $this
            ->setName('teamset:restore_from_backup')
            ->setDescription('Restores all team set tables from backups. DO NOT USE while users are logged into the system!');
        $this->setHelp('
            You must have run either teamset:prune or teamset:backup to have backups to restore from.
            This method will copy the data from the most recent backups into the team set tables. If you have had live 
            users on the system between the time you backed up (either prune or backup) and the time you run restore,
            you will lose any data created during that time period.
            
            DO NOT RUN this command if live users have been on the system since you last ran prune or backup.
            ');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->preflightCheck($output);
        $this->getPruner()->restoreFromBackup();
        return 0;
    }
}
