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

namespace Sugarcrm\Sugarcrm\DocumentMerge\Client\Constants;

abstract class MergeType
{
    public const Merge = 'merge';
    public const Convert = 'convert';
    public const MultiMerge = 'multimerge';
    public const MultiConvert = 'multimerge_convert';
    public const LabelsGenerate = 'labelsgenerate';
    public const LabelsGenerateConvert = 'labelsgenerate_convert';
    public const Presentation = 'presentation';
    public const PresentationConvert = 'presentation_convert';
    public const Spreadsheet = 'excel';
    public const SpreadsheetConvert = 'excel_convert';
}
