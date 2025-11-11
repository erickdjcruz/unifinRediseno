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

class FormulaBuilderApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'meta' => [
                'reqType' => 'POST',
                'path' => ['formulaBuilder', 'meta'],
                'pathVars' => ['', ''],
                'method' => 'meta',
                'shortHelp' => 'Retrieve metadata needed by the Formula Builder component',
                'longHelp' => 'include/api/help/formulabuilder_meta_post_help.html',
                'ignoreMetaHash' => true,
                'minVersion' => '11.13',
            ],
        ];
    }

    /**
     * Returns needed metadata for the formula builder component.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function meta(ServiceBase $api, array $args)
    {
        $module = $args['module'];
        $allowRestricted = $args['allowRestricted'] || false;

        return [
            'fields' => $this->getFields($module, $allowRestricted),
            'relateFields' => $this->getRelateFields($module),
            'relateModules' => $this->getRelateModules($module),
            'help' => $this->getFunctionsHelp(),
            'rollupFields' => $this->getRollupFields($module),
            'fieldsTypes' => $this->getFieldsTypes($module),
        ];
    }

    /**
     * Returns a field name/type associative array
     *
     * @param string $module
     * @return array
     */
    private function getFieldsTypes(string $module): array
    {
        return FormulaHelper::cleanFields(BeanFactory::newBean($module)->field_defs, true, false, true);
    }

    /**
     * Returns a cleaned up array of the module fields
     *
     * @param string $module
     * @param bool $allowRestricted Return Relate/FlexRelate fields
     * @param bool|string $strictType A specific type of fields you want returned. False will return all types.
     * @return array
     */
    private function getFields(string $module, bool $allowRestricted = false, $strictType = false): array
    {
        $fields = [];
        $bean = BeanFactory::newBean($module);
        $cleanFields = FormulaHelper::cleanFields($bean->field_defs, true, false, true);

        foreach ($cleanFields as $fieldName => $fieldData) {
            if (!$allowRestricted && $fieldData[1] === 'relate') {
                continue;
            }
            if ($strictType && $strictType === $fieldData[1] || !$strictType) {
                $fields[$fieldName] = $fieldName;
            }
        }

        if ((!$strictType || $strictType === 'parent') &&
            (isset($bean->field_defs['parent_name']) && $bean->field_defs['parent_name'])) {
            $fields['parent_name'] = 'parent';
        }

        return $fields;
    }

    /**
     *  Returns an array of modules related to the given module through standard Sugar relationships.
     *
     * @param string $module
     * @return array
     * @throws UnsatisfiedDependencyException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function getRelateModules(string $module): array
    {
        $relatedModules = [];
        $links = FormulaHelper::getLinksForModule($module, '');

        // get the modules
        foreach ($links as $lname => $link) {
            $relatedModules[$lname] = $link['label'];
        }

        return $relatedModules;
    }

    /**
     * Returns an array of related fields on the given module.
     *
     * @param string $module
     * @return array
     * @throws UnsatisfiedDependencyException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function getRelateFields(string $module): array
    {
        $relatedFields = [];
        $links = FormulaHelper::getLinksForModule($module, '');

        // get the fields
        if (!empty($links)) {
            reset($links);

            foreach ($links as $link) {
                $relatedFields[$link['label']] = $this->getFields($link['module']);
            }
        }

        return $relatedFields;
    }

    /**
     * Return an array of rollup-able fields.
     *
     * @param string $module
     * @return array
     * @throws UnsatisfiedDependencyException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function getRollupFields(string $module): array
    {
        $relatedFields = [];
        $links = FormulaHelper::getLinksForModule($module, '');

        // get the fields
        if (!empty($links)) {
            reset($links);

            foreach ($links as $link) {
                $relatedFields[$link['label']] = $this->getFields($link['module'], false, 'number');
            }
        }

        return $relatedFields;
    }

    /**
     * Return the description/help of each function registered in the functionmap dictionary
     *
     * @return array
     */
    private function getFunctionsHelp(): array
    {
        $FUNCTION_MAP = null;
        $functionsHelp = [];

        $cacheFile = sugar_cached('Expressions/functionmap.php');
        if (!file_exists($cacheFile)) {
            $GLOBALS['updateSilent'] = true;
            include 'include/Expressions/updatecache.php';
        }

        include $cacheFile;

        if (is_iterable($FUNCTION_MAP)) {
            foreach ($FUNCTION_MAP as $functionName => $functionData) {
                $funcPath = $functionData['src'];

                $uploadFile = new UploadFile();

                // get the file location
                $uploadFile->temp_file_location = $funcPath;

                // get contents within the file
                $doc = $uploadFile->get_file_contents();
                $docComments = [];
                $allTokens = token_get_all($doc);

                foreach ($allTokens as $tokenKey => $tokenValue) {
                    if ($tokenValue[0] == T_DOC_COMMENT) {
                        array_push($docComments, $tokenValue);
                    }
                }

                $fileDocComment = array_shift($docComments);

                if (!empty($fileDocComment[1])) {
                    // replace all of the new lines and unneeded chars
                    $functionDescription = preg_replace('/((\/\*+)|(\*+\/)|(\n\s*\*)[^\/])/', '', $fileDocComment[1]);
                    $functionDescription = str_replace('\n', ' ', str_replace('"', '', $functionDescription));

                    if (strpos($functionDescription, '<b>') !== false) {
                        $functionsHelp[$functionName] = str_replace('\r', ' ', $functionDescription);
                    }
                }
            }
        }

        return $functionsHelp;
    }
}
