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


class ExternalSourceEAPMAdapter extends ImportDataSource
{
    /**
     * @var string The name of the EAPM object.
     */
    // @codingStandardsIgnoreStart PSR2.Classes.PropertyDeclaration.Underscore
    private $_eapmName = 'Google';

    /**
     * @var int Total record count of rows that will be imported
     */
    private $_totalRecordCount = -1;

    /**
     * @var The record set loaded from the external source
     */
    private $_recordSet = [];

    protected $_localeSettings = [
        'importlocale_charset' => 'UTF-8',
        'importlocale_dateformat' => 'Y-m-d',
        'importlocale_timeformat' => 'H:i',
        'importlocale_timezone' => '',
        'importlocale_currency' => '',
        'importlocale_default_currency_significant_digits' => '',
        'importlocale_num_grp_sep' => '',
        'importlocale_dec_sep' => '',
        'importlocale_default_locale_name_format' => '',
    ];

    // @codingStandardsIgnoreEnd PSR2.Classes.PropertyDeclaration.Underscore


    public function __construct($eapmName)
    {
        global $current_user, $locale;
        $this->_eapmName = $eapmName;

        $this->_localeSettings['importlocale_num_grp_sep'] = $current_user->getPreference('num_grp_sep');
        $this->_localeSettings['importlocale_dec_sep'] = $current_user->getPreference('dec_sep');
        $this->_localeSettings['importlocale_default_currency_significant_digits'] = $locale->getPrecedentPreference('default_currency_significant_digits', $current_user);
        $this->_localeSettings['importlocale_default_locale_name_format'] = $locale->getLocaleFormatMacro($current_user);
        $this->_localeSettings['importlocale_currency'] = $locale->getPrecedentPreference('currency', $current_user);
        $this->_localeSettings['importlocale_timezone'] = $current_user->getPreference('timezone');

        $this->setSourceName();
    }

    /**
     * Return a feed of google contacts using the EAPM and Connectors farmework.
     *
     * @param  $maxResults
     * @return array
     * @throws Exception
     */
    public function loadDataSet($maxResults = 0)
    {
        if (!$eapmBean = EAPM::getLoginInfo($this->_eapmName, true)) {
            throw new Exception("Authentication error with {$this->_eapmName}");
        }

        $api = ExternalAPIFactory::loadAPI($this->_eapmName);
        $api->loadEAPM($eapmBean);
        $conn = $api->getConnector();

        $feed = $conn->getList(['maxResults' => $maxResults, 'startIndex' => $this->_offset]);
        if ($feed !== false) {
            $this->_totalRecordCount = $feed['totalResults'];
            $this->_recordSet = $feed['records'];
        } else {
            throw new Exception("Unable to retrieve {$this->_eapmName} feed.");
        }
    }

    public function getHeaderColumns()
    {
        return '';
    }

    public function getTotalRecordCount()
    {
        return $this->_totalRecordCount;
    }

    public function setSourceName($sourceName = '')
    {
        $this->_sourcename = $this->_eapmName;
    }

    //Begin Implementation for SPL's Iterator interface
    #[\ReturnTypeWillChange]
    public function current()
    {
        $this->_currentRow = current($this->_recordSet);
        return $this->_currentRow;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->_recordSet);
    }

    public function rewind(): void
    {
        reset($this->_recordSet);
    }

    public function next(): void
    {
        $this->_rowsCount++;
        next($this->_recordSet);
    }

    public function valid(): bool
    {
        return (current($this->_recordSet) !== false);
    }
}
