<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $sugar_config;
$GLOBALS['url_token_dynamics_vendors'] = $sugar_config['url_token_dynamics_vendors'];
$GLOBALS['grant_type_dynamics_vendors'] = $sugar_config['grant_type_dynamics_vendors'];
$GLOBALS['client_id_dynamics_vendors'] = $sugar_config['client_id_dynamics_vendors'];
$GLOBALS['client_secret_dynamics_vendors'] = $sugar_config['client_secret_dynamics_vendors'];
$GLOBALS['resource_dynamics_vendors'] = $sugar_config['resource_dynamics_vendors'];
$GLOBALS['url_api_dynamics_vendors'] = $sugar_config['url_api_dynamics_vendors'];

class ListadoFranquiciaVendors extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'ListadoFranquiciaVendors' => array(
                'reqType' => 'GET',
                'noLoginRequired' => true,
                'path' => array('ListadoFranquiciaVendors'),
                'pathVars' => array(''),
                'method' => 'getListaFranquiciaVendors',
                'shortHelp' => 'Obtiene lista de Franquicia Vendors',
            ),
        );
    }

    public function getListaFranquiciaVendors($api, $args)
    {
        $GLOBALS['log']->fatal("************ INICIA_CONSUMO_DE_API_EXTERNO_DE_FRANQUICIA_VENDORS ***************");
        // CACHE KEYS: Son nombres (claves) de cache que tú defines y usas para almacenar temporalmente
        $cacheKey = 'external_api_token_unifin';
        $expiresKey = 'external_api_token_expires_unifin';
        $currentTime = time();
        $accessToken = sugar_cache_retrieve($cacheKey);
        $tokenExpires = sugar_cache_retrieve($expiresKey);

        if (!$accessToken || !$tokenExpires || $currentTime >= $tokenExpires) {
            // Obtener nuevo token desde Azure
            $tokenUrl = $GLOBALS['url_token_dynamics_vendors'];
            // $GLOBALS['log']->fatal("TOKEN_URL ". $tokenUrl);
            $tokenParams = [
                'grant_type' => $GLOBALS['grant_type_dynamics_vendors'],
                'client_id' => $GLOBALS['client_id_dynamics_vendors'],
                'client_secret' => $GLOBALS['client_secret_dynamics_vendors'],
                'resource' => $GLOBALS['resource_dynamics_vendors'],
            ];
            // $GLOBALS['log']->fatal($tokenParams);

            $ch = curl_init($tokenUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //SE DESACTIVA VERIFICADOR DE SSL PARA PRUEBAS
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenParams));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            $tokenResponse = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);            

            $tokenData = json_decode($tokenResponse, true);
            // $GLOBALS['log']->fatal("TOKEN JSON DECODED: " . print_r($tokenData, true));

            if (!$tokenResponse || $httpCode >= 400) {
                return [
                    'error' => 'Error al obtener el token.',
                    'detalle' => $curlError,
                    'codigo_http' => $httpCode,
                    'respuesta_raw' => $tokenResponse,
                ];
            }

            if (empty($tokenData['access_token'])) {
                return ['error' => 'No se pudo obtener el token'];
            }

            $accessToken = $tokenData['access_token'];
            $expiresIn = isset($tokenData['expires_in']) ? intval($tokenData['expires_in']) : 3600;
            $tokenExpires = $currentTime + $expiresIn - 60;

            // Guardar en cache
            sugar_cache_put($cacheKey, $accessToken);
            sugar_cache_put($expiresKey, $tokenExpires);

            $GLOBALS['log']->fatal("Token obtenido y cacheado, expira en: " . date('c', $tokenExpires));
        } else {
            $GLOBALS['log']->fatal("Token obtenido desde cache");
        }

        // Consumir API externo de Dynamics - VENDORS
        // rawurlencode("dataAreaId eq 'ufin'") ⟶ dataAreaId%20eq%20%27ufin%27
        $apiUrl = $GLOBALS['url_api_dynamics_vendors'] . rawurlencode("dataAreaId eq 'ufin'");
        // $GLOBALS['log']->fatal("API_URL ". $apiUrl);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //SE DESACTIVA VERIFICADOR DE SSL PARA PRUEBAS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode >= 400) {
            return ['error' => 'Error al consumir el API externo.', 'detalle' => $curlError, 'codigo_http' => $httpCode];
        }

        $data = json_decode($response, true);
        if (!isset($data['value'])) {
            return ['error' => 'Respuesta inesperada del API externo.'];
        }

        $records = [];
        foreach ($data['value'] as $vendor) {
            // $GLOBALS['log']->fatal($vendor);
            if (!empty($vendor['AccountNum']) && !empty($vendor['Name'])) {
                $records[] = [
                    'id' => $vendor['AccountNum'],
                    'nombre' => $vendor['Name']
                ];
            }
        }

        return ['records' => $records];
    }
}
