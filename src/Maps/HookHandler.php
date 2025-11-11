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

namespace Sugarcrm\Sugarcrm\Maps;

use BeanFactory;
use SugarBean;
use Exception;
use SugarApiExceptionNotFound;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use InvalidArgumentException;
use Sugarcrm\Sugarcrm\Maps\Constants;
use SugarQuery;
use SugarQueryException;
use Sugarcrm\Sugarcrm\Maps\FilterUtils as MapsFilterUtils;

/**
 *
 * Handle Maps Logic Hooks
 *
 */
class HookHandler
{
    /**
     * To be used from logic hooks to mark a bean as not geocoded
     *
     * @param \SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function geocode($bean, $event, $arguments)
    {
        if (!hasMapsLicense()) {
            return;
        }

        $currentModule = $bean->getModuleName();
        $beanId = $bean->id;
        $isUpdate = $arguments['isUpdate'];

        if (!$this->isMappable($currentModule)) {
            return;
        }

        //we need to update the flag if the record is update
        //otherwise if it's a new bean, it will be handled directly by the scheduler job
        if ($isUpdate) {
            $changedFields = array_keys($arguments['dataChanges']);

            $modulesMapping = $this->getMapsModuleMappings($currentModule);

            $changedAddressFields = array_intersect($changedFields, $modulesMapping);

            $geocodeBean = $this->getGeocodeBean($beanId, $currentModule);

            if (!$geocodeBean) {
                return;
            }

            $saveGeocodeBean = $this->manageNameFieldChanged(
                $changedFields,
                $geocodeBean,
                $bean
            );

            if (safeCount($changedAddressFields) > 0) {
                $geocodeBean->geocoded = false;
                $geocodeBean->address = $this->getFormattedAddress($bean, $currentModule);
                $rawAddress = str_replace(', ', '', $geocodeBean->address);
                $isEmptyAddress = false;

                if (strlen($rawAddress) < Constants::MIN_CHARS_NR_FOR_VALID_ADDRESS) {
                    $isEmptyAddress = true;
                }

                if ($isEmptyAddress) {
                    $geocodeBean->geocoded = 1;
                    $geocodeBean->status = Constants::GEOCODE_SCHEDULER_STATUS_NOT_FOUND;
                } else {
                    $geocodeBean->status = Constants::GEOCODE_SCHEDULER_STATUS_REQUEUE;
                    $geocodeBean->error_message = '';
                }

                MapsFilterUtils::updateGeocodeStatuses([$geocodeBean->id], $geocodeBean->status);

                $saveGeocodeBean = true;
            }

            if ($saveGeocodeBean) {
                $geocodeBean->save();
            }
        }
    }

    /**
     * @param array $changedFields
     * @param SugarBean $geocodeBean
     * @param SugarBean $recordBean
     *
     * @return bool
     */
    protected function manageNameFieldChanged(
        array     $changedFields,
        SugarBean &$geocodeBean,
        SugarBean $recordBean
    ): bool {

        if (in_array('name', $changedFields)) {
            $geocodeBean->parent_name = $recordBean->name;

            return true;
        }

        if (in_array('first_name', $changedFields) || in_array('last_name', $changedFields)) {
            $geocodeBean->parent_name = $recordBean->getRecordName();

            return true;
        }

        return false;
    }

    /**
     * Check whether a module is mappable
     *
     * @param string $currentModule
     *
     * @return bool
     *
     * @throws Exception
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     * @throws UnsatisfiedDependencyException
     * @throws InvalidArgumentException
     */
    protected function isMappable(string $currentModule): bool
    {
        $admin = BeanFactory::getBean('Administration');
        $mapsSettings = $admin->retrieveSettings('maps', true)->settings;

        if (!$mapsSettings || !is_array($mapsSettings)
            || !array_key_exists('maps_enabled_modules', $mapsSettings)
            || !array_key_exists('maps_modulesData', $mapsSettings)
        ) {
            return false;
        }

        $availableModules = $mapsSettings['maps_enabled_modules'];
        $modulesData = $mapsSettings['maps_modulesData'];

        if (!$availableModules) {
            return false;
        }

        if (!is_array($availableModules)) {
            return false;
        }

        if (!safeInArray($currentModule, $availableModules)) {
            return false;
        }

        if (!array_key_exists($currentModule, $modulesData)) {
            return false;
        }

        if (!array_key_exists('mappings', $modulesData[$currentModule])) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted address
     *
     * @param SugarBean $targetBean
     * @param string $currentModule
     *
     * @return string
     *
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    protected function getFormattedAddress(SugarBean $targetBean, string $currentModule): string
    {
        $address = [];

        $mappingTable = $this->getMapsModuleMappings($currentModule);

        foreach ($mappingTable as $clientKey => $sugarKey) {
            $value = null;

            if ($sugarKey && $targetBean->getFieldDefinition($sugarKey)) {
                $value = $targetBean->{$sugarKey};
            }

            if ($value) {
                $address[$clientKey] = $value;
            }
        }

        $addressString = implode(', ', $address);

        return $addressString;
    }

    /**
     * Get mapping for a given module
     *
     * @return array
     */
    protected function getMapsModuleMappings(string $currentModule): array
    {
        $admin = BeanFactory::getBean('Administration');

        $mapsSettings = $admin->retrieveSettings('maps', true)->settings;
        $modulesData = $mapsSettings['maps_modulesData'];

        $mappings = $modulesData[$currentModule]['mappings'];

        return array_values($mappings);
    }

    /**
     * Get geocode record by parent
     *
     * @return mixed
     */
    protected function getGeocodeBean(string $parentId, string $parentType)
    {
        $geocodeBeanTemplate = BeanFactory::newBean(Constants::GEOCODE_MODULE);

        $sq = new SugarQuery();
        $sq->select('id');
        $sq->from($geocodeBeanTemplate)
            ->where()
            ->equals('parent_type', $parentType)
            ->equals('parent_id', $parentId)
            ->equals('deleted', 0);
        $sq->limit(1);

        $result = $geocodeBeanTemplate->fetchFromQuery($sq, ['id']);

        if (empty($result)) {
            return false;
        }

        $geocodeBeanId = array_keys($result)[0];
        $geocodeBean = BeanFactory::retrieveBean(Constants::GEOCODE_MODULE, $geocodeBeanId);

        return $geocodeBean;
    }
}
