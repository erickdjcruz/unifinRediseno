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

use Doctrine\DBAL\Exception as DBALException;

/**
 * Localization manager
 * @api
 */
class Localization
{
    private $deprecatedEncodings = [
        'BASE64',
        'HTML-ENTITIES',
        'Quoted-Printable',
        'UUENCODE',
    ];
    public $availableCharsets = [
        'BIG-5',        //Taiwan and Hong Kong
        /*'CP866'			  // ms-dos Cyrillic */
        /*'CP949'			  //Microsoft Korean */
        'CP1251',       //MS Cyrillic
        'CP1252',       //MS Western European & US
        'EUC-CN',       //Simplified Chinese GB2312
        'EUC-JP',       //Unix Japanese
        'EUC-KR',       //Korean
        'EUC-TW',       //Taiwanese
        'ISO-2022-JP',  //Japanese
        'ISO-2022-KR',  //Korean
        'ISO-8859-1',   //Western European and US
        'ISO-8859-2',   //Central and Eastern European
        'ISO-8859-3',   //Latin 3
        'ISO-8859-4',   //Latin 4
        'ISO-8859-5',   //Cyrillic
        'ISO-8859-6',   //Arabic
        'ISO-8859-7',   //Greek
        'ISO-8859-8',   //Hebrew
        'ISO-8859-9',   //Latin 5
        'ISO-8859-10',  //Latin 6
        'ISO-8859-13',  //Latin 7
        'ISO-8859-14',  //Latin 8
        'ISO-8859-15',  //Latin 9
        'KOI8-R',       //Cyrillic Russian
        'KOI8-U',       //Cyrillic Ukranian
        'SJIS',         //MS Japanese
        'UTF-8',        //UTF-8
    ];
    public $localeNameFormat;
    public $localeNameFormatDefault;
    public $default_export_charset = 'UTF-8';
    public $default_email_charset = 'UTF-8';

    /**
     * Mapping of currency IDs to their properties
     *
     * @var mixed[][]
     */
    private $currencies = [];

    public $invalidNameFormatUpgradeFilename = 'upgradeInvalidLocaleNameFormat.php';
    /* Charset mappings for iconv */
    public $iconvCharsetMap = [
        'KS_C_5601-1987' => 'CP949',
        'ISO-8859-8-I' => 'ISO-8859-8',
    ];

    /**
     * Cache of parsed localized name formats.
     *
     * @var array
     */
    protected $parsedFormats = [];

    /**
     * Reference to the logger for writing log messages
     *
     * @var LoggerManager
     */
    protected $logger;

    /**
     * sole constructor
     */
    public function __construct()
    {
        global $sugar_config;
        $this->localeNameFormatDefault = empty($sugar_config['locale_name_format_default']) ? 's f l' : $sugar_config['default_name_format'];
        $this->logger = LoggerManager::getLogger();
    }

    /**
     * Method to get Localization object
     *
     * @return Localization
     */
    public static function getObject()
    {
        $class = self::class;
        if (SugarAutoLoader::load('custom/include/Localization/Localization.php')) {
            $class = SugarAutoLoader::customClass($class);
        }

        return new $class();
    }

    /**
     * returns an array of Sugar Config defaults that are determined by locale settings
     * @return array
     */
    public function getLocaleConfigDefaults()
    {
        $coreDefaults = [
            'currency' => '',
            'datef' => 'm/d/Y',
            'timef' => 'H:i',
            'default_currency_significant_digits' => 2,
            'default_currency_symbol' => '$',
            'default_export_charset' => $this->default_export_charset,
            'default_locale_name_format' => 's f l',
            'name_formats' => ['s f l' => 's f l', 'f l' => 'f l', 's l' => 's l', 'l, s f' => 'l, s f',
                'l, f' => 'l, f', 's l, f' => 's l, f', 'l s f' => 'l s f', 'l f s' => 'l f s'],
            'default_number_grouping_seperator' => ',',
            'default_decimal_seperator' => '.',
            'export_delimiter' => ',',
            'default_email_charset' => $this->default_email_charset,
        ];

        return $coreDefaults;
    }

