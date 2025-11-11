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

require_once 'modules/iCals/HTTP_WebDAV_Server_iCal.php';
require_once 'modules/Calendar_iCals/Calendar_iCal.php';

/**
 * Calendar access using WebDAV
 *
 * @access public
 */
class HTTP_WebDAV_Server_Calendar_iCal extends HTTP_WebDAV_Server_iCal
{
    /**
     * @var string|mixed
     */
    public $ical_publish_key;
    /**
     * @var string|mixed
     */
    public $calendars_uid;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->vcal_focus = new Calendar_iCal();

        $this->ical_publish_key = (array_key_exists('key', $_REQUEST)) ? strip_tags($_REQUEST['key']) : '';
        $this->calendars_uid = (array_key_exists('calendarsUID', $_REQUEST)) ?
            strip_tags($_REQUEST['calendarsUID']) : '';
    }

    /**
     * {@inheritDoc}
     */
    public function http_GET()
    {
        $validRequest = false;

        if ($this->vcal_type == 'ics') {
            if (!empty($this->ical_publish_key) && !empty($this->calendars_uid)) {
                $qb = DBManagerFactory::getConnection()->createQueryBuilder();
                $qb->select('calendar_configurations');
                $qb->from('calendar_ical_configs');
                $qb->where($qb->expr()->eq('id', "'$this->calendars_uid'"));

                $calConfigsRes = $qb->execute();
                $calendars = $calConfigsRes->fetchAll();

                if (is_array($calendars) && safeCount($calendars) === 1 &&
                    !empty($calendars[0]['calendar_configurations'])) {
                    $calendarConfigurations = json_decode($calendars[0]['calendar_configurations'], true);

                    $_REQUEST['calendarConfigurations'] = $calendarConfigurations;
                }

                if (isset($_REQUEST['export'])) {
                    //when export we only store calendars for one time use
                    $qb = DBManagerFactory::getConnection()->createQueryBuilder();
                    $qb->delete('calendar_ical_configs')
                        ->where($qb->expr()->eq('id', "'$this->calendars_uid'"));

                    $qb->execute();
                }

                $validRequest = true;
            }
        }

        if ($validRequest) {
            $this->http_status('200 OK');
            header('Content-Type: text/calendar; charset="' . $this->cal_charset . '"');
            header('Content-Disposition: inline; filename="Calendar export.ics"');
            $result = mb_convert_encoding(html_entity_decode(
                $this->vcal_focus->getVcalIcal(
                    $this->user_focus,
                    null
                ),
                ENT_QUOTES,
                $this->cal_charset
            ), $this->cal_encoding);
            ob_end_clean();
            echo $result;
        } else {
            $this->http_status('404 Not Found');
            ob_end_clean();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function check_auth($type, $user, $pass)
    {
        if (!array_key_exists('user_id', $_REQUEST)
            || strip_tags($_REQUEST['user_id']) === ''
            || !array_key_exists('key', $_REQUEST)
            || strip_tags($_REQUEST['key']) === ''
            || !array_key_exists('calendarsUID', $_REQUEST)
            || strip_tags($_REQUEST['calendarsUID']) === ''
        ) {
            return false;
        }

        $user = BeanFactory::retrieveBean('Users', $_REQUEST['user_id']);
        if ($user instanceof SugarBean) {
            $publishKey = $user->getPreference('calendar_publish_key');
            $publishKey = empty($publishKey) ? '' : $publishKey;

            if ($_REQUEST['key'] !== $publishKey) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }
}
