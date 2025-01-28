<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/12/2015
 * Time: 9:37 PM
 */
require_once("custom/Levementum/UnifinAPI.php");
class Rel_Relaciones_Hooks{

    public function SetName($bean=null,$event=null,$args=null){
        global $db;
        $query = <<<SQL
select id from rel_relaciones_accounts_1_c where deleted = 0 and rel_relaciones_accounts_1rel_relaciones_idb <> '{$bean->id}' 
and rel_relaciones_accounts_1accounts_ida = '{$bean->rel_relaciones_accounts_1accounts_ida}'
and rel_relaciones_accounts_1rel_relaciones_idb in 
(SELECT b.id_c FROM rel_relaciones a, rel_relaciones_cstm b WHERE a.id = b.id_c AND a.deleted = 0 AND b.account_id1_c = '{$bean->account_id1_c}');
SQL;
        $queryResult = $db->query($query);
        $row = $db->fetchByAssoc($queryResult);
		if($row)
		{
			$beanPersona = BeanFactory::getBean('Accounts', $bean->account_id1_c);
    		$persona = $beanPersona->name;
			$beanRelacion = BeanFactory::getBean('Accounts', $bean->rel_relaciones_accounts_1accounts_ida);
    		$relacion = $beanRelacion->name;
    		require_once 'include/api/SugarApiException.php';
    		throw new SugarApiExceptionInvalidParameter("La relación ".$persona." ya existe para la cuenta ".$relacion." Favor de verificarlo. Si deseas agregar una nueva Relación Activa para ".$persona.", favor de acceder a la Relación, editar y agregarlo.");
    	}
    	else
    	{
      		$query = <<<SQL
SELECT name FROM accounts WHERE id = '{$bean->account_id1_c}'
SQL;
    		$queryResult = $db->query($query);
			while($row = $db->fetchByAssoc($queryResult))
    		{
    			$bean->name = $row['name'];
   			}
    	}
    }