    /**
     * abstraction of precedence
     * @param string prefName Name of preference to retrieve based on overrides
     * @param object user User in focus, default null (current_user)
     * @return string pref Most significant preference
     */
    public function getPrecedentPreference($prefName, $user = null, $sugarConfigPrefName = '')
    {
        $emailSettings = [];
        global $current_user;
        global $sugar_config;

        $userPref = '';
        $coreDefaults = $this->getLocaleConfigDefaults();
        $pref = $coreDefaults[$prefName] ?? ''; // defaults, even before config.php

        if ($user != null) {
            $userPref = $user->getPreference($prefName);
        } elseif (!empty($current_user)) {
            $userPref = $current_user->getPreference($prefName);
        }
        // Bug 39171 - If we are asking for default_email_charset, check in emailSettings['defaultOutboundCharset'] as well
        if ($prefName == 'default_email_charset') {
            if ($user != null) {
                $emailSettings = $user->getPreference('emailSettings', 'Emails');
            } elseif (!empty($current_user)) {
                $emailSettings = $current_user->getPreference('emailSettings', 'Emails');
            }
            if (isset($emailSettings['defaultOutboundCharset'])) {
                $userPref = $emailSettings['defaultOutboundCharset'];
            }
        }

        // set fallback defaults defined in this class
        if (isset($this->$prefName)) {
            $pref = $this->$prefName;
        }
        //rrs: 33086 - give the ability to pass in the preference name as stored in $sugar_config.
        if (!empty($sugarConfigPrefName)) {
            $prefName = $sugarConfigPrefName;
        }

        // if we don't have a user pref for the num_grp_sep, just return NULL as the key is not
        // in the main global config and if we let it continue, it will just return '' (empty string)
        if ($prefName == 'num_grp_sep' && empty($sugarConfigPrefName) && is_null($userPref)) {
            return null;
        }

        // cn: 9549 empty() call on a value of 0 (0 significant digits) resulted in a false-positive.  changing to "isset()"
        $pref = (!isset($sugar_config[$prefName]) || (empty($sugar_config[$prefName]) && $sugar_config[$prefName] !== '0')) ? $pref : $sugar_config[$prefName];
        $pref = (empty($userPref) && $userPref !== '0') ? $pref : $userPref;
        return $pref;
    }

    ///////////////////////////////////////////////////////////////////////////
    ////	CURRENCY HANDLING
    /**
     * Loads the list of currently active currencies
     *
     * @return mixed[][]
     * @throws DBALException
     */
    private function loadCurrencies(): array
    {
        global $db;
        global $sugar_config;

        if (empty($db)) {
            return [];
        }

        $currencies = [
            '-99' => [
                'name' => $sugar_config['default_currency_name'],
                'symbol' => $sugar_config['default_currency_symbol'],
                'conversion_rate' => 1,
            ],
        ];

        $query = "SELECT id, name, symbol, conversion_rate FROM currencies WHERE status = 'Active' AND deleted = 0";
        $stmt = $db->getConnection()->executeQuery($query);

        foreach ($stmt->iterateAssociative() as $row) {
            $currencies[$row['id']] = $row;
        }

        return $currencies;
    }

    /**
     * Returns the list of currently active currencies
     *
     * @return mixed[][]
     * @throws DBALException
     */
    public function getCurrencies(): array
    {
        if (safeCount($this->currencies) < 1) {
            $this->currencies = $this->loadCurrencies();
        }

        return $this->currencies;
    }

    /**
     * retrieves default OOTB currencies for sugar_config and installer.
     * @return array ret Array of default currencies keyed by ISO4217 code
     */
    public function getDefaultCurrencies()
    {
        $ret = [
            'AUD' => ['name' => 'Australian Dollars',
                'iso4217' => 'AUD',
                'symbol' => '$'],
            'BRL' => ['name' => 'Brazilian Reais',
                'iso4217' => 'BRL',
                'symbol' => 'R$'],
            'GBP' => ['name' => 'British Pounds',
                'iso4217' => 'GBP',
                'symbol' => '£'],
            'CAD' => ['name' => 'Canadian Dollars',
                'iso4217' => 'CAD',
                'symbol' => '$'],
            'CNY' => ['name' => 'Chinese Yuan',
                'iso4217' => 'CNY',
                'symbol' => '￥'],
            'EUR' => ['name' => 'Euro',
                'iso4217' => 'EUR',
                'symbol' => '€'],
            'HKD' => ['name' => 'Hong Kong Dollars',
                'iso4217' => 'HKD',
                'symbol' => '$'],
            'INR' => ['name' => 'Indian Rupees',
                'iso4217' => 'INR',
                'symbol' => '₨'],
            'KRW' => ['name' => 'Korean Won',
                'iso4217' => 'KRW',
                'symbol' => '₩'],
            'YEN' => ['name' => 'Japanese Yen',
                'iso4217' => 'JPY',
                'symbol' => '¥'],
            'MXM' => ['name' => 'Mexican Pesos',
                'iso4217' => 'MXM',
                'symbol' => '$'],
            'SGD' => ['name' => 'Singaporean Dollars',
                'iso4217' => 'SGD',
                'symbol' => '$'],
            'CHF' => ['name' => 'Swiss Franc',
                'iso4217' => 'CHF',
                'symbol' => 'SFr.'],
            'THB' => ['name' => 'Thai Baht',
                'iso4217' => 'THB',
                'symbol' => '฿'],
            'USD' => ['name' => 'US Dollars',
                'iso4217' => 'USD',
                'symbol' => '$'],
        ];

        return $ret;
    }
    ////	END CURRENCY HANDLING
    ///////////////////////////////////////////////////////////////////////////


