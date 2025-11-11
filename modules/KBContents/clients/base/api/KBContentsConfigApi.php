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


use Sugarcrm\Sugarcrm\Dbal\Connection;

class KBContentsConfigApi extends ConfigModuleApi
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function registerApiRest()
    {
        $api = parent::registerApiRest();

        $api['kbcontentsConfigCreate'] = [
            'reqType' => 'POST',
            'path' => ['KBContents', 'config'],
            'pathVars' => ['module', ''],
            'method' => 'configSave',
            'shortHelp' => 'Creates the config entries for the KBContents module.',
            'longHelp' => 'modules/KBContents/clients/base/api/help/kb_config_put_help.html',
        ];
        $api['kbcontentsConfigUpdate'] = [
            'reqType' => 'PUT',
            'path' => ['KBContents', 'config'],
            'pathVars' => ['module', ''],
            'method' => 'configSave',
            'shortHelp' => 'Updates the config entries for the KBContents module',
            'longHelp' => 'modules/KBContents/clients/base/api/help/kb_config_put_help.html',
        ];

        return $api;
    }

    /**
     * {@inheritdoc}
     * Overridden method to save KBContents module settings.
     *
     * @param ServiceBase $api
     * @param array $params
     * @param string $module
     */
    protected function save(ServiceBase $api, $params, $module)
    {
        /** @var Administration $admin */
        $admin = BeanFactory::newBean('Administration');

        $deletedLanguages = [];
        if (isset($params['deleted_languages'])) {
            $deletedLanguages = $params['deleted_languages'];
            unset($params['deleted_languages']);
        }

        $config = $admin->getConfigForModule($module);

        foreach ($params as $name => $value) {
            $configSaved = $admin->saveSetting($module, $name, $value, $api->platform);

            if ((false !== $configSaved && 'languages' == $name) && (isset($config['languages']))) {
                $initialLanguageList = $this->_getLanguagesAbbreviations($config['languages']);

                foreach ($value as $key => $language) {
                    unset($language['primary']);
                    $languageKey = key($language);

                    if (safeInArray($languageKey, $deletedLanguages)) {
                        // Case when we removed and after add the same language back.
                        unset($deletedLanguages[array_search($languageKey, $deletedLanguages)]);
                        continue;
                    }

                    if (!safeInArray($languageKey, $initialLanguageList)) {
                        if (isset($config['languages'][$key])) {
                            $_tmp = $config['languages'][$key];
                            unset($_tmp['primary']);
                            $configLanguageKey = key($_tmp);
                            if (!safeInArray($configLanguageKey, $deletedLanguages)) {
                                // $configLanguageKey - initial key
                                // $languageKey - updated key
                                $this->updateDocuments(
                                    ['language' => $languageKey],
                                    [$configLanguageKey]
                                );
                            }
                        }
                    }
                }
                // Process documents for deleted languages
                if (!empty($deletedLanguages)) {
                    $this->updateDocuments(
                        ['deleted' => 1],
                        $deletedLanguages
                    );
                }
            }
        }
    }

    /**
     * Update documents.
     *
     * @param array $values Pairs {key=>value} for update.
     * @param array $lang Languages which should be updated.
     */
    protected function updateDocuments($values, $lang)
    {
        $db = DBManagerFactory::getInstance();
        $bean = BeanFactory::newBean('KBContents');

        if (!empty($lang) && !empty($values)) {
            $setParams = [];
            $paramValues = [];
            $types = [];
            foreach ($values as $key => $value) {
                $setParams[] = $key . ' = ?';
                $paramValues[] = $value;
                $types[] = null;
            }
            $paramValues[] = $lang;
            $types[] = Connection::PARAM_STR_ARRAY;
            $sql = 'UPDATE ' . $bean->table_name . ' SET ' . implode(',', $setParams) . ' WHERE language IN (?)';
            $db->getConnection()
                ->executeUpdate($sql, $paramValues, $types);
        }
    }

    /**
     * Return list of abbreviations from languages.
     *
     * @param array $list Language list.
     * @return array Of language abbreviations.
     */
    private function _getLanguagesAbbreviations($list)
    {
        $result = [];
        foreach ($list as $item) {
            unset($item['primary']);
            $key = key($item);
            if (2 == strlen($key) || 1 == strlen($key)) {
                $result[] = $key;
            }
        }
        return $result;
    }
}
