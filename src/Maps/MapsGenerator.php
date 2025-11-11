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

use Exception;
use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;
use TCPDF;

/**
 *
 * Maps Generator
 *
 */
class MapsGenerator
{
    /**
     * @var string
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerLicenseKey;

    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * constructor
     *
     * @param string $provider
     * @param string $providerLicenseKey
     */
    public function __construct(string $provider, string $providerLicenseKey)
    {
        $this->provider = $provider;
        $this->providerLicenseKey = $providerLicenseKey;
        $this->guzzle = new GuzzleClient();
    }

    /**
     * Generate PDF map
     *
     * @param array $recordsMeta
     * @param array $mapMeta
     *
     * @return string
     */
    public function generatePdfMap(array $recordsMeta, array $mapMeta): string
    {
        if ($this->provider === 'bing') {
            return $this->generatePdfBingMap($recordsMeta, $mapMeta);
        }

        throw new SugarApiExceptionNotAuthorized(translate('LBL_AUTH_FAILED_TITLE'));
    }

    /**
     * Generate PDF bing map
     *
     * @param array $recordsMeta
     * @param array $mapMeta
     *
     * @return string
     */
    private function generatePdfBingMap(array $recordsMeta, array $mapMeta): string
    {
        $fromDirections = $mapMeta['fromDirections'];

        if ($fromDirections) {
            return $this->getDirectionsPdfMapFromBing($recordsMeta, $mapMeta);
        }

        return $this->getSimplePdfMapFromBing($recordsMeta, $mapMeta);
    }

    /**
     * Generate directions PDF map from Bing
     *
     * @param array $recordsMeta
     * @param array $mapMeta
     *
     * @return string
     */
    private function getDirectionsPdfMapFromBing(array $recordsMeta, array $mapMeta): string
    {
        $mapType = 'directions';
        $itinerary = $mapMeta['itinerary'];
        $isMapExpanded = $mapMeta['mapExpanded'];

        $mapUrl = $this->bingGenerateLink($recordsMeta, $mapMeta, true);
        $directionsItinerary = $this->generateDirectionsItinerary($itinerary, $recordsMeta);
        $mapContentHtml = $this->generateMapHtml($isMapExpanded, $mapUrl, $recordsMeta, $directionsItinerary, $mapType);

        $pdf = $this->createPDF($isMapExpanded, $mapContentHtml, true);

        return $pdf;
    }

    /**
     * Generate simple PDF map from Bing
     *
     * @param array $recordsMeta
     * @param array $mapMeta
     *
     * @return string
     */
    private function getSimplePdfMapFromBing(array $recordsMeta, array $mapMeta): string
    {
        $mapType = 'sample';
        $isMapExpanded = $mapMeta['mapExpanded'];

        $mapUrl = $this->bingGenerateLink($recordsMeta, $mapMeta, false);
        $mapContentHtml = $this->generateMapHtml($isMapExpanded, $mapUrl, $recordsMeta, [], $mapType);

        $pdf = $this->createPDF($isMapExpanded, $mapContentHtml, true);

        return $pdf;
    }

    /**
     * Generate simple link for map
     *
     * @param array $recordsMeta
     * @param array $mapMeta
     * @param boolean $fromDirections
     *
     * @return string
     */
    private function bingGenerateLink(array &$recordsMeta, array $mapMeta, bool $fromDirections = false): string
    {
        $mapExpanded = $mapMeta['mapExpanded'];
        $mapCenterBounds = $mapMeta['mapCenter'];
        $mapCenterLat = $mapCenterBounds['latitude'];
        $mapCenterLong = $mapCenterBounds['longitude'];

        $format = 'format=png';
        $zoom = $mapMeta['mapZoom'] - 1;
        $mapSize = $mapExpanded ? 'mapSize=1120,620' : 'mapSize=800,400';
        $path = 'Imagery/Map';
        $key = "key={$this->providerLicenseKey}";
        $centerPoint = "{$mapCenterLat},{$mapCenterLong}";

        if ($fromDirections) {
            $pushPins = $this->bingGeneratePushPins($recordsMeta, true, true);
        } else {
            $pushPins = $this->bingGeneratePushPins($recordsMeta, false, true);
        }

        $pushPins = implode($pushPins);

        $mapType = $this->bingGetMapType($mapMeta);
        $baseUrl = $this->bingMapUrl();

        $pathType = $fromDirections ? 'Routes?' : '?';

        $url = "{$baseUrl}/{$path}/{$mapType}/{$centerPoint}/{$zoom}/{$pathType}{$pushPins}{$mapSize}&{$format}&{$key}";

        return $url;
    }

