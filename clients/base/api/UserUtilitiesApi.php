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

use Sugarcrm\Sugarcrm\UserUtils\Invoker\Invoker;

class UserUtilitiesApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'getLocaleData' => [
                'reqType' => 'GET',
                'path' => ['userUtilities', 'getLocaleData', '?'],
                'pathVars' => ['userUtilities', 'getLocaleData', 'userId'],
                'method' => 'getLocaleData',
                'shortHelp' => 'Retrieves user locale settings',
                'longHelp' => 'include/api/help/user_utilities_getlocaledata_get_help.html',
                'minVersion' => '11.15',
            ],
            'performActions' => [
                'reqType' => 'POST',
                'path' => ['userUtilities'],
                'pathVars' => ['userUtilities'],
                'method' => 'performActions',
                'shortHelp' => 'Execute user utility actions',
                'longHelp' => 'include/api/help/user_utilities_performactions_post_help.html',
                'minVersion' => '11.15',
            ],
        ];
    }

    /**
     * Retrieves locale data dropdowns and data for a user
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getLocaleData(ServiceBase $api, array $args): array
    {
        global $locale;
        global $sugar_config;

        $userId = $args['userId'];

        $user = BeanFactory::retrieveBean('Users', $userId);
        $locale->getCurrencies();

        $coreDefaults = $locale->getLocaleConfigDefaults();

        $dateFormat = $user->getPreference('datef');
        $timeFormat = $user->getPreference('timef');
        $userCurrency = $user->getPreference('currency') ?? '-99';

        $currency = $userCurrency === 'US Dollars' ? '-99' : $userCurrency;

        $defaultCurrencySignificantDigits = $user->getPreference('default_currency_significant_digits') ??
            $coreDefaults['default_currency_significant_digits'];
        $timezone = $user->getPreference('timezone');
        $ut = $user->getPreference('ut');
        $numGrpSep = $user->getPreference('num_grp_sep') ?? ',';
        $defaultLocaleNameFormat = $user->getPreference('default_locale_name_format');
        $decSep = $user->getPreference('dec_sep') ?? '.';
        $appearance = $user->getPreference('appearance') ?? 'system_default';
        $fieldNamePlacement = $user->getPreference('field_name_placement');

        $dateOptions = [];
        foreach ($sugar_config['date_formats'] as $name => $val) {
            $selectedDateOption = $dateFormat === $name ? true : false;
            $dateOptions[] = [
                'name' => $name,
                'label' => $val,
                'selected' => $selectedDateOption,
            ];
        }

        $timeOptions = [];
        foreach ($sugar_config['time_formats'] as $name => $val) {
            $selectedTimeOption = $timeFormat === $name ? true : false;
            $timeOptions[] = [
                'name' => $name,
                'label' => $val,
                'selected' => $selectedTimeOption,
            ];
        }

        $nameOptions = [];
        foreach ($locale->getUsableLocaleNameOptions($sugar_config['name_formats']) as $name => $val) {
            $selectedNameOption = $defaultLocaleNameFormat === $name ? true : false;
            $nameOptions[] = [
                'name' => $name,
                'label' => $val,
                'selected' => $selectedNameOption,
            ];
        }

        $currencyOptions = [];
        foreach ($locale->getCurrencies() as $name => $val) {
            $currencyLabel = $val['symbol'] . ' - ' . $val['name'];
            $selectedCurrency = $currency == $name ? true : false;
            $currencyOptions[] = [
                'id' => $name,
                'label' => $currencyLabel,
                'selected' => $selectedCurrency,
            ];
        }

        $sigDigitsOptions = [];
        for ($i = 0; $i <= 6; $i++) {
            $selectedSigDigits = $defaultCurrencySignificantDigits == $i ? true : false;
            $sigDigitsOptions[] = [
                'name' => $i,
                'selected' => $selectedSigDigits,
            ];
        }

        $timezoneList = TimeDate::getTimezoneList();
        $timezoneOptions = [];
        foreach ($timezoneList as $tzValue => $tzLabel) {
            $selectedTimezone = $timezone === $tzValue ? true : false;
            $timezoneOptions[] = [
                'name' => $tzValue,
                'label' => $tzLabel,
                'selected' => $selectedTimezone,
            ];
        }

        $wizardPrompt = false;
        if (!$ut) {
            $wizardPrompt = true;
        }

        $appearanceOptions = [];
        foreach (translate('appearance_options') as $name => $val) {
            $selectedAppearance = $name === $appearance ? true : false;
            $appearanceOptions[] = [
                'name' => $name,
                'label' => $val,
                'selected' => $selectedAppearance,
            ];
        }

        $result = [
            'datef' => $dateFormat,
            'timef' => $timeFormat,
            'currency' => $currency,
            'defaultCurrencySignificantDigits' => $defaultCurrencySignificantDigits,
            'timezone' => $timezone,
            'ut' => $ut,
            'numGrpSep' => $numGrpSep,
            'defaultLocaleNameFormat' => $defaultLocaleNameFormat,
            'decSep' => $decSep,
            'timeOptions' => $timeOptions,
            'dateOptions' => $dateOptions,
            'nameOptions' => $nameOptions,
            'currencyOptions' => $currencyOptions,
            'sigDigitsOptions' => $sigDigitsOptions,
            'timezoneOptions' => $timezoneOptions,
            'wizardPrompt' => $wizardPrompt,
            'appearance' => $appearanceOptions,
            'fieldNamePlacement' => $fieldNamePlacement,
        ];

        return $result;
    }

    /**
     * It will execute the user utils actions
     *
     * @param ServiceBase $api
     * @param array $args
     * @return void
     */
    public function performActions(ServiceBase $api, array $args)
    {
        /**
         * $actions should look like this
         * [
         *      {
         *          'type': 'CopyDashboards',
         *          'dashboards': ['id1', 'id2'],
         *          'modules': ['Accounts', 'Contacts'],
         *          'sourceUser': 'source-user-id',
         *          'destinationUsers': ['destinationUserId1', 'destinationUserId2'],
         *      },
         *      {
         *          'type': 'DeleteFilters',
         *          'filters': ['fid1', 'fid2'],
         *          'modules': [],
         *          'sourceUser': 'source-user-id',
         *          'destinationUsers': ['destinationUserId1', 'destinationUserId2'],
         *      }
         * ...............................................
         * ]
         */
        $actions = $args['actions'];
        $commandInvoker = new Invoker($actions);
        $commandInvoker->execute();

        // for now
        return true;
    }
}
