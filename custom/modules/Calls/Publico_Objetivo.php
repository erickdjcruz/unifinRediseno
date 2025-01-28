<?php

class Publico_Objetivo{

	//Establece Cancelado registro padre de Público Objetivo en caso de que el resultado Ilocalizable se haya establecido por 5ta ocasión
	function set_cancelado_po($bean=null, $event=null, $args=null){
		
		$resultado_actual = $bean->tct_resultado_llamada_ddw_c;
		//Ilocalizable
		if( $bean->parent_type == 'Prospects' ){
			
			//Establece origen de llamada PO
			$bean->origen_po_c = true;

			$id_prospecto = $bean->parent_id;
			$beanPO = BeanFactory::retrieveBean('Prospects', $id_prospecto, array('disable_row_level_security' => true));
			
			if( $resultado_actual == 'Ilocalizable' ){
				$numero_ilocalizable = 0;
				//Obtener llamadas relacionadas
				if ($beanPO->load_relationship('calls')) {
					$relatedBeans = $beanPO->calls->getBeans();
		
					if (!empty($relatedBeans)) {
						
						foreach ($relatedBeans as $call) {
							if( $call->tct_resultado_llamada_ddw_c == 'Ilocalizable' ){
								$numero_ilocalizable += 1;
								$GLOBALS['log']->fatal("ILOCALIZABLE CALL NUMERO". $numero_ilocalizable);
							}

						}
					}
				}

				//Obtener reuniones relacionadas
				if ($beanPO->load_relationship('meetings')) {
					$relatedMeetings = $beanPO->meetings->getBeans();
		
					if (!empty($relatedMeetings)) {
						
						foreach ($relatedMeetings as $meeting) {

							if( $meeting->resultado_c == '27' ){
								$numero_ilocalizable += 1;
								$GLOBALS['log']->fatal("ILOCALIZABLE MEETINGS NUMERO". $numero_ilocalizable);
							}

						}
					}
				}

				$GLOBALS['log']->fatal("PROSPECTO ILOCALIZABLE POR ". $numero_ilocalizable. " ocasión");

				if( $numero_ilocalizable >= 5 && $beanPO->estatus_po_c != '4' ){

					$GLOBALS['log']->fatal("El Público Objetivo ". $beanPO->id." se establece CANCELADO al ser ilocalizable por 5ta ocasión");
					$beanPO->estatus_po_c = '4';//Cancelado
					$beanPO->save();

				}else{
					$beanPO->estatus_po_c = '5';//Ilocalizable
					$beanPO->save();

				}
			}

			if( $resultado_actual == 'Cita' || $resultado_actual == '1er Contacto'  ){
				$beanPO->estatus_po_c = '1';//Nuevo
				$beanPO->save();
			}

			if( $resultado_actual == 'No_esta_Interesado' ){
				$beanPO->estatus_po_c = '6';//No interesado
				$beanPO->save();
			}

			if( $resultado_actual == 'Fuera_de_Perfil' ){
				$beanPO->estatus_po_c = '7';//Fuera_de_Perfil
				$beanPO->save();
			}

			if( $resultado_actual == 'Viable_Envio_Solicitud' ){
				$beanPO->estatus_po_c = '8';//Viable_Envio_Solicitud
				$beanPO->save();
			}
			
		} 
		
	}
}
