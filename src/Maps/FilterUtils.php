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

use SugarBean;
use BeanFactory;
use SugarQuery;
use Sugarcrm\Sugarcrm\Maps\Logger;
use LoggerManager;

class FilterUtils
{
    /**
     * Get coords by zipcode from GCS database/Bing
     *
     * @param string $zipCode
     * @param string $country
     *
     * @return mixed
     */
    public static function getCoordsFromZip(string $zipCode, string $country)
    {
        $logger = new Logger(LoggerManager::getLogger());

        try {
            $coords = self::getDbCoordsFromZip($zipCode, $country);

            if (!$coords) {
                $data = self::getGCSCoordsFromZip($zipCode, $country);

                if (!is_array($data) || $data['geocoded'] !== true) {
                    return false;
                }

                $coords = [
                    'latitude' => $data['lat'],
                    'longitude' => $data['long'],
                ];

                return $coords;
            }

            return $coords;
        } catch (\Exception|\Throwable $e) {
            $logger->error("Maps: Failed to get coords from zip for zipCode: {$zipCode} country: {$country}");
        }

        return false;
    }

    /**
     * Get coords by record from database
     *
     * @param string $module
     * @param string $id
     *
     * @return mixed
     */
    public static function getDbCoordsFromRecord(string $module, string $id)
    {
        $geocodeBean = BeanFactory::newBean(Constants::GEOCODE_MODULE);

        $sq = new SugarQuery();
        $sq->select('latitude', 'longitude');
        $sq->from($geocodeBean)
            ->where()
            ->equals('parent_type', $module)
            ->equals('parent_id', $id)
            ->equals('geocoded', true)
            ->equals('status', Constants::GEOCODE_SCHEDULER_STATUS_COMPLETED);
        $sq->limit(1);

        $result = $geocodeBean->fetchFromQuery($sq, ['latitude', 'longitude']);

        if (empty($result)) {
            return false;
        }

        $beanId = array_keys($result)[0];

        $geocodeBeanResult = $result[$beanId];

        $coords = [
            'latitude' => $geocodeBeanResult->latitude,
            'longitude' => $geocodeBeanResult->longitude,
        ];

        return $coords;
    }

    /**
     * Get coords by zipcode from database
     *
     * @param string $zipCode
     * @param string $country
     *
     * @return mixed
     */
    public static function getDbCoordsFromZip(string $zipCode, string $country)
    {
        $geocodeBean = BeanFactory::newBean(Constants::GEOCODE_MODULE);

        $sq = new SugarQuery();
        $sq->select('latitude', 'longitude');
        $sq->from($geocodeBean)
            ->where()
            ->equals('postalcode', $zipCode)
            ->equals('country', $country)
            ->equals('geocoded', true)
            ->equals('status', Constants::GEOCODE_SCHEDULER_STATUS_COMPLETED);
        $sq->limit(1);

        $result = $geocodeBean->fetchFromQuery($sq, ['latitude', 'longitude']);

        if (empty($result)) {
            return false;
        }

        $beanId = array_keys($result)[0];

        $geocodeBeanResult = $result[$beanId];

        $coords = [
            'latitude' => $geocodeBeanResult->latitude,
            'longitude' => $geocodeBeanResult->longitude,
        ];

        return $coords;
    }


    /**
     * Get coords by zipcode from GCS
     *
     * @param string $zipCode
     * @param string $country
     *
     * @return array
     */
    public static function getGCSCoordsFromZip(string $zipCode, string $country): array
    {
        $gcsClient = new GCSClient();

        $resp = $gcsClient->getCoordsByZip($zipCode, $country);

        return $resp;
    }

    /**
     * Get the name of the coords table
     *
     * @return void
     */
    public static function getCoordsTableName(): string
    {
        $geocodeBean = BeanFactory::newBean(Constants::GEOCODE_MODULE);
        return $geocodeBean->table_name;
    }

    /**
     * Update the geocode status of the parent record
     *
     * @param array $geocodeBeansIds
     * @param string $status
     */
    public static function updateGeocodeStatuses(array $geocodeBeansIds, string $status)
    {
        foreach ($geocodeBeansIds as $geocodeBeanId) {
            $geocodeBean = BeanFactory::retrieveBean(Constants::GEOCODE_MODULE, $geocodeBeanId);
            $parentRecord = BeanFactory::retrieveBean($geocodeBean->parent_type, $geocodeBean->parent_id);

            if (!$parentRecord) {
                return;
            }

            foreach ($parentRecord->field_defs as $fieldName => $fieldMeta) {
                if ($fieldMeta['type'] === 'geocodestatus') {
                    $parentRecord->{$fieldName} = $status;
                }
            }

            $parentRecord->processed = true;
            $parentRecord->save();
        }
    }
}
