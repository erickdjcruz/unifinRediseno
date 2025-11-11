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

namespace Sugarcrm\Sugarcrm\DocumentMerge\Client\Adapter;

use Sugarcrm\Sugarcrm\DocumentMerge\Client\Constants\MergeType;

class AdapterFactory
{
    /**
     * Returns an instance of the adapter based on merge type
     *
     * @param mixed $options
     * @return object|null
     */
    public static function getDataAdapterInstance($options)
    {
        $instance = null;
        $class = static::getClass($options['mergeType']);
        $log = \LoggerManager::getLogger();

        $adapterClass = __NAMESPACE__ . $class;
        if (class_exists($adapterClass)) {
            try {
                $instance = new $adapterClass($options);
            } catch (\Error $e) {
                $log->error('Document Merge: ' . $e->getMessage());
            }
        } else {
            $log->error('Document Merge: Data adapter ' . $adapterClass . ' does not exist.');
        }

        return $instance;
    }

    /**
     * Return the class coresponding to the merge type
     *
     * @param string $type
     * @return string
     */
    private static function getClass(string $type): string
    {
        $class = null;
        switch ($type) {
            case MergeType::Convert:
                $class = '\\Adapters\\ConvertDataAdapter';
                break;
            case MergeType::Merge:
                $class = '\\Adapters\\MergeDataAdapter';
                break;
            case MergeType::MultiMerge:
                $class = '\\Adapters\\MultiMergeDataAdapter';
                break;
            case MergeType::MultiConvert:
                $class = '\\Adapters\\MultiConvertDataAdapter';
                break;
            case MergeType::LabelsGenerate:
                $class = '\\Adapters\\LabelsMergeDataAdapter';
                break;
            case MergeType::LabelsGenerateConvert:
                $class = '\\Adapters\\LabelsMergeConvertDataAdapter';
                break;
            case MergeType::Spreadsheet:
                $class = '\\Adapters\\ExcelMergeDataAdapter';
                break;
            case MergeType::SpreadsheetConvert:
                $class = '\\Adapters\\ExcelMergeConvertDataAdapter';
                break;
            case MergeType::Presentation:
                $class = '\\Adapters\\PresentationDataAdapter';
                break;
            case MergeType::PresentationConvert:
                $class = '\\Adapters\\PresentationConvertDataAdapter';
                break;
        }

        return $class;
    }
}