    ///////////////////////////////////////////////////////////////////////////
    ////	CHARSET TRANSLATION
    /**
     * returns a mod|app_strings array in the target charset
     * @param array strings $mod_string, et.al.
     * @param string charset Target charset
     * @return array Translated string pack
     */
    public function translateStringPack($strings, $charset)
    {
        // handle recursive
        foreach ($strings as $k => $v) {
            if (is_array($v)) {
                $strings[$k] = $this->translateStringPack($v, $charset);
            } else {
                $strings[$k] = $this->translateCharset($v, 'UTF-8', $charset);
            }
        }
        ksort($strings);
        return $strings;
    }

    /**
     * translates the passed variable for email sending (export)
     * @param mixed the var (array or string) to translate
     * @return  mixed the translated variable
     */
    public function translateForEmail($var)
    {
        if (is_array($var)) {
            foreach ($var as $k => $v) {
                $var[$k] = $this->translateForEmail($v);
            }
            return $var;
        } elseif (!empty($var)) {
            return $this->translateCharset($var, 'UTF-8', $this->getOutboundEmailCharset());
        }
    }

    /**
     * prepares a bean for export by translating any text fields into the export
     * character set
     * @param bean object A SugarBean
     * @return bean object The bean with translated strings
     */
    public function prepBeanForExport($bean)
    {
        foreach ($bean->field_defs as $k => $field) {
            if (is_string($bean->$k)) {
                // $bean->$k = $this->translateCharset($bean->$k, 'UTF-8', $this->getExportCharset());
            } else {
                $bean->$k = '';
            }
        }

        return $bean;
    }

    /**
     * translates a character set from one encoding to another encoding
     * @param string string the string to be translated
     * @param string fromCharset the charset the string is currently in
     * @param string toCharset the charset to translate into (defaults to UTF-8)
     * @param bool   forceIconv force using the iconv library instead of mb_string
     * @param bool   addBOM prepends BOM to the encoded string to be translated
     * @return string the translated string
     */
    public function translateCharset($string, $fromCharset, $toCharset = 'UTF-8', $forceIconv = false, $addBOM = false)
    {
        $GLOBALS['log']->debug("Localization: translating [{$string}] from {$fromCharset} into {$toCharset}");

        // Bug #35413: Must fallback to using iconv if $fromCharset is not
        // compatible with mb_convert_encoding
        $canUseMbConvert = function_exists('mb_convert_encoding') &&
            $this->validateMbEncoding($fromCharset) &&
            !$forceIconv;
        $canUseIconv = function_exists('iconv');

        if ($canUseMbConvert) {
            global $sugar_config;
            if (!empty($sugar_config['export_excel_compatible']) && $addBOM === true) {
                return chr(255) . chr(254) . mb_convert_encoding($string, 'UTF-16LE', $fromCharset);
            } else {
                return mb_convert_encoding($string, $toCharset, $fromCharset);
            }
        } elseif ($canUseIconv) {
            $newFromCharset = $this->prepareNewCharset($fromCharset);
            $newToCharset = $this->prepareNewCharset($toCharset);
            return iconv($newFromCharset, $newToCharset, $string);
        } else {
            return $string;
        }
    }

    /**
     * Prepares new charset before iconv
     *
     * @param string $charset the character set
     * @return string
     */
    public function prepareNewCharset(string $charset): string
    {
        // Checks charset mapping from original case, then uppercase
        $newCharset = $this->iconvCharsetMap[$charset] ??
            ($this->iconvCharsetMap[sugarStrToUpper($charset)] ?? $charset);

        if ($newCharset !== $charset) {
            $this->logger->debug("Localization: iconv using charset {$newCharset} instead of {$charset}");
        }
        return $newCharset;
    }

    /**
     * Validates whether the given character set is compatible for use with
     * mb_convert_encoding
     *
     * @param string $charset the character set to validate
     * @return bool true if the character set is compatible with mb_convert_encoding
     */
    public function validateMbEncoding(string $charset)
    {
        // Convert the character set to uppercase
        $charset = sugarStrToUpper($charset);

        // Get the uppercase list of all character sets supported by
        // mb_convert_encoding
        $validCharsets = $this->getValidMbEncodings();

        // Check the list of supported character sets to see if the given
        // character set is included among them
        $isValid = array_key_exists($charset, $validCharsets);
        if (!$isValid) {
            $this->logger->debug("Localization: charset {$charset} not supported by mb_convert_encoding");
        }

        return $isValid;
    }

