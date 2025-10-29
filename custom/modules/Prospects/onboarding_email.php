<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once("custom/Levementum/UnifinAPI.php");
class onboarding_c
{
    public function onboarding_f($bean = null, $event = null, $args = null){
        if(strtoupper($bean->fetched_row['email1']) != $bean->email1) {
			global $sugar_config;
            $callApi = new UnifinAPI();
            $hostOnboarding = $sugar_config['onboarding_url'];
            $tokenOnboarding = $sugar_config['onboarding_token'];
            $idPO = $bean->id;
            $urlOnboarding = $hostOnboarding ."contact_user/".$idPO;
            $body = array(
                "email" => $bean->email1
            );
			$GLOBALS['log']->fatal("Email Onboarding: ".$urlOnboarding);
			$GLOBALS['log']->fatal($body);
            $resp = $callApi->postOnboardingPO($urlOnboarding, $tokenOnboarding ,$body);
			$GLOBALS['log']->fatal($resp);
        }
    }
}