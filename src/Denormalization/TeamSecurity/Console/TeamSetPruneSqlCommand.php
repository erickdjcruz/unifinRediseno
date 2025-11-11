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

class TeamSetPruneSqlCommand extends TeamSetPruneCommand implements InstanceModeInterface
{
    protected function configure()
    {
        $this
            ->setName('teamset:sql')
            ->setDescription('Print the sql query used to search for unused teamsets')
            ->setHelp('Just displays the SQL used to search for unused team sets, in case you want to review it.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->preflightCheck($output);
        $this->getPruner()->getSQL();
        return 0;
    }
}
