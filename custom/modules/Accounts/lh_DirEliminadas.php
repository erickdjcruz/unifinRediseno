<?php
/*
  * LH para procesar y actualizar direcciones, "eliminadas", asociadas a una Persona.
*/
//Agrega referencia s
require_once("custom/Levementum/DropdownValuesHelper.php");
require_once("custom/Levementum/UnifinAPI.php");

class DirEliminada_Class
{
  function DirEliminada_Method($bean, $event, $arguments)
  {
    /*
      *1.- Proceso para recuperar direcciones eliminadas sin relación eliminada 
    */
    //Inicia proceso
    $GLOBALS['log']->fatal ('BuscaDirEliminadas: Inicia');
    //Ejecuta consulta
    $query = "update accounts_dire_direccion_1_c ad
  	  inner join dire_direccion d on d.id = ad.accounts_dire_direccion_1dire_direccion_idb
      set ad.deleted=1
      where 
      d.deleted=1
      and ad.deleted=0
      and ad.accounts_dire_direccion_1accounts_ida = '{$bean->id}';";

    //Ejecuta consultaa
    $resultQ = $GLOBALS['db']->query($query);
    $GLOBALS['log']->fatal ('BuscaDirEliminadas: Ejecuta update para depurar direcciones eliminadas');

    //Concluye proceso
    $GLOBALS['log']->fatal ('BuscaDirEliminadas: Concluye');


    /*
      * 2.- Proceso para recuperar direcciones eliminadas y enviar a unics
    */
    //Inicia proceso
    $GLOBALS['log']->fatal ('DirEliminadas: Inicia');

    //Estructura consulta
    $query = "SELECT distinct dire_direccion.id AS direccionId, accounts.id AS accountId, dire_direccion_dire_pais_c.dire_direccion_dire_paisdire_pais_ida AS paisId, dire_direccion_dire_estado_c.id AS EstadoId, dire_direccion_dire_municipio_c.dire_direccion_dire_municipiodire_municipio_ida AS MunicipioId, dire_direccion_dire_ciudad_c.dire_direccion_dire_ciudaddire_ciudad_ida AS CiudadId, dire_direccion_dire_codigopostal_c.dire_direccion_dire_codigopostaldire_codigopostal_ida AS CodigoPostalId, dire_direccion.tipodedireccion, dire_direccion.calle, dire_direccion.numext, dire_codigopostal.name AS codigoPostal, dire_ciudad.name AS Ciudad, dire_colonia.name AS Colonia, dire_direccion.inactivo, dire_direccion.numint, dire_direccion.secuencia, accounts_cstm.idcliente_c, dire_pais.name AS Pais, dire_estado.name AS Estado, dire_direccion.indicador, dire_estado.id AS EstadoId, dire_pais.Id AS PaisId
    FROM dire_direccion
    LEFT JOIN accounts_dire_direccion_1_c ON accounts_dire_direccion_1_c.accounts_dire_direccion_1dire_direccion_idb = dire_direccion.id AND accounts_dire_direccion_1_c.deleted = 1
    LEFT JOIN dire_direccion_dire_pais_c ON dire_direccion_dire_pais_c.dire_direccion_dire_paisdire_direccion_idb = dire_direccion.id --  AND dire_direccion_dire_pais_c.deleted = 0
    LEFT JOIN dire_direccion_dire_estado_c ON dire_direccion_dire_estado_c.dire_direccion_dire_estadodire_direccion_idb = dire_direccion.id -- AND dire_direccion_dire_estado_c.deleted = 0
    LEFT JOIN dire_direccion_dire_municipio_c ON dire_direccion_dire_municipio_c.dire_direccion_dire_municipiodire_direccion_idb = dire_direccion.id -- AND dire_direccion_dire_municipio_c.deleted = 0
    LEFT JOIN dire_direccion_dire_ciudad_c ON dire_direccion_dire_ciudad_c.dire_direccion_dire_ciudaddire_direccion_idb = dire_direccion.id -- AND dire_direccion_dire_ciudad_c.deleted = 0
    LEFT JOIN dire_direccion_dire_codigopostal_c ON dire_direccion_dire_codigopostal_c.dire_direccion_dire_codigopostaldire_direccion_idb = dire_direccion.id -- AND dire_direccion_dire_codigopostal_c.deleted = 0
    LEFT JOIN dire_direccion_dire_colonia_c ON dire_direccion_dire_colonia_c.dire_direccion_dire_coloniadire_direccion_idb = dire_direccion.id -- AND dire_direccion_dire_colonia_c.deleted = 0
    LEFT JOIN dire_codigopostal ON dire_codigopostal.id = dire_direccion_dire_codigopostal_c.dire_direccion_dire_codigopostaldire_codigopostal_ida -- AND dire_codigopostal.deleted = 0
    LEFT JOIN dire_ciudad ON dire_ciudad.id = dire_direccion_dire_ciudad_c.dire_direccion_dire_ciudaddire_ciudad_ida -- AND dire_ciudad.deleted = 0
    LEFT JOIN dire_colonia ON dire_colonia.id = dire_direccion_dire_colonia_c.dire_direccion_dire_coloniadire_colonia_ida -- AND dire_colonia.deleted = 0
    LEFT JOIN dire_pais ON dire_pais.id = dire_direccion_dire_pais_c.dire_direccion_dire_paisdire_pais_ida -- AND dire_pais.deleted = 0
    LEFT JOIN dire_estado ON dire_estado.id = dire_direccion_dire_estado_c.dire_direccion_dire_estadodire_estado_ida -- AND dire_estado.deleted = 0
    LEFT JOIN accounts ON accounts.id = accounts_dire_direccion_1_c.accounts_dire_direccion_1accounts_ida -- AND accounts.deleted = 0
    INNER JOIN accounts_cstm ON accounts_cstm.id_c = accounts.id
    WHERE accounts.id = '{$bean->id}' AND accounts.deleted = 0
    ;";

    //Ejecuta consultaa
    $resultQ = $GLOBALS['db']->query($query);
    $GLOBALS['log']->fatal ('DirEliminadas: Ejecuta consulta');

    //Procesa registros recuperados
    $fields = array();
    $IntValue = new DropdownValuesHelper();

    while ($row = $GLOBALS['db']->fetchByAssoc($resultQ)) {
      $GLOBALS['log']->fatal ('DirEliminadas: Registro recuperado - '. $row['direccionId']);

      //Formatea valores
      $estadoId = $IntValue->getEstadoId($row['EstadoId']);
      $municipioId = $IntValue->getMunicipioId($row['MunicipioId']);
      $ciudadId = $IntValue->getCiudadId($row['CiudadId']);
      $codigoPostalId = $IntValue->getCodigoPostalId($row['CodigoPostalId']);

      $estado = '';
      if ($row["inactivo"] == 0) {
          $estado = "A";
      } else {
          $estado = "I";
      }

      $tipoDireccion = (explode("^", $row['tipodedireccion']));
      foreach ($tipoDireccion as $index => $value) {
          if ($value == '') {
              continue;
          }
          if ($value != '') {
              $direccionId = $value;
              break;
          }
      }

      //Form the Direcciones array with all of the info we gathered from the database and dropdowns
      /***CVV INCIO***/
      $fields[] = array(
        "_IdCliente" =>  intval($row['idcliente_c']),
        "_IdDrccConsecutivo" => intval($row['secuencia']),
        "_IdPais" => intval($row['PaisId']),
        "_IdEstado" => intval($estadoId),
        "_IdMunicipio" => intval($municipioId),
        "_IdCiudad" => intval($ciudadId),
        "_IdCodigoPostal" => intval($codigoPostalId),
        "_IdTipoDireccion" => intval($direccionId),
        "_DrccCalle" => $row['calle'],
        "_DrccNumeroExterior" => $row['numext'],
        "_DrccNumeroInterior" => $row['numint'],
        "_DrccCodigoPostal" => $row['codigoPostal'],
        "_DrccCiudad" => $row['Ciudad'],
        "_DrccColonia" => $row['Colonia'],
        "_DrccIndicadorEstado" =>  $estado,
        "_DrccIndicadores" =>  intval($row['indicador']),
        "_guidDireccion" =>  $row['direccionId'],
        "_deletedCRM" => 1
      );  
    }  
  
    //Proces direcciones
    $callApi = new UnifinAPI();
    global $db, $current_user;
    $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaDireccion";
    $GLOBALS['log']->fatal($host);
    
    foreach ($fields as $key => $value) {
      //***CVV INICIO***/
      try {
        $direcciones = array("oDireccion" => array($value));
        $GLOBALS['log']->fatal("DirEliminadas: Petición");
        $GLOBALS['log']->fatal($direcciones);
        //$time_start = microtime(true);
      
        $direccion = $callApi->unifinpostCall($host, $direcciones);
        $GLOBALS['log']->fatal("DirEliminadas: Resultado");
        $GLOBALS['log']->fatal($direccion);
      
      } catch (Exception $e) {
          $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ .  " <".$current_user->user_name."> : Error " . $e->getMessage());

          $this->setErrorLogFailRequest( "ActualizaDireccion", $bean, $host, json_encode($fields), $e->getMessage() );

      }
    }

    $GLOBALS['log']->fatal("DirEliminadas: Termina");

  }

  public function setErrorLogFailRequest( $endpoint, $bean, $url, $request, $response ){

    $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores S_seguros");
    require_once("custom/clients/base/api/ErrorLogApi.php");
    $apiErrorLog = new ErrorLogApi();
    $args = array(
      "integration"=> "Unics: ".$endpoint,
      "system"=> "Unics",
      "parent_type"=> "Accounts",
      "parent_id"=> $bean->id,
      "endpoint"=> $url,
      "request"=> $request,
      "response"=> $response
    );
    $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);

  }
}
?>