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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

class ListCurrency
{
    public $focus = null;
    public $list = null;
    public $javascript = '<script>';
    public $recordSaved = false;

    /**
     * Look up the currencies in the system and set the list
     *
     * @param bool $activeOnly only return active currencies to the list
     */
    public function lookupCurrencies($activeOnly = false)
    {
        $this->focus = BeanFactory::newBean('Currencies');
        $db = DBManagerFactory::getInstance();
        $where = '';
        if ($activeOnly === true) {
            $where = $this->focus->table_name . '.status = ' . $db->quoted('Active');
        }
        $this->list = $this->focus->get_full_list('name', $where);
        $this->focus->retrieve('-99');
        if (is_array($this->list)) {
            $this->list = array_merge([$this->focus], $this->list);
        } else {
            $this->list = [$this->focus];
        }
    }

    /**
     * handle creating or updating a currency record
     *
     */
    public function handleAdd()
    {
        global $current_user;

        if ($current_user->is_admin) {
            if (isset($_POST['edit']) && $_POST['edit'] == 'true' && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['conversion_rate']) && !empty($_POST['conversion_rate']) && isset($_POST['symbol']) && !empty($_POST['symbol'])) {
                $currency = BeanFactory::newBean('Currencies');
                $isUpdate = false;
                if (isset($_POST['record']) && !empty($_POST['record'])) {
                    $isUpdate = true;
                    $currency->retrieve($_POST['record']);
                }
                $currency->name = $_POST['name'];
                $currency->status = $_POST['status'];
                $currency->symbol = $_POST['symbol'];
                $currency->iso4217 = $_POST['iso4217'];
                $previousConversionRate = $currency->conversion_rate;
                $currency->conversion_rate = (string)unformat_number($_POST['conversion_rate']);
                $currency->save();
                $this->focus = $currency;
                // Used to tell calling code that a change was made
                $this->recordSaved = true;

                //Check if the conversion rates changed and, if so, update the rates with a scheduler job
                if ($isUpdate && $previousConversionRate != $currency->conversion_rate) {
                    global $timedate;
                    // use bean factory here
                    $job = BeanFactory::newBean('SchedulersJobs');
                    $job->name = 'SugarJobUpdateCurrencyRates: ' . $timedate->getNow()->asDb();
                    $job->target = 'class::SugarJobUpdateCurrencyRates';
                    $job->data = $currency->id;
                    $job->retry_count = 0;
                    $job->assigned_user_id = $current_user->id;
                    $jobQueue = new SugarJobQueue();
                    $jobQueue->submitJob($job);
                }
            }
        }
    }

    public function handleUpdate()
    {
        global $current_user;
        if ($current_user->is_admin) {
            if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['rate']) && !empty($_POST['rate']) && isset($_POST['symbol']) && !empty($_POST['symbol'])) {
                $ids = $_POST['id'];
                $names = $_POST['name'];
                $symbols = $_POST['symbol'];
                $rates = $_POST['rate'];
                $isos = $_POST['iso'];
                $size = sizeof($ids);
                if ($size != sizeof($names) || $size != sizeof($isos) || $size != sizeof($symbols) || $size != sizeof($rates)) {
                    return;
                }

                $temp = BeanFactory::newBean('Currencies');
                for ($i = 0; $i < $size; $i++) {
                    $temp->id = $ids[$i];
                    $temp->name = $names[$i];
                    $temp->symbol = $symbols[$i];
                    $temp->iso4217 = $isos[$i];
                    $temp->conversion_rate = $rates[$i];
                    $temp->save();
                }
            }
        }
    }

    public function getJavascript()
    {
        // wp: DO NOT add formatting and unformatting numbers in here, add them prior to calling these to avoid double calling
        // of unformat number
        return $this->javascript . <<<EOQ
					function get_rate(id){
						return ConversionRates[id];
					}
					function ConvertToDollar(amount, rate){
						return amount / rate;
					}
					function ConvertFromDollar(amount, rate){
						return amount * rate;
					}
					function ConvertRate(id,fields){
							for(var i = 0; i < fields.length; i++){
								fields[i].value = toDecimal(ConvertFromDollar(toDecimal(ConvertToDollar(toDecimal(fields[i].value), lastRate)), ConversionRates[id]));
							}
							lastRate = ConversionRates[id];
						}
					function ConvertRateSingle(id,field){
						var temp = field.innerHTML.substring(1, field.innerHTML.length);
						unformattedNumber = unformatNumber(temp, num_grp_sep, dec_sep);

						field.innerHTML = CurrencySymbols[id] + formatNumber(toDecimal(ConvertFromDollar(ConvertToDollar(unformattedNumber, lastRate), ConversionRates[id])), num_grp_sep, dec_sep, 2, 2);
						lastRate = ConversionRates[id];
					}
					function CurrencyConvertAll(form){
                        try {
                        var id = form.currency_id.options[form.currency_id.selectedIndex].value;
						var fields = new Array();

						for(i in currencyFields){
							var field = currencyFields[i];
							if(typeof(form[field]) != 'undefined' && form[field].value.length > 0){
								form[field].value = unformatNumber(form[field].value, num_grp_sep, dec_sep);
								fields.push(form[field]);
							}

						}

							ConvertRate(id, fields);
						for(i in fields){
							fields[i].value = formatNumber(fields[i].value, num_grp_sep, dec_sep);

						}

						} catch (err) {
                            // Do nothing, if we can't find the currency_id field we will just not attempt to convert currencies
                            // This typically only happens in lead conversion and quick creates, where the currency_id field may be named somethnig else or hidden deep inside a sub-form.
                        }

					}
				</script>
EOQ;
    }


    public function getSelectOptions($id = '', $base_rate = null)
    {
        global $current_user;
        $this->javascript .= "var ConversionRates = new Array(); \n";
        $this->javascript .= "var CurrencySymbols = new Array(); \n";
        $options = '';
        $this->lookupCurrencies();
        $setLastRate = false;
        if (isset($this->list) && !empty($this->list)) {
            foreach ($this->list as $data) {
                if ($data->status == 'Active') {
                    $rate = $data->conversion_rate;
                    if ($id == $data->id) {
                        $options .= '<option value="' . $data->id . '" selected>';
                        $setLastRate = true;

                        // if a rate was passed in, this means the record has a locked rate and shouldn't be changed,
                        /// when the currency is the same.
                        if (!is_null($base_rate)) {
                            $rate = $base_rate;
                        }

                        $this->javascript .= 'var lastRate = "' . $rate . '";';
                    } else {
                        $options .= '<option value="' . $data->id . '">';
                    }
                    $options .= $data->name . ' : ' . $data->symbol;
                    $this->javascript .= " ConversionRates['" . $data->id . "'] = '" . $rate . "';\n";
                    $this->javascript .= " CurrencySymbols['" . $data->id . "'] = '" . $data->symbol . "';\n";
                }
            }
            if (!$setLastRate) {
                $this->javascript .= 'var lastRate = "1";';
            }
        }
        return $options;
    }

    public function setCurrencyFields($fields)
    {
        $json = getJSONobj();
        $this->javascript .= 'var currencyFields = ' . $json->encode($fields) . ";\n";
    }
}