    /**
     * Returns an array where the keys are encodings supported by mb_convert_encoding,
     * including any aliases that are not directly returned by mb_list_encodings.
     * Keys are returned in all-uppercase format
     */
    private function getValidMbEncodings()
    {
        $encodings = sugar_cache_retrieve('valid_mb_encodings');

        // If the cache didn't contain the encodings, build the list and cache it
        if (empty($encodings)) {
            // Get the list of supported encodings, including all of their
            // aliases. Store the encodings as all-uppercase keys of the array
            // for faster lookup
            $encodings = [];
            foreach (mb_list_encodings() as $encoding) {
                if (in_array($encoding, $this->deprecatedEncodings)) {
                    continue;
                }
                $encodings[sugarStrToUpper($encoding)] = true;
                foreach (mb_encoding_aliases($encoding) as $alias) {
                    $encodings[sugarStrToUpper($alias)] = true;
                }
            }

            // Cache the list for future use
            sugar_cache_put('valid_mb_encodings', $encodings);
        }

        return $encodings;
    }

    /**
     * translates a character set from one to another, and the into MIME-header friendly format
     */
    public function translateCharsetMIME($string, $fromCharset, $toCharset = 'UTF-8', $encoding = 'Q')
    {
        $previousEncoding = mb_internal_encoding();
        mb_internal_encoding($toCharset);
        $result = mb_encode_mimeheader($string, $toCharset, $encoding);
        if (is_string($previousEncoding)) {
            mb_internal_encoding($previousEncoding);
        }
        return $result;
    }

    public function normalizeCharset($charset)
    {
        $charset = strtolower(preg_replace("/[\-\_]*/", '', $charset));
        return $charset;
    }

    /**
     * returns an array of charsets with keys for available translations; appropriate for get_select_options_with_id()
     */
    public function getCharsetSelect()
    {
        //jc:12293 - the "labels" or "human-readable" representations of the various charsets
        //should be translatable
        $translated = [];
        foreach ($this->availableCharsets as $key) {
            //$translated[$key] = translate($value);
            $translated[$key] = translate($key);
        }

        return $translated;
        //end:12293
    }

    /**
     * returns the charset preferred in descending order: User, Sugar Config, DEFAULT
     * @param string charset to override ALL, pass a valid charset here
     * @return string charset the chosen character set
     */
    public function getExportCharset($charset = '', $user = null)
    {
        $charset = $this->getPrecedentPreference('default_export_charset', $user);
        return $charset;
    }

    /**
     * returns the charset preferred in descending order: User, Sugar Config, DEFAULT
     * @return string charset the chosen character set
     */
    public function getOutboundEmailCharset($user = null)
    {
        $charset = $this->getPrecedentPreference('default_email_charset', $user);
        return $charset;
    }
    ////	END CHARSET TRANSLATION
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    ////	NUMBER DISPLAY FORMATTING CODE
    public function getDecimalSeparator($user = null)
    {
        // Bug50887 this is purposefully misspelled as ..._seperator to match the way it's defined throughout the app.
        $dec = $this->getPrecedentPreference('dec_sep', $user);
        $dec = $dec ?: $this->getPrecedentPreference('default_decimal_seperator', $user);
        return $dec;
    }

    public function getNumberGroupingSeparator($user = null)
    {
        $sep = $this->getPrecedentPreference('num_grp_sep', $user);
        $sep = !is_null($sep) ? $sep : $this->getPrecedentPreference('default_number_grouping_seperator', $user);
        return $sep;
    }

    public function getPrecision($user = null)
    {
        $precision = $this->getPrecedentPreference('default_currency_significant_digits', $user);
        return $precision;
    }

    public function getCurrencySymbol($user = null)
    {
        $currencyId = $this->getPrecedentPreference('currency', $user);
        $currencyId = $currencyId ?: '-99';
        $currency = SugarCurrency::getCurrencyByID($currencyId);
        return $currency->symbol;
    }

    /**
     * returns a number formatted by user preference or system default
     * @param string number Number to be formatted and returned
     * @param string currencySymbol Currency symbol if override is necessary
     * @param bool is_currency Flag to also return the currency symbol
     * @return string Formatted number
     */
    public function getLocaleFormattedNumber($number, $currencySymbol = '', $is_currency = true, $user = null)
    {
        $fnum = $number;
        $majorDigits = '';
        $minorDigits = '';
        $dec = $this->getDecimalSeparator($user);
        $thou = $this->getNumberGroupingSeparator($user);
        $precision = $this->getPrecision($user);
        $symbol = empty($currencySymbol) ? $this->getCurrencySymbol($user) : $currencySymbol;

        $exNum = explode($dec, $number);
        // handle grouping
        if (is_array($exNum) && safeCount($exNum) > 0) {
            if (strlen($exNum[0]) > 3) {
                $offset = strlen($exNum[0]) % 3;
                if ($offset > 0) {
                    for ($i = 0; $i < $offset; $i++) {
                        $majorDigits .= $exNum[0][$i];
                    }
                }

                $tic = 0;
                for ($i = $offset; $i < strlen($exNum[0]); $i++) {
                    if ($tic % 3 == 0 && $i != 0) {
                        $majorDigits .= $thou; // add separator
                    }

                    $majorDigits .= $exNum[0][$i];
                    $tic++;
                }
            } else {
                $majorDigits = $exNum[0]; // no formatting needed
            }
            $fnum = $majorDigits;
        }

        if ($is_currency) {
            $fnum = $symbol . $fnum;
        }
        return $fnum;
    }