    /**
     * Get Map from bing function
     *
     * @param string $url
     * @param array $queryParams
     *
     * @return mixed
     * @throws SugarApiExceptionServiceUnavailable
     */
    private function downloadMapFromBing(string $url, array $queryParams)
    {
        $query = GuzzleHttp\Psr7\Query::build($queryParams);

        $options = [
            'query' => $query,
        ];

        $response = $this->guzzle->get($url, $options);

        $statusCode = $response->getStatusCode();
        $mapBody = $response->getBody();

        if (!$mapBody || $statusCode !== 200) {
            $errorStatusCode = 422;

            throw new \SugarApiExceptionServiceUnavailable(null, null, null, $errorStatusCode);
        }

        $mapContent = $mapBody->getContents();

        return $mapContent;
    }

    /**
     * Generat direction itinerary function
     *
     * @param array $itinerary
     * @param array $recordsMeta
     *
     * @return array
     */
    private function generateDirectionsItinerary(array $itinerary, array $recordsMeta): array
    {
        $directionsItinerary = [];

        $routeSummary = $itinerary['routeSummary'][0]; //first is the optimal route
        $route = $itinerary['route'][0];

        $directionsItinerary['travelDistance'] = $this->formatDirectionsTravelDistance($routeSummary['distance']);
        $directionsItinerary['travelDuration'] = $this->formatSecondsToTime($routeSummary['time']);
        $directionsItinerary['travelDurationTraffic'] = $this->formatSecondsToTime($routeSummary['timeWithTraffic']);
        $directionsItinerary['itinerary'] = [];

        if (!$route || !array_key_exists('routeLegs', $route) || sizeof($route['routeLegs']) < 1) {
            return $directionsItinerary;
        }

        $charCodeA = ord('A');

        for ($i = 0; $i < sizeof($route['routeLegs']); $i++) {
            $currentRouteLeg = $route['routeLegs'][$i];

            $itineraryTravelDistance = $this->formatDirectionsTravelDistance($currentRouteLeg['summary']['distance']);
            $itineraryTravelDuration = $this->formatSecondsToTime($currentRouteLeg['summary']['time']);

            $directionsItinerary['itinerary'][$i]['travelDistance'] = $itineraryTravelDistance;
            $directionsItinerary['itinerary'][$i]['travelDuration'] = $itineraryTravelDuration;

            $direction = chr($i + $charCodeA) . ' to ' . chr($i + 1 + $charCodeA);
            $startPoint = chr($i + $charCodeA) . ' (' . $recordsMeta[$i]['parent_name'] . ')';
            $endPoint = chr($i + 1 + $charCodeA) . ' (' . $recordsMeta[$i + 1]['parent_name'] . ')';

            $directionsItinerary['itinerary'][$i]['direction'] = $direction;
            $directionsItinerary['itinerary'][$i]['startPoint'] = $startPoint;
            $directionsItinerary['itinerary'][$i]['endPoint'] = $endPoint;

            $itineraryItemsExists = array_key_exists('itineraryItems', $currentRouteLeg);

            if (!$itineraryItemsExists || sizeof($currentRouteLeg['itineraryItems']) < 1) {
                return $currentRouteLeg;
            }

            $itineraryItems = $currentRouteLeg['itineraryItems'];

            $directionsItinerary['itinerary'][$i]['steps'] = $this->generateDirectionsItinerarySteps($itineraryItems);
        }

        return $directionsItinerary;
    }

    /**
     * format travel distance for directions function
     *
     * @param int|float $distance
     *
     * @return string
     */
    private function formatDirectionsTravelDistance($distance): string
    {
        $travelDistanceMiles = $this->kmToMiles($distance);
        $travelDistanceRounded = round($distance, 2);

        $travelDistance = "{$travelDistanceMiles} ({$travelDistanceRounded} km)";

        return $travelDistance;
    }

