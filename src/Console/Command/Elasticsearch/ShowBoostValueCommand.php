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

namespace Sugarcrm\Sugarcrm\Console\Command\Elasticsearch;

use Sugarcrm\Sugarcrm\Console\CommandRegistry\Mode\InstanceModeInterface;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\SearchEngine\Engine\Elastic;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * CLI Tool to get all boost values
 *
 */
class ShowBoostValueCommand extends Command implements InstanceModeInterface
{
    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elastic:show_boost_values')
            ->setDescription(
                'show boost values for fts enabled fields for modules'
            )
            ->addOption(
                'modules',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated list of modules to search.'
            );
    }

    /**
     * {inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $engine = $this->getSearchEngine();

        $modules = $input->getOption('modules');
        if ($modules) {
            $modules = explode(',', $modules);
        } else {
            $modules = $engine->getMetaDataHelper()->getAllEnabledModules();
        }

        $globalSearch = $engine->getContainer()->getProvider('GlobalSearch');
        $globalSearch->fieldBoost(true);

        $table = new Table($output);
        $table->setHeaders(['Module', 'Field Name', 'Sugar Type', 'ES Type', 'Orignal Boost Value', 'Calculated Boost Value']);
        foreach ($modules as $module) {
            $sfs = $globalSearch->buildSearchFields([$module]);
            foreach ($sfs->getIterator() as $it) {
                $path = $it->getPath();
                $defs = $it->getDefs();
                $originalBoost = $defs['full_text_search']['boost'] ?? 1.0;
                $table->addRow([$it->getModule(), $path[0], $defs['type'] ?? 'no', $path[1], $originalBoost, $it->getBoost()]);
            }
        }
        $table->render();
        return 0;
    }

    protected function getSearchEngine($checkElastic = false)
    {
        $searchEngine = SearchEngine::getInstance()->getEngine();
        if ($checkElastic && !$searchEngine instanceof Elastic) {
            throw new SugarApiExceptionSearchUnavailable(
                'Administration not supported for non Elasticsearch backend'
            );
        }
        return $searchEngine;
    }
}
