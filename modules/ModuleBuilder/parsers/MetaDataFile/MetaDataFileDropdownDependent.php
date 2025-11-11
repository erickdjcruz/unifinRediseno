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


/**
 * Sidecar metadata file
 */
class MetaDataFileDropdownDependent implements MetaDataFileInterface
{
    /**
     * @var MetaDataFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $dropdownField;

    /**
     * @var string
     */
    protected $dropdownValue;

    /**
     * Constructor
     *
     * @param MetaDataFileInterface $file
     * @param string $dropdownField
     * @param string $dropdownValue
     */
    public function __construct(MetaDataFileInterface $file, $dropdownField = null, $dropdownValue = null)
    {
        $this->file = $file;
        $this->dropdownField = $dropdownField;
        $this->dropdownValue = $dropdownValue;
    }

    /** {@inheritDoc} */
    public function getPath()
    {
        $path = $this->file->getPath();
        $view = array_pop($path);
        array_push($path, 'dropdowns', $this->dropdownField, $this->dropdownValue, $view);

        return $path;
    }
}
