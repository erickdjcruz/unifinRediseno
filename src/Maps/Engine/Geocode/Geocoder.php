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

namespace Sugarcrm\Sugarcrm\Maps\Engine\Geocode;

use BeanFactory;
use Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Container;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use InvalidArgumentException;
use Exception;
use SugarApiExceptionNotFound;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use SugarAutoLoader;
use SugarBean;
use Sugarcrm\Sugarcrm\Maps\Constants as MapsGlobalConstants;

class Geocoder
{
    /**
     * @var Geocoder
     */
    protected static $instance;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Get Geocoder instance.
     *
     * @return \Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Geocoder
     * @throws \RuntimeException
     */
    public static function getInstance(): Geocoder
    {
        if (empty(self::$instance)) {
            self::$instance = self::create();
        }

        return self::$instance;
    }

    /**
     * Create new geocoder object. Use self::getInstance unless you know
     * what you are doing.
     *
     * @return \Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Geocoder
     */
    public static function create(): Geocoder
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = SugarAutoLoader::customClass(self::class);
        return new $class();
    }

    /**
     *  Save the coordonates on the bean
     *
     * @param array $targetBeans
     * @param array $geocodeBeans
     *
     * @return bool
     *
     * @throws UnsatisfiedDependencyException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws SugarApiExceptionNotFound
     * @throws DBALException
     * @throws DoctrineDBALException
     */
    public function geocodeBeans(array $targetBeans, array $geocodeBeans): bool
    {
        if (empty($targetBeans) || empty($geocodeBeans)) {
            return false;
        }

        $addressesData = [
            'addresses_data' => [],
        ];

        foreach ($geocodeBeans as $geocodeBean) {
            $targetBeanId = $geocodeBean->parent_id;
            $targetBean = $targetBeans[$targetBeanId];

            $addressData = [];
            $address = [];
            $moduleName = $targetBean->module_name;

            $mappingTable = $this->getGeocodingMapping($moduleName);

            foreach ($mappingTable as $clientKey => $sugarKey) {
                $address[$clientKey] = $this->getFormattedValue($targetBean, $sugarKey);
            }

            $addressString = implode('|', $address);
            $addressHash = md5($addressString);

            $addressData['address'] = $address;
            $addressData['sugar_id'] = $geocodeBean->id;
            $addressData['sugar_module'] = $geocodeBean->module_name;
            $addressData['hash'] = $addressHash;

            $addressesData['addresses_data'][] = $addressData;
        }

        $geocodeScheduler = BeanFactory::newBean(MapsGlobalConstants::GEOCODE_SCHEDULER_MODULE);

        $geocodeScheduler->status = MapsGlobalConstants::GEOCODE_SCHEDULER_STATUS_QUEUED;
        $geocodeScheduler->addresses_data = json_encode($addressesData);

        $addressesData['batch_id'] = $geocodeScheduler->save();

        $this->getContainer()->client->sendRecordsToGCS($addressesData);

        return true;
    }

    /**
     *  Save the coordonates on the bean
     *
     * @param SugarBean $targetBean
     * @param SugarBean $geocodeBean
     *
     * @return bool
     *
     * @throws UnsatisfiedDependencyException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws SugarApiExceptionNotFound
     * @throws DBALException
     * @throws DoctrineDBALException
     */
    public function geocodeBean(SugarBean $targetBean, SugarBean $geocodeBean): bool
    {
        $address = [];
        $moduleName = $targetBean->module_name;
        $beanId = $targetBean->id;
        $mappingTable = $this->getGeocodingMapping($moduleName);

        foreach ($mappingTable as $clientKey => $sugarKey) {
            $address[$clientKey] = $this->getFormattedValue($targetBean, $sugarKey);
        }

        $coords = $this->getContainer()->client->getCoordsByAddress($address);

        if (empty($coords)) {
            $message = sprintf(
                'Invalid Address[GeocodeMaps][%s][%s]: %s',
                $moduleName,
                $beanId,
                json_encode($address)
            );

            $this->getContainer()->logger->critical($message);

            return false;
        }

        $this->setCoords($geocodeBean, $coords);

        $geocodeBean->save();

        return true;
    }

    /**
     * Retreive required bean fields
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getBeanFields(string $moduleName): array
    {
        $beanFields = array_values($this->getGeocodingMapping($moduleName));
        $beanFields[] = 'id';
        $beanFields[] = 'deleted';

        return $beanFields;
    }

    /**
     * Set coordonates on given bean
     *
     * @param SugarBean $bean
     * @param array $coords
     */
    protected function setCoords(SugarBean $bean, array $coords)
    {
        $bean->latitude = $coords['lat'];
        $bean->longitude = $coords['long'];
        $bean->geocoded = true;
    }

    /**
     * Returns encoded url value
     *
     * @param SugarBean $bean
     * @param string $key
     *
     * @return string
     */
    public function getFormattedValue(SugarBean $bean, string $key): string
    {
        if (!property_exists($bean, $key)) {
            return '';
        }

        /**
         * Replace non-alphanumeric characters with empty space
         */
        $regex = '~[^\p{L}\p{N}\n]+~u';

        $value = $bean->{$key};
        $value = preg_replace($regex, ' ', (string)$value);
        $value = trim($value);

        return $value;
    }

    /**
     * Mapping between SugarCRM and Map Api
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getGeocodingMapping(string $moduleName): array
    {
        $admin = BeanFactory::getBean('Administration');

        $mapsConfig = $admin->retrieveSettings('maps', true)->settings;

        if (array_key_exists('maps_modulesData', $mapsConfig)) {
            $modulesData = $mapsConfig['maps_modulesData'];

            if (array_key_exists($moduleName, $modulesData)
                && array_key_exists('mappings', $modulesData[$moduleName])) {
                return $modulesData[$moduleName]['mappings'];
            }
        }

        return [
            'addressLine' => '',
            'locality' => '',
            'adminDistrict' => '',
            'postalCode' => '',
            'countryRegion' => '',
        ];
    }

    /**
     *
     * @return Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Container
     */
    protected function getContainer(): Container
    {
        if (!$this->container) {
            $this->container = Container::getInstance();
        }

        return $this->container;
    }
}
