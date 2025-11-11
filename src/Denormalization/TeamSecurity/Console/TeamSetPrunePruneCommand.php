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

class TeamSetPrunePruneCommand extends TeamSetPruneCommand implements InstanceModeInterface
{
    protected function configure()
    {
        $this
            ->setName('teamset:prune')
            ->setDescription('Prune all team set tables of unused team sets. Original tables will be backed up automatically. DO NOT USE while users are logged into the system!')
            ->setHelp("
            
                NOTE: You should only run this during a planned outage. 
                
                Pruning the team sets tables means
                1) Backing up the denormalization table (if denorm is enabled), and the team_sets, team_sets_teams and team_sets_modules tables.
                   Backup tables start with 'tsp_' and end with a datetime stamp, '_MMDDHHMM'
                2) Truncating the original tables.
                3) Copying the active team sets out of the backup tables and into the truncated original tables, 
                   and leaving out the unused team sets.
                   \"Active\" team sets are team sets that are used by at least one record in sugar, i.e. a Case or an Account.
            ");
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->preflightCheck($output);
        $this->getPruner()->prune();
        return 0;
    }
}