    /**
     * returns Javascript to format numbers and currency for ***DISPLAY***
     */
    public function getNumberJs()
    {
        $out = <<<eoq

			var exampleDigits = '123456789.000000';

			// round parameter can be negative for decimal, precision has to be postive
			function formatNumber(n, sep, dec, precision) {
				var majorDigits;
				var minorDigits;
				var formattedMajor = '';
				var formattedMinor = '';

				var nArray = n.split('.');
				majorDigits = nArray[0];
				if(nArray.length < 2) {
					minorDigits = 0;
				} else {
					minorDigits = nArray[1];
				}

				// handle grouping
				if(sep.length > 0) {
					var strlength = majorDigits.length;

					if(strlength > 3) {
						var offset = strlength % 3; // find how many to lead off by

						for(j=0; j<offset; j++) {
							formattedMajor += majorDigits[j];
						}

						tic=0;
						for(i=offset; i<strlength; i++) {
							if(tic % 3 == 0 && i != 0)
								formattedMajor += sep;

							formattedMajor += majorDigits.substr(i,1);
							tic++;
						}
					}
				} else {
					formattedMajor = majorDigits; // no grouping marker
				}

				// handle decimal precision
				if(precision > 0) {
					for(i=0; i<precision; i++) {
						if(minorDigits[i] != undefined)
							formattedMinor += minorDigits[i];
						else
							formattedMinor += '0';
					}
				} else {
					// we're just returning the major digits, no decimal marker
					dec = ''; // just in case
				}

				return formattedMajor + dec + formattedMinor;
			}

			function setSigDigits() {
				var sym = document.getElementById('symbol').value;
				var thou = document.getElementById('default_number_grouping_seperator').value;
				var dec = document.getElementById('default_decimal_seperator').value;
				var precision = document.getElementById('sigDigits').value;
				//umber(n, num_grp_sep, dec_sep, round, precision)
				var newNumber = sym + formatNumber(exampleDigits, thou, dec, precision, precision);
				document.getElementById('sigDigitsExample').value = newNumber;
			}
eoq;
        return $out;
    }

    ////	END NUMBER DISPLAY FORMATTING CODE
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    ////	NAME DISPLAY FORMATTING CODE
    /**
     * get's the Name format macro string, preferring $current_user
     * @return string format Name Format macro for locale
     */
    public function getLocaleFormatMacro($user = null)
    {
        $returnFormat = $this->getPrecedentPreference('default_locale_name_format', $user);
        return $returnFormat;
    }

    /**
     * Returns parsed name according to $current_user's locale settings.
     *
     * @param string $fullName
     * @param string $format If a particular format is desired, then pass this optional parameter as a simple string.
     * @return array
     */
    public function getLocaleUnFormattedName($fullName, $format = '')
    {
        global $current_user;
        global $app_list_strings;

        $result = [];

        if (empty($format)) {
            $this->localeNameFormat = $this->getLocaleFormatMacro($current_user);
        } else {
            $this->localeNameFormat = $format;
        }

        $formatCommaParts = explode(', ', $this->localeNameFormat);
        $countFormatCommaParts = safeCount($formatCommaParts);

        $nameCommaParts = explode(', ', trim($fullName), $countFormatCommaParts);
        $nameCommaParts = array_pad($nameCommaParts, $countFormatCommaParts, '');

        foreach ($formatCommaParts as $idx => $part) {
            $formatSpaceParts = explode(' ', $part);
            $nameSpaceParts = explode(' ', $nameCommaParts[$idx]);

            $salutationFormatKey = array_search('s', $formatSpaceParts);
            $salutationNameKey = null;

            if ($salutationFormatKey !== false) {
                foreach ($nameSpaceParts as $_idx => $_part) {
                    if (isset($app_list_strings['salutation_dom'])
                        && is_array($app_list_strings['salutation_dom'])
                        && ($result['s'] = array_search($_part, $app_list_strings['salutation_dom'])) !== false
                    ) {
                        $salutationNameKey = $_idx;
                        break;
                    }
                }

                if (!empty($result['s'])) {
                    $this->helperCombineFormatToName(
                        array_slice($formatSpaceParts, 0, $salutationFormatKey),
                        array_slice($nameSpaceParts, 0, $salutationNameKey),
                        $result
                    );

                    $this->helperCombineFormatToName(
                        array_slice($formatSpaceParts, $salutationFormatKey + 1),
                        array_slice($nameSpaceParts, $salutationNameKey + 1),
                        $result
                    );
                    continue;
                } else {
                    unset($formatSpaceParts[$salutationFormatKey]);
                }
            }

            $this->helperCombineFormatToName($formatSpaceParts, $nameSpaceParts, $result);
        }

        return $result;
    }