    /**
     * Generate Directions Steps
     *
     * @param array $itineraryItems
     *
     * @return array
     */
    private function generateDirectionsItinerarySteps(array $itineraryItems): array
    {
        $steps = [];

        for ($i = 0; $i < sizeof($itineraryItems); $i++) {
            $step = [];
            $itineraryItem = $itineraryItems[$i];

            $step['text'] = htmlspecialchars($itineraryItem['preIntersectionHints'][0], ENT_COMPAT);
            $step['maneuver'] = $itineraryItem['maneuver'];
            $step['travelDistance'] = $this->formatDirectionsTravelDistance($itineraryItem['distance']);
            $step['travelDuration'] = $this->formatSecondsToTime($itineraryItem['durationInSeconds']);
            $step['warnings'] = [];
            $step['hints'] = [];

            if (array_key_exists('warnings', $itineraryItem)) {
                foreach ($itineraryItem['warnings'] as $warning) {
                    $stepWarning = [];

                    $stepWarning['severity'] = $warning['severity'];
                    $stepWarning['text'] = $warning['text'];
                    $stepWarning['warningType'] = $warning['warningType'];

                    $step['warnings'][] = $stepWarning;
                }
            }

            if (array_key_exists('hints', $itineraryItem)) {
                foreach ($itineraryItem['hints'] as $hint) {
                    $stepHint = [];

                    $stepHint['hintType'] = $hint['hintType'];
                    $stepHint['text'] = $hint['text'];

                    $step['hints'][] = $stepHint;
                }
            }

            $steps[] = $step;
        }

        return $steps;
    }

    /**
     * Generate Bing map function
     *
     * @param bool $isMapExpanded
     * @param string $mapUrl
     * @param array $recordsMeta
     * @param array $directionsItinerary
     * @param string $type
     *
     * @return string
     */
    private function generateMapHtml(
        bool   $isMapExpanded,
        string $mapUrl,
        array  $recordsMeta,
        array  $directionsItinerary,
        string $type
    ): string {

        if ($type === 'directions') {
            return $this->generateDirectionsPdfMapHtml($isMapExpanded, $mapUrl, $recordsMeta, $directionsItinerary);
        }

        return $this->generateSimplePdfMapHtml($isMapExpanded, $mapUrl, $recordsMeta);
    }

    /**
     * Generate simple html for pdf from smarty
     *
     * @param bool $isMapExpanded
     * @param string $mapUrl
     * @param array $records
     *
     * @return string
     */
    private function generateSimplePdfMapHtml(bool $isMapExpanded, string $mapUrl, array $records): string
    {
        $ss = new \Sugar_Smarty();

        $ss->assign('isMapExpanded', $isMapExpanded);
        $ss->assign('logoUrl', './themes/default/images/company_logo.png');
        $ss->assign('map', $mapUrl);
        $ss->assign('mapPoints', translate('LBL_MAPS_POINTS'));
        $ss->assign('headerName', translate('LBL_NAME'));
        $ss->assign('headerLat', translate('LBL_MAP_LATITUDE'));
        $ss->assign('headerLong', translate('LBL_MAP_LONGITUDE'));
        $ss->assign('headerAddress', translate('LBL_MAP_ADDRESS'));
        $ss->assign('headerPoint', translate('LBL_MAPS_POINT'));
        $ss->assign('recordsMeta', $records);

        $htmlTemplate = to_html($ss->fetch('src/Maps/Templates/SampleMapPdfTemplate.tpl'));

        return $htmlTemplate;
    }

    /**
     * Generate simple html for pdf from smarty
     *
     * @param bool $isMapExpanded
     * @param string $mapUrl
     * @param array $records
     * @param array $directionsItinerary
     *
     * @return string
     */
    private function generateDirectionsPdfMapHtml(
        bool   $isMapExpanded,
        string $mapUrl,
        array  $records,
        array  $directionsItinerary
    ): string {

        $ss = new \Sugar_Smarty();

        $ss->assign('isMapExpanded', $isMapExpanded);
        $ss->assign('logoUrl', './themes/default/images/company_logo.png');
        $ss->assign('map', $mapUrl);
        $ss->assign('mapPoints', translate('LBL_MAPS_POINTS'));
        $ss->assign('headerName', translate('LBL_NAME'));
        $ss->assign('headerLat', translate('LBL_MAP_LATITUDE'));
        $ss->assign('headerLong', translate('LBL_MAP_LONGITUDE'));
        $ss->assign('headerAddress', translate('LBL_MAP_ADDRESS'));
        $ss->assign('headerPoint', translate('LBL_MAPS_POINT'));
        $ss->assign('recordsMeta', $records);
        $ss->assign('travelDetails', translate('LBL_MAPS_TRAVEL_DETAILS'));
        $ss->assign('totalDistanceLbl', translate('LBL_MAPS_TOTAL_DISTANCE'));
        $ss->assign('totalDistance', $directionsItinerary['travelDistance']);
        $ss->assign('totalDurationLbl', translate('LBL_MAPS_TOTAL_DURATION'));
        $ss->assign('totalDuration', $directionsItinerary['travelDuration']);
        $ss->assign('totalDurationWithoutTraficLbl', translate('LBL_MAPS_TOTAL_DURATION_WITHOUT_TRAFIC'));
        $ss->assign('totalDurationWithoutTrafic', $directionsItinerary['travelDurationTraffic']);
        $ss->assign('itineraryLbl', translate('LBL_MAPS_ITINERARY'));
        $ss->assign('itinerary', $directionsItinerary['itinerary']);
        $ss->assign('fromLbl', translate('LBL_FROM'));
        $ss->assign('toLbl', translate('LBL_TO'));
        $ss->assign('travelDistanceLbl', translate('LBL_MAPS_TRAVEL_DISTANCE'));
        $ss->assign('travelDurationLbl', translate('LBL_MAPS_TRAVEL_DURATION'));
        $ss->assign('travelStepsLbl', translate('LBL_MAPS_TRAVEL_STEPS'));
        $ss->assign('travelWarningsLbl', translate('LBL_MAPS_TRAVEL_WARNINGS'));

        $htmlTemplate = to_html($ss->fetch('src/Maps/Templates/DirectionsMapPdfTemplate.tpl'));

        return $htmlTemplate;
    }

