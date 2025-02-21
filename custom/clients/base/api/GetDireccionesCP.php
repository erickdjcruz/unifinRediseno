<?php
/**
 * Created by Salvador Lopez.
 * email: salvador.lopez@tactos.com.mx
 * Date: 26/06/18
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class GetDireccionesCP extends SugarApi
{

    /**
     * Registro de todas las rutas para consumir los servicios del API
     *
     */
    public function registerApiRest()
    {
        return array(
            //GET
            'retrieve' => array(
                //request type
                'reqType' => 'GET',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('DireccionesCP', '?','?'),
                //endpoint variables
                'pathVars' => array('module', 'cp','indice'),
                //method to call
                'method' => 'getAddressByCP',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método GET para obtener información relacionada al Código Postal',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
            //GET
            'retrieve_mex_cp' => array(
                //request type
                'reqType' => 'GET',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('DireccionesMexCP', '?','?'),
                //endpoint variables
                'pathVars' => array('module', 'cp','indice'),
                //method to call
                'method' => 'getAddressByCP',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método GET para obtener información relacionada al Código Postal - filtra CP de México',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
            //GET - Copia de DireccionesCP, pero con un atributo adicional - Municipio - Creado para no afectar sistemas que hoy consumen servicio actual
            'retrieve_cpm' => array(
                //request type
                'reqType' => 'GET',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('DireccionesCPM', '?','?','?'),
                //endpoint variables
                'pathVars' => array('module', 'cp','indice','municipio'),
                //method to call
                'method' => 'getAddressByCP',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método GET para obtener información relacionada al Código Postal y Municipio',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

        );

    }

    /**
     * Obtiene información relacionada al Código Postal
     *
     * Método que obtiene Pais, Estado, Municipio, Ciudad y Colonia filtrados por Código Posta
     *
     * @param array $api
     * @param array $args Array con los parámetros enviados para su procesamiento
     * @return array $arrPadre Array con Pais, Estados, Municipios, Ciudades y Colonias relacionadas al CP
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getAddressByCP($api, $args)
    {
        $nacional = isset($args['module']) && $args['module'] == 'DireccionesMexCP'  ? true : false;
        $cp=$args['cp'];
        $municipio = isset($args['municipio']) ? $args['municipio'] : '';
        $indice = "0";
        if (!empty($args['indice'])) {
          $indice = $args['indice'];
        }
        //$indice= (strval($args['indice'])=="")? "0" : $args['indice'];
        /*
        $query = "SELECT
  cp.id                                                   AS idCP,
  cp.name                                                 AS nameCP,
  cp_m.dire_codigopostal_dire_municipiodire_municipio_ida AS idMunicipio,
  m.name                                                  AS nameMunicipio,
  m_e.dire_municipio_dire_estadodire_estado_ida           AS idEstado,
  e.name                                                  AS nameEstado,
  e_p.dire_estado_dire_paisdire_pais_ida                  AS idPais,
  p.name                                                  AS namePais,
   c_e.dire_ciudad_dire_estadodire_ciudad_idb as idCiudad,
        c.name as nameCiudad,
  co.id                                                   AS idColonia,
  co.name                                                 AS nameColonia
FROM dire_codigopostal cp
  RIGHT JOIN dire_codigopostal_dire_municipio_c cp_m
    ON cp_m.dire_codigopostal_dire_municipiodire_codigopostal_idb = cp.id and cp_m.deleted=0
  RIGHT JOIN dire_municipio m
    ON m.id = cp_m.dire_codigopostal_dire_municipiodire_municipio_ida and m.deleted=0
  RIGHT JOIN dire_municipio_dire_estado_c m_e
    ON m_e.dire_municipio_dire_estadodire_municipio_idb = m.id and m_e.deleted=0
  RIGHT JOIN dire_estado e
    ON e.id = m_e.dire_municipio_dire_estadodire_estado_ida and e.deleted=0
  RIGHT JOIN dire_estado_dire_pais_c e_p
    ON e_p.dire_estado_dire_paisdire_estado_idb = e.id and e_p.deleted=0
  RIGHT JOIN dire_pais p
    ON p.id = e_p.dire_estado_dire_paisdire_pais_ida and p.deleted=0
   right join dire_ciudad_dire_estado_c c_e
  	on c_e.dire_ciudad_dire_estadodire_estado_ida = e.id and c_e.deleted=0
  right join dire_ciudad c
   	on c.id = c_e.dire_ciudad_dire_estadodire_ciudad_idb and c.deleted=0
  RIGHT JOIN dire_colonia co
  -- on co.codigo_postal = cp.name
    ON co.id LIKE concat(cp_m.dire_codigopostal_dire_municipiodire_municipio_ida, cp.name, '%') and co.deleted=0
WHERE cp.name = '{$cp}'";
        */

        $query="SELECT
        id as idCP,
        codigo_postal as nameCP,
        id_municipio as idMunicipio,
        municipio as nameMunicipio,
        id_estado as idEstado,
        estado as nameEstado,
        id_pais as idPais,
        pais as namePais,
        id_ciudad as idCiudad,
        ciudad as nameCiudad,
        id_colonia as idColonia,
        colonia as nameColonia
        FROM dir_sepomex
        WHERE codigo_postal='{$cp}'";
  
        //Valida existencia de municipio
        /*
        if(!empty($municipio)){
            $query .= " and m.name='{$municipio}'";
        }
        
        //Valida direcciones nacionales
        if($nacional){
            $query .= " and p.id='2'";
        }
        */

        //LOG Plataforma:
        $GLOBALS['log']->fatal("Consulta GetDireccionesCP - CP: " .$cp . ' - Usuario: ' . $GLOBALS['current_user']->user_name. ' - Plataforma: ' . $GLOBALS['service']->platform);
        $result = $GLOBALS['db']->query($query);

        $arrPadre= array('paises'=>array(),'municipios'=>array(),'estados'=>array(),'colonias'=>array(),'ciudades'=>array(),'ciudades_metadata'=>array());


        $pos=0;

        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {

            if($pos==0){
              $idCP=$row['idCP'];
              $codigoPostal=$row['nameCP'];
            }

            $idPais=$row['idPais'];
            $namePais = $row['namePais'];

            $idMunicipio=$row['idMunicipio'];
            $nameMunicipio = $row['nameMunicipio'];

            $idEstado=$row['idEstado'];
            $nameEstado = $row['nameEstado'];

            $idColonia=$row['idColonia'];
            $nameColonia = $row['nameColonia'];

            $idCiudad=$row['idCiudad'];
            $nameCiudad = $row['nameCiudad'];

            $arrPais=array('idPais'=>$idPais,'namePais'=>$namePais);
            $arrMunicipio=array('idMunicipio'=>$idMunicipio,'nameMunicipio'=>$nameMunicipio);
            $arrEstado=array('idEstado'=>$idEstado,'nameEstado'=>$nameEstado);
            $arrColonia=array('idColonia'=>$idColonia,'nameColonia'=>$nameColonia,'idCodigoPostal'=>$row['idCP'],'idMunicipio'=>$idMunicipio);
            //$arrColonia=array('idColonia'=>$idColonia,'nameColonia'=>$nameColonia);
            $arrCiudad=array('idCiudad'=>$idCiudad,'nameCiudad'=>$nameCiudad);

            $arrPadre['idCP']=$idCP;
            $arrPadre['nameCP']=$codigoPostal;
            $arrCiudadMetadata=array('estado_id'=>$idEstado,'id'=>$idCiudad,'name'=>$nameCiudad,'pais_id'=>$idPais);

            $arrPadre['paises'][$pos]=$arrPais;
            $arrPadre['municipios'][$pos]=$arrMunicipio;
            $arrPadre['estados'][$pos]=$arrEstado;
            $arrPadre['colonias'][$pos]=$arrColonia;
            $arrPadre['ciudades'][$pos]=$arrCiudad;
            $arrPadre['ciudades_metadata'][$idCiudad]=$arrCiudadMetadata;

            $pos++;
        }

        $arrPaises=$arrPadre['paises'];
        $arrMunicipios=$arrPadre['municipios'];
        $arrEstados=$arrPadre['estados'];
        $arrColonias=$arrPadre['colonias'];
        $arrCiudades=$arrPadre['ciudades'];

        $arrNewPaises=array_values(array_unique($arrPaises, SORT_REGULAR));
        $arrNewMunicipios=array_values(array_unique($arrMunicipios, SORT_REGULAR));
        $arrNewEstados=array_values(array_unique($arrEstados, SORT_REGULAR));
        $arrNewColonias=array_values(array_unique($arrColonias, SORT_REGULAR));
        $arrNewCiudades=array_values(array_unique($arrCiudades, SORT_REGULAR));

        //Ordena arreglos de Ciudades y colonias
        usort($arrNewColonias, fn ($a, $b) => strcmp($a["nameColonia"], $b["nameColonia"]));
        usort($arrNewCiudades, fn ($a, $b) => strcmp($a["nameCiudad"], $b["nameCiudad"]));

        $arrPadre['paises']=$arrNewPaises;
        $arrPadre['municipios']=$arrNewMunicipios;
        $arrPadre['estados']=$arrNewEstados;
        $arrPadre['colonias']=$arrNewColonias;
        $arrPadre['ciudades']=$arrNewCiudades;

        /*
        $queryIdCP="SELECT id FROM dire_codigopostal WHERE name='{$cp}' LIMIT 1;";
        $resultID = $GLOBALS['db']->query($queryIdCP);
        while ($row = $GLOBALS['db']->fetchByAssoc($resultID)) {
            $idCP=$row['id'];
        }
        */

        //$arrPadre['idCP']=$idCP;
        $arrPadre['indice']=$indice;

        return $arrPadre;

    }


}

?>