    /**
     * Helper method combine name by format.
     *
     * @param array $formatSpaceParts
     * @param array $nameSpaceParts
     * @param array $result
     */
    protected function helperCombineFormatToName($formatSpaceParts, $nameSpaceParts, &$result)
    {
        $formatSpaceParts = array_values($formatSpaceParts);
        $nameSpaceParts = array_values($nameSpaceParts);

        $countFormatSpaceParts = safeCount($formatSpaceParts);
        $countNameSpaceParts = safeCount($nameSpaceParts);

        if ($countFormatSpaceParts == $countNameSpaceParts) {
            $result = array_merge($result, array_combine($formatSpaceParts, $nameSpaceParts));
        } else {
            foreach ($formatSpaceParts as $idx => $item) {
                if ($idx == ($countFormatSpaceParts - 1)) {
                    $result[$item] = implode(' ', $nameSpaceParts);
                } else {
                    $result[$item] = $nameSpaceParts[$idx];
                    unset($nameSpaceParts[$idx]);
                }
            }
        }
    }

    /**
     * returns formatted name according to $current_user's locale settings
     *
     * @param string firstName
     * @param string lastName
     * @param string salutation
     * @param string title
     * @param string format If a particular format is desired, then pass this optional parameter as a simple string.
     * sfl is "Salutation FirstName LastName", "l, f s" is "LastName[comma][space]FirstName[space]Salutation"
     * @param object user object
     * @param bool returnEmptyStringIfEmpty true if we should return back an empty string rather than a single space
     * when the formatted name would be blank
     * @return string formattedName
     * @deprecated
     */
    public function getLocaleFormattedName($firstName, $lastName, $salutationKey = '', $title = '', $format = '', $user = null, $returnEmptyStringIfEmpty = false)
    {
        global $current_user;
        global $app_list_strings;

        if ($user == null) {
            $user = $current_user;
        }

        $salutation = $salutationKey;
        if (!empty($salutationKey) && !empty($app_list_strings['salutation_dom'][$salutationKey])) {
            $salutation = (!empty($app_list_strings['salutation_dom'][$salutationKey]) ? $app_list_strings['salutation_dom'][$salutationKey] : $salutationKey);
        }

        //check to see if passed in variables are set, if so, then populate array with value,
        //if not, then populate array with blank ''
        $names = [];
        $names['f'] = (empty($firstName) && $firstName != 0) ? '' : $firstName;
        $names['l'] = (empty($lastName) && $lastName != 0) ? '' : $lastName;
        $names['s'] = (empty($salutation) && $salutation != 0) ? '' : $salutation;
        $names['t'] = (empty($title) && $title != 0) ? '' : $title;

        //Bug: 39936 - if all of the inputs are empty, then don't try to format the name.
        $allEmpty = true;
        foreach ($names as $key => $val) {
            if (!empty($val)) {
                $allEmpty = false;
                break;
            }
        }
        if ($allEmpty) {
            return $returnEmptyStringIfEmpty ? '' : ' ';
        }
        //end Bug: 39936

        if (empty($format)) {
            $this->localeNameFormat = $this->getLocaleFormatMacro($user);
        } else {
            $this->localeNameFormat = $format;
        }

        // parse localeNameFormat
        $formattedName = '';
        for ($i = 0; $i < strlen($this->localeNameFormat); $i++) {
            $formattedName .= $names[$this->localeNameFormat[$i]] ?? $this->localeNameFormat[$i];
        }

        $formattedName = trim($formattedName);
        if (strlen($formattedName) == 0) {
            return $returnEmptyStringIfEmpty ? '' : ' ';
        }

        if (strpos($formattedName, ',', strlen($formattedName) - 1)) { // remove trailing commas
            $formattedName = substr($formattedName, 0, strlen($formattedName) - 1);
        }
        return trim($formattedName);
    }

