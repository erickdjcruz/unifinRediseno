<?php

class clase_ClasfSectorial
{
    function func_ClasfSectorial($bean, $event, $arguments)
    {
        $GLOBALS['log']->fatal("ACTUALIZA CLASIFICACION SECTORIAL CNBV");
        //Campo custom Clasificacion Sectorial
        $clasfSectorial = $bean->account_clasf_sectorial;
        // $GLOBALS['log']->fatal("ClasfSectorialCustom " . print_r($clasfSectorial, true));

        if (!empty($clasfSectorial)) {

            $bean->actividadeconomica_c = $clasfSectorial['ae']['id'];
            // $bean->subsectoreconomico_c = $clasfSectorial['sse']['id'];
            // $bean->sectoreconomico_c = $clasfSectorial['se']['id'];
            // $bean->tct_macro_sector_ddw_c = $clasfSectorial['ms']['id'];
        }
    }
}
