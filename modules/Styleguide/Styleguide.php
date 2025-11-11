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


class Styleguide extends Person
{
    public $table_name = 'styleguide';
    public $module_name = 'Styleguide';
    public $module_dir = 'Styleguide';
    public $object_name = 'Styleguide';
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $activities;
    public $team_id;
    public $team_set_id;
    public $team_count;
    public $team_name;
    public $team_link;
    public $team_count_link;
    public $teams;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $salutation;
    public $first_name;
    public $last_name;
    public $full_name;
    public $title;
    public $facebook;
    public $twitter;
    public $googleplus;
    public $department;
    public $do_not_call;
    public $phone_home;
    public $email;
    public $phone_mobile;
    public $phone_work;
    public $phone_other;
    public $phone_fax;
    public $email1;
    public $email2;
    public $invalid_email;
    public $email_opt_out;
    public $primary_address_street;
    public $primary_address_street_2;
    public $primary_address_street_3;
    public $primary_address_city;
    public $primary_address_state;
    public $primary_address_postalcode;
    public $primary_address_country;
    public $alt_address_street;
    public $alt_address_street_2;
    public $alt_address_street_3;
    public $alt_address_city;
    public $alt_address_state;
    public $alt_address_postalcode;
    public $alt_address_country;
    public $assistant;
    public $assistant_phone;
    public $email_addresses_primary;
    public $email_addresses;
    public $picture;
    public $date_start;
    public $birthdate;
    public $radio_button_group;

    public $list_price;
    public $currency_id;

    public function __construct()
    {
        parent::__construct();
        $this->addVisibilityStrategy('OwnerVisibility');
    }

    /**
     * This overrides the default retrieve function setting the default to encode to false
     */
    public function retrieve($id = '-1', $encode = false, $deleted = true)
    {
        return parent::retrieve($id, false, $deleted);
    }

    /**
     * This overrides the default save function setting assigned_user_id
     * @see SugarBean::save()
     */
    public function save($check_notify = false)
    {
        $this->assigned_user_id = $GLOBALS['current_user']->id;
        return parent::save($check_notify);
    }

    /**
     * function to handle removeFile method from FileApi.php.
     * Actual function that removes file calls using js ('save' method with blank filename)
     */
    public function deleteAttachment()
    {
        return true;
    }
}