    /**
     * Create PDF
     *
     * @param boolean $isMapExpanded
     * @param string $htmlPage
     * @param boolean $encode
     *
     * @return string
     */
    private function createPDF(bool $isMapExpanded, string $htmlPage, bool $encode = false): string
    {
        require_once 'vendor/tcpdf/tcpdf.php';

        $pdf = new TCPDF('L', 'mm', 'Letter', true, 'UTF-8');

        $imageScale = $isMapExpanded ? 2.1 : 1.5;

        $pdf->setPrintFooter(true);
        $pdf->SetCreator('SugarCRM');
        $pdf->SetAuthor('SugarCRM');
        $pdf->AddPage('P', 'A4');
        $pdf->setImageScale($imageScale);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->writeHTML($htmlPage, true, false, true, false, '');

        $pdfData = $pdf->Output('', 'S');

        if ($encode) {
            $pdfData = base64_encode($pdfData);
        }

        return $pdfData;
    }

    /**
     * Generate pushpins for map
     *
     * @param array $recordsMeta
     * @param boolean $fromDirections
     * @param boolean $isUrl
     *
     * @return array
     */
    private function bingGeneratePushPins(array &$recordsMeta, bool $fromDirections, bool $isUrl = false): array
    {
        $pushPinIconStyle = 66;
        $letters = array_merge(range('A', 'Z'), range('a', 'z'));

        $pushPins = [];

        for ($i = 0; $i < sizeof($recordsMeta); $i++) {
            $record = $recordsMeta[$i];

            $long = $record['longitude'];
            $lat = $record['latitude'];
            $letter = $i >= sizeof($letters) ? $i : $letters[$i];

            $recordsMeta[$i]['point'] = $letter;

            $pushPin = "{$lat},{$long};{$pushPinIconStyle};{$letter}";

            if ($fromDirections && $isUrl) {
                $pushPin = "wp.{$i}={$pushPin}&";
            } elseif ($isUrl) {
                $pushPin = "pp={$pushPin}&";
            }

            $pushPins [] = $pushPin;
        }

        return $pushPins;
    }

    /**
     * Get map type
     *
     * @param array $recordsMeta
     *
     * @return string
     */
    private function bingGetMapType(array $recordsMeta): string
    {
        $mapType = $recordsMeta['mapType'];

        if ($mapType === 'r') {
            return 'Road';
        } elseif ($mapType === 'a') {
            return 'Aerial';
        }

        return 'Road';
    }

    /**
     * format seconds to readable time function
     *
     * @param int|float $seconds
     *
     * @return string
     */
    private function formatSecondsToTime($seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);

        if ($minutes == 0) {
            return "{$hours} hr {$minutes} min {$seconds} sec";
        }

        return "{$hours} hr {$minutes} min";
    }

    /**
     * kmToMiles function
     *
     * @param int|float $km
     * @return string
     */
    private function kmToMiles($km): string
    {
        $miles = $km / 1.609344;
        $result = round($miles, 2);

        return $result . ' mi';
    }

    /**
     * Base bing map generate url
     *
     * @return string
     */
    private function bingMapUrl(): string
    {
        return 'http://dev.virtualearth.net/REST/v1';
    }
}