    /**
     * Returns formatted name according to $current_user's locale settings
     *
     * @param SugarBean|string $beanOrModuleName SugarBean object or module name
     * @param array $data The data that should be used to fetch values from instead of $bean.
     *
     * @return string
     */
    public function formatName($beanOrModuleName, array $data = null)
    {
        global $app_list_strings;

        $bean = $this->getBean($beanOrModuleName);
        if (!$bean) {
            return '';
        }

        $format = $this->getLocaleFormatMacro();
        $tokens = $this->parseLocaleFormatMacro($format);

        $result = [];
        foreach ($tokens as $token) {
            if ($token['is_field']) {
                $alias = $token['field_alias'];
                $value = '';
                if (isset($bean->name_format_map[$alias])) {
                    $field = $bean->name_format_map[$alias];
                    if ($data === null) {
                        if (isset($bean->$field)) {
                            $value = $bean->$field;
                        }
                    } else {
                        if (isset($data[$field])) {
                            $value = $data[$field];
                        }
                    }

                    if (isset($bean->field_defs[$field])) {
                        $field_defs = $bean->field_defs[$field];
                        if (isset($field_defs['type'])) {
                            switch ($field_defs['type']) {
                                case 'enum':
                                case 'multienum':
                                    if (isset($field_defs['options'])) {
                                        $options = $field_defs['options'];
                                        if (isset($app_list_strings[$options][$value])) {
                                            $value = $app_list_strings[$options][$value];
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }

                $result[] = [
                    'value' => $value,
                    'is_field' => $token['is_field'],
                ];
            } else {
                $result[] = $token;
            }
        }

        $result = $this->trim($result, 'reset', 'array_shift');
        $result = $this->trim($result, 'end', 'array_pop');

        return implode('', array_map(function ($token) {
            return $token['value'];
        }, $result));
    }

    /**
     * Trims delimiters and empty values until non-empty value token is found
     *
     * @param array $tokens Tokens
     * @param callable $next Function to get next token
     * @param callable $remove Function to remove token
     * @return array
     */
    protected function trim(array $tokens, $next, $remove)
    {
        $fieldRemoved = false;
        while ($tokens) {
            $token = $next($tokens);
            if ($token['is_field'] && strcmp($token['value'], '') != 0) {
                break;
            }

            if ($token['is_field'] || $fieldRemoved) {
                $remove($tokens);
                $fieldRemoved |= $token['is_field'];
            } else {
                break;
            }
        }

        return $tokens;
    }

    /**
     * Returns names of SugarBean fields that are used in current formatting macro.
     *
     * @param SugarBean|string $beanOrModuleName SugarBean object or module name
     *
     * @return array
     */
    public function getNameFormatFields($beanOrModuleName)
    {
        $fields = [];
        $bean = $this->getBean($beanOrModuleName);
        if (!$bean) {
            return $fields;
        }

        $format = $this->getLocaleFormatMacro();
        $tokens = $this->parseLocaleFormatMacro($format);

        foreach ($tokens as $token) {
            if ($token['is_field']) {
                $alias = $token['field_alias'];
                if (isset($bean->name_format_map[$alias])) {
                    $fields[] = $bean->name_format_map[$alias];
                }
            }
        }

        return array_unique($fields);
    }

    /**
     * Returns an instance of specified module or the bean itself.
     *
     * @param SugarBean|string $beanOrModuleName SugarBean object or module name
     *
     * @return SugarBean|null
     */
    protected function getBean($beanOrModuleName)
    {
        if (is_string($beanOrModuleName)) {
            global $current_user;

            // don't instantiate User object if the metadata can be read from current user
            if ($beanOrModuleName == 'Users' && $current_user) {
                $bean = $current_user;
            } else {
                $bean = BeanFactory::getDefinition($beanOrModuleName);
            }
        } elseif ($beanOrModuleName instanceof SugarBean) {
            $bean = $beanOrModuleName;
        } else {
            $bean = null;
        }

        return $bean;
    }

    /**
     * Returns array of tokens corresponding to the given formatting macro.
     *
     * @param string $format
     * @return array
     */
    protected function parseLocaleFormatMacro($format)
    {
        if (!isset($this->parsedFormats[$format])) {
            $tokens = [];
            for ($i = 0, $length = strlen($format); $i < $length; $i++) {
                $character = $format[$i];
                $is_field = $character >= 'a' && $character <= 'z';

                $token = [
                    'is_field' => $is_field,
                ];

                if ($is_field) {
                    $token['field_alias'] = $character;
                } else {
                    $token['value'] = $character;
                }

                $tokens[] = $token;
            }
            $this->parsedFormats[$format] = $tokens;
        }

        return $this->parsedFormats[$format];
    }

    /**
     * outputs some simple Javascript to show a preview of Name format in "My Account" and "Admin->Localization"
     * @param string first First Name, use app_strings default if not specified
     * @param string last Last Name, use app_strings default if not specified
     * @param string salutation Saluation, use app_strings default if not specified
     * @return string some Javascript
     */
    public function getNameJs($first = '', $last = '', $salutation = '', $title = '')
    {
        global $app_strings;

        $salutation = !empty($salutation) ? $salutation : $app_strings['LBL_LOCALE_NAME_EXAMPLE_SALUTATION'];
        $first = !empty($first) ? $first : $app_strings['LBL_LOCALE_NAME_EXAMPLE_FIRST'];
        $last = !empty($last) ? $last : $app_strings['LBL_LOCALE_NAME_EXAMPLE_LAST'];
        $title = !empty($title) ? $title : $app_strings['LBL_LOCALE_NAME_EXAMPLE_TITLE'];

        $ret = "
		function setPreview() {
			format = document.getElementById('default_locale_name_format').value;
			field = document.getElementById('nameTarget');

			stuff = new Object();

			stuff['s'] = '{$salutation}';
			stuff['f'] = '{$first}';
			stuff['l'] = '{$last}';
			stuff['t'] = '{$title}';

			var name = '';
			for(i=0; i<format.length; i++) {
                if(stuff[format.substr(i,1)] != undefined) {
                    name += stuff[format.substr(i,1)];
				} else {
                    name += format.substr(i,1);
		}
			}

			//alert(name);
			field.value = name;
		}

        ";

        return $ret;
    }

    /**
     * Checks to see that the characters in $name_format are allowed:  s, f, l, space/tab or punctuation
     * @param $name_format
     * @return bool
     */
    public function isAllowedNameFormat($name_format)
    {
        // will result in a match as soon as a disallowed char is hit in $name_format
        $match = preg_match('/[^sfl[:punct:][:^alnum:]\s]/', $name_format);
        if ($match !== false && $match === 0) {
            return true;
        }
        return false;
    }

    /**
     * Checks to see if there was an invalid Name Format encountered during the upgrade
     * @return bool true if there was an invalid name, false if all went well.
     */
    public function invalidLocaleNameFormatUpgrade()
    {
        return file_exists($this->invalidNameFormatUpgradeFilename);
    }

    /**
     * Creates the file that is created when there is an invalid name format during an upgrade
     */
    public function createInvalidLocaleNameFormatUpgradeNotice()
    {
        $fh = fopen($this->invalidNameFormatUpgradeFilename, 'w');
        fclose($fh);
    }

    /**
     * Removes the file that is created when there is an invalid name format during an upgrade
     */
    public function removeInvalidLocaleNameFormatUpgradeNotice()
    {
        if ($this->invalidLocaleNameFormatUpgrade()) {
            unlink($this->invalidNameFormatUpgradeFilename);
        }
    }


    /**
     * Creates dropdown items that have localized example names while filtering out invalid formats
     *
     * @param array un-prettied dropdown list
     * @return array array of dropdown options
     */
    public function getUsableLocaleNameOptions($options)
    {
        global $app_strings;

        $examples = ['s' => $app_strings['LBL_LOCALE_NAME_EXAMPLE_SALUTATION'],
            'f' => $app_strings['LBL_LOCALE_NAME_EXAMPLE_FIRST'],
            'l' => $app_strings['LBL_LOCALE_NAME_EXAMPLE_LAST']];
        $newOpts = [];
        foreach ($options as $key => $val) {
            if ($this->isAllowedNameFormat($key) && $this->isAllowedNameFormat($val)) {
                $newVal = '';
                $pieces = str_split($val);
                foreach ($pieces as $piece) {
                    if (isset($examples[$piece])) {
                        $newVal .= $examples[$piece];
                    } else {
                        $newVal .= $piece;
                    }
                }
                $newOpts[$key] = $newVal;
            }
        }
        return $newOpts;
    }
    ////	END NAME DISPLAY FORMATTING CODE
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Attempts to detect the charset used in the string
     *
     * @param  $str string
     * @param $strict bool default false (use strict encoding?)
     * @return string
     */
    public function detectCharset($str, $strict = false)
    {
        if (function_exists('mb_convert_encoding')) {
            // There some differences in the behavior for newer version of PHP:
            // the command: mb_detect_encoding(base64_decode('GyRCJWYhPCU2TD4bKEI='ASCII,UTF-8,JIS,EUC-JP,SJIS,ISO-8859-1), '', true);
            // returns JIS for 8.1 - 8.2
            // returns UTF-8 for 8.0
            // Also: mb_detect_encoding('FN:sMüster', 'ASCII,UTF-8,JIS,EUC-JP,SJIS,ISO-8859-1', true);
            // returns JIS for 8.3
            // returns UTF-8 for < 8.3
            // That's why this condition was added
            if (PHP_VERSION_ID < 80300) {
                $encodingList = 'ASCII,JIS,UTF-8,EUC-JP,SJIS,ISO-8859-1';
            } else {
                $encodingList = 'ASCII,UTF-8,JIS,EUC-JP,SJIS,ISO-8859-1';
            }
            return mb_detect_encoding($str, $encodingList, $strict);
        }

        return false;
    }

    /**
     * Gets the authenticated user's language. This is used throughout the app.
     *
     * @return string
     */
    public function getAuthenticatedUserLanguage()
    {
        if (!empty($GLOBALS['current_user']->preferred_language)) {
            return $GLOBALS['current_user']->preferred_language;
        }
        if (!empty($_SESSION['authenticated_user_language'])) {
            return $_SESSION['authenticated_user_language'];
        }
        return $GLOBALS['sugar_config']['default_language'];
    }
} // end class def