    public function insertarRelacionenUNICS($bean=null,$event=null,$args=null){
		//only for new records
		/*** CVV INICIO ***/
		//Debe validarse si el cliente ya  tiene id_UNICS
		global $db, $current_user;
        $GLOBALS['log']->fatal(" <".$current_user->user_name."> Entra a insertarRelacionenUNICS");
        $callApiAccounts = new UnifinAPI();
		try {
		    /*
		     * F. Javier G. Solar
		     * 20/08/2018
		     * Valida si la cuenta arelacionar esta en estado Lead y no ha sido sincronizda
		     * envia la petición
		    **/
            $CuentaC =  BeanFactory::getBean('Accounts',$bean->account_id1_c);
            if(($CuentaC->tipo_registro_cuenta_c=='1' || $CuentaC->tipo_registro_cuenta_c=='2') && $CuentaC->sincronizado_unics_c==0){
                $GLOBALS['log']->fatal(" el id de la cuenta es ingredsado por JA  " . $bean->account_id1_c);
                $CuentaC->idcliente_c =$callApiAccounts->generarFolios(1,$CuentaC);
                $GLOBALS['log']->fatal(" Folio de unix " . $CuentaC->idcliente_c);
                $actualizaIdClienteLead= <<<SQL
update accounts_cstm set idcliente_c = '{$CuentaC->idcliente_c}' where id_c = '{$CuentaC->id}';
SQL;
                $db->query($actualizaIdClienteLead);
                $lead = $callApiAccounts->insertarClienteCompleto($CuentaC);
			}

        $query = <<<SQL
SELECT acc.idcliente_c idCliente, acc.sincronizado_unics_c ClienteSincronizado,
contact.idcliente_c idRelacionado, contact.sincronizado_unics_c RelacionadoSincronizado, contact.id_c GuidRelacionado
FROM rel_relaciones_accounts_c rel
inner join accounts_cstm acc on rel.rel_relaciones_accountsaccounts_ida = acc.id_c
inner join rel_relaciones_cstm Relcontact on Relcontact.id_c = rel.rel_relaciones_accountsrel_relaciones_idb
inner join accounts_cstm contact on contact.id_c = Relcontact.account_id1_c
where rel.rel_relaciones_accountsrel_relaciones_idb = '{$bean->id}'
SQL;
            $GLOBALS['log']->fatal(" <".$current_user->user_name."> query" . $query);
			$queryResult = $db->query($query);
			while ($row = $db->fetchByAssoc($queryResult)) {
			$GLOBALS['log']->fatal(" <".$current_user->user_name."> : El valor de idCliente_c al agregar relacion: " . $row['idCliente'] . " El cliente se encuentra sincronizado con UNICS:". $row['ClienteSincronizado']);
			$callApi = new UnifinAPI();

			//Las relaciones solo se enviaran si el cliente ya se encuentra en UNICS
			if ($row['ClienteSincronizado'] == 1){
				//Si la persona no esta enviada a UNICS, debe sincronizarse antes de guardar la relación
                if ($row['RelacionadoSincronizado'] == 0){
                	$GLOBALS['log']->fatal(" <".$current_user->user_name."> : La persona relacionada NO se encuentra sincronizada con UNICS");
                	$contacto =  BeanFactory::getBean('Accounts', $row['GuidRelacionado']);
                	$GLOBALS['log']->fatal(" <".$current_user->user_name."> : Contenido de relacion en persona: " . print_r($contacto->tipo_relacion_c ,true));
                	$contacto->save();
				}

				///Envia la relación
				if (empty($bean->fetched_row['id'])) {
						$relacion = $callApi->creaRelacion($bean);
				}else{
                    // TODO Validar que la relación se encuentre sincronizada con UNICS antes de invocar el servicio de actualizarRelación
                    if($bean->fetched_row['sincronizado_unics_c'] == '1'){
                        $relacion = $callApi->ActualizaRelacion($bean);
                    }else{
                        $relacion = $callApi->creaRelacion($bean);
                    }
                }
                    //Actualiza el campo de sincronizdo UNICS de la relación
                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ .  " <".$current_user->user_name."> : RELACION " . print_r($relacion,true));
				if ($relacion == true){
					$fieldUnicsSincronize = <<<SQL
update rel_relaciones_cstm set sincronizado_unics_c = '1' where id_c = '{$bean->id}';
SQL;
                    $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ .  " <".$current_user->user_name."> : CONSULTA " . $fieldUnicsSincronize);
					$db->query($fieldUnicsSincronize);
				}
            }
		}

        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ .  " <".$current_user->user_name."> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ .  " <".$current_user->user_name."> : Error " . $e->getMessage());
        }
    }

    public function detectaEstadoRelacion($bean = null, $event = null, $args = null)
    {
        global $current_user;
        if (empty($bean->fetched_row['id'])) {
            $_SESSION['estadoRelacion'] = 'insertando';
        } else {
            $_SESSION['estadoRelacion'] = 'actualizando';
        }
        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : ESTADO: " . $_SESSION['estadoRelacion'] );
    }

    public function setAccount($bean=null,$event=null,$args=null){
		if(!$bean->rel_relaciones_accountsaccounts_ida) {
			global $db;
			//Inserta los datos a la otra tabla, copia del campo Relacion a Persona
			$bean->rel_relaciones_accountsaccounts_ida = $bean->rel_relaciones_accounts_1accounts_ida;
			$query = "insert ignore into rel_relaciones_accounts_c (select * from rel_relaciones_accounts_1_c where rel_relaciones_accounts_1accounts_ida = '{$bean->rel_relaciones_accounts_1accounts_ida}')";
			$queryResult = $db->query($query);
		}
	}

	public function SetRelacionesOtrosClientes($bean=null,$event=null,$args=null){
		
		$cuentaRelacionada=$bean->account_id1_c;
		
		if($cuentaRelacionada!=null && $cuentaRelacionada!=""){
			$bean->accounts_rel_relaciones_1accounts_ida=$cuentaRelacionada;
			$bean->load_relationship('accounts_rel_relaciones_1');
			$bean->accounts_rel_relaciones_1->add($cuentaRelacionada);  
		}

	}

	//Evita guardado de registro en caso de que se relacione una cuenta bloqueada
    function verifica_cuenta_bloqueada($bean=null, $event=null, $args=null){
        
        $id_cuenta = $bean->rel_relaciones_accounts_1accounts_ida;

        $api_bloqueo = new GetBloqueoCuenta();
        $args =array();
        $args['id_cuenta']=$id_cuenta;
        
        $responseBloqueo = $api_bloqueo->getBloqueoCuentaPorTipo(array(),$args);
        
        if( $responseBloqueo['bloqueo'] == 'SI' ){
            
            $tipos_bloqueo = $responseBloqueo['tipo'];
            require_once 'include/api/SugarApiException.php';
            require_once 'custom/include/api/CstmException.php';

			if( $_SESSION['platform'] == 'base'  || $_SESSION['platform'] == 'mobile'){
				throw new SugarApiExceptionInvalidParameter("El registro no se puede guardar ya que la cuenta relacionada se encuentra bloqueada por: ". implode(',',$tipos_bloqueo) );
			}else{
				throw new CstmException("El registro no se puede guardar ya que la cuenta relacionada se encuentra bloqueada por: ". implode(',',$tipos_bloqueo) );
			}
        }

		$id_cuenta_relacionada = $bean->account_id1_c;
        $args_rel=array();
        $args_rel['id_cuenta']=$id_cuenta_relacionada;
        
        $responseBloqueoRel = $api_bloqueo->getBloqueoCuentaPorTipo(array(),$args_rel);
        
        if( $responseBloqueoRel['bloqueo'] == 'SI' ){
            
            $tipos_bloqueo_rel = $responseBloqueoRel['tipo'];
            require_once 'include/api/SugarApiException.php';
			require_once 'custom/include/api/CstmException.php';

			if( $_SESSION['platform'] == 'base'  || $_SESSION['platform'] == 'mobile'){
				throw new SugarApiExceptionInvalidParameter("El registro no se puede guardar ya que la cuenta relacionada se encuentra bloqueada por: ". implode(',',$tipos_bloqueo_rel) );
			}else{
				throw new CstmException("El registro no se puede guardar ya que la cuenta relacionada se encuentra bloqueada por: ". implode(',',$tipos_bloqueo) );
			}
        }
        
    }
    
    public function obtenHistorialComercial($bean=null,$event=null,$args=null){
  		/*
        Función: Recuepera historial comercial del PO asociado a la cuenta que se está relacionando y hereda llamadas y reuniones a Cuenta principal
        Criterios:
          1) Cuenta relacionada tiene PO vinculado
        
        Acciones:
          1) Recuperar llamadas relacionadas a PO
          2) Cambiar valores:
              parent = Cliente principal
              po_origen_rel = PO Relacionado
              cuenta_origen_rel = Cuenta relacionada
          3) Recuperar reuniones relacionadas a PO
          4) Cambiar valores:
              parent = Cliente principal
              po_origen_rel = PO Relacionado
              cuenta_origen_rel = Cuenta relacionada
      */
      
      //Recupera datos de cuentas
      $GLOBALS['log']->fatal('Entra LH Historial PO');
      global $db;
      $beanContacto = BeanFactory::retrieveBean('Accounts', $bean->account_id1_c, array('disable_row_level_security' => true));
      $beanPrincipal = BeanFactory::retrieveBean('Accounts', $bean->rel_relaciones_accounts_1accounts_ida, array('disable_row_level_security' => true));
      $tieneSeguimientoComercial = false;
      $IdPO = '';
      
      $queryHC = "SELECT 'Calls' module, c.id , c.status, pc.id_c, pc.account_id2_c
          from calls c
          inner join prospects_cstm pc on pc.id_c = c.parent_id and c.parent_type='Prospects'
          where 
          c.status='Held'
          and pc.account_id2_c = '{$beanContacto->id}'
          union
          select 'Meetings' module, m.id , m.status, pc.id_c, pc.account_id2_c
          from meetings m
          inner join prospects_cstm pc on pc.id_c = m.parent_id and m.parent_type='Prospects'
          where 
          m.status='Held'
          and pc.account_id2_c = '{$beanContacto->id}'
          limit 1;";
      $GLOBALS['log']->fatal($queryHC);
      $queryResult = $db->query($queryHC);
      while ($row = $db->fetchByAssoc($queryResult)) {
          $tieneSeguimientoComercial = true;
          $idPO = $row['id_c'];
      }

  		//Valida criterios
  		if(isset($beanContacto->id) && isset($beanPrincipal->id) && $tieneSeguimientoComercial){
  			//Actualiza llamadas cstm
        $queryCC = "UPDATE calls_cstm cc
            	inner join calls c on c.id = cc.id_c
            	inner join prospects_cstm pc on pc.id_c = c.parent_id and c.parent_type='Prospects'
            set 
            	cc.prospect_id_c ='{$idPO}', cc.account_id1_c ='{$beanContacto->id}'
            where 
            	c.status='Held'
            	and pc.account_id2_c = '{$beanContacto->id}' ;";
        $db->query($queryCC);
        
        //Actualiza llamadas
        $queryC = "UPDATE calls c 
            inner join prospects_cstm pc on pc.id_c = c.parent_id and c.parent_type='Prospects'
            set 
            c.parent_id ='{$beanPrincipal->id}' , c.parent_type='Accounts'
            where 
            c.status='Held'
            and pc.account_id2_c = '{$beanContacto->id}' ;";
        $db->query($queryC);
        
        //Actualiza reuniones cstm
        $queryMC = "UPDATE meetings_cstm mc
            	inner join meetings m on m.id = mc.id_c
            	inner join prospects_cstm pc on pc.id_c = m.parent_id and m.parent_type='Prospects'
            set 
            	mc.prospect_id_c ='{$idPO}', mc.account_id_c ='{$beanContacto->id}'
            where 
            	m.status='Held'
            	and pc.account_id2_c = '{$beanContacto->id}' ;";
        $db->query($queryMC);
        
        //Actualiza reuniones
        $queryM = "UPDATE meetings m 
              	inner join prospects_cstm pc on pc.id_c = m.parent_id and m.parent_type='Prospects'
              set 
              	m.parent_id ='{$beanPrincipal->id}' , m.parent_type='Accounts'
              where 
              	m.status='Held'
              	and pc.account_id2_c = '{$beanContacto->id}' ;";
        $db->query($queryM);
        
  		}

  	}
}
