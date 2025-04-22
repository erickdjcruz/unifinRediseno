<?php

class clase_ClasfSectorial
{
    function func_ClasfSectorial($bean, $event, $arguments)
    {
        $GLOBALS['log']->fatal("ACTUALIZA CLASIFICACION SECTORIAL CNBV");
        //Campo custom Clasificacion Sectorial
        $clasfSectorial = $bean->account_clasf_sectorial;
        //$GLOBALS['log']->fatal("ClasfSectorialCustom " . print_r($clasfSectorial, true));

        if (!empty($clasfSectorial)) {
            $bean_Resumen = BeanFactory::retrieveBean('tct02_Resumen', $bean->id);

            $bean->actividadeconomica_c = $clasfSectorial['ae']['id'];
            $bean_Resumen->id_actividad_economica_sat_c = $clasfSectorial['ResumenSAT']['aes']['id_actividad_economica_sat'];
            $bean_Resumen->actividad_economica_sat_c = $clasfSectorial['ResumenSAT']['aes']['actividad_economica_sat'];
            $bean_Resumen->save();
            // $bean->subsectoreconomico_c = $clasfSectorial['sse']['id'];
            // $bean->sectoreconomico_c = $clasfSectorial['se']['id'];
            // $bean->tct_macro_sector_ddw_c = $clasfSectorial['ms']['id'];
        }
    }
}
