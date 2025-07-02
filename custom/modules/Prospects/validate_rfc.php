<?php
require_once 'include/api/SugarApiException.php';
class class_validate_rfc
{
    public function func_validate_rfc($bean = null, $event = null, $args = null)
    {
        global $db;

        /*if(!$bean->excluye_campana_c){
            if($bean->rfc_c != '' && $bean->rfc_c != 'XXX010101XXX' && $bean->rfc_c != 'XXXX010101XXX' && $bean->estatus_po_c!='3') {
                $queryRFCPO = "SELECT p.id from prospects p, prospects_cstm pc where p.id = pc.id_c
                  and p.deleted = 0
                  and p.id <> '{$bean->id}'
                  and pc.rfc_c = '{$bean->rfc_c}';";
                $queryResultPO = $db->query($queryRFCPO);
                if($db->getRowCount($queryResultPO) > 0) throw new SugarApiExceptionInvalidParameter('No se puede guardar el registro. El RFC '.$bean->rfc_c.' ya exite en PO, favor de corregir');
                $queryRFCLead = "SELECT l.id from leads l, leads_cstm lc where l.id = lc.id_c
                  and l.deleted = 0 and (l.account_id is null or l.account_id <> '{$bean->id}')
                  and l.id <> '{$bean->id}'
                  and lc.rfc_c = '{$bean->rfc_c}'";
                $queryResultL1 = $db->query($queryRFCLead);
                if($db->getRowCount($queryResultL1) > 0) throw new SugarApiExceptionInvalidParameter('No se puede guardar el registro. El RFC '.$bean->rfc_c.' ya exite en Leads, favor de corregir');
                $queryRFCAccount = "SELECT a.id from accounts a, accounts_cstm ac where a.id = ac.id_c
                  and a.deleted = 0
                  and (ac.convertido_c is null or ac.convertido_c = 0)
                  and ac.rfc_c = '{$bean->rfc_c}'";
                $queryResultA1 = $db->query($queryRFCAccount);
                if($db->getRowCount($queryResultA1) > 0) throw new SugarApiExceptionInvalidParameter('No se puede guardar el registro. El RFC '.$bean->rfc_c.' ya exite en Cuentas, favor de corregir');
            }
          }
        */   
    }
}
