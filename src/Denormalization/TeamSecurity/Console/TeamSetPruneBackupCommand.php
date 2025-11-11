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

class TeamSetPruneBackupCommand extends TeamSetPruneCommand implements InstanceModeInterface
{
    protected function configure()
    {
        $this
            ->setName('teamset:backup')
            ->setDescription('Backs up the team_sets related tables.');
        $this->setHelp("
            You don't need to run this command before you run teamset:prune.
            This command only backs up the team set tables. This is performed automatically when you run teamset:prune.
            But if you want backups of these tables for some other purpose, you can use this command to do so easily.
            It will back up these tables: team_sets, team_sets_teams, team_sets_modules, and the active denorm table
            (team_sets_users_[1|2])if denormalization is enabled.
            ");
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->preflightCheck($output);
        $this->getPruner()->backupTables();
        return 0;
    }
}
