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

class MetaDataManagerConnections extends MetaDataManager
{
    /**
     * View=full includes all fields, even links and collections. View=detail
     * excludes links and collections unless they are included via view
     * metadata. View metadata may be used to control display parameters for any
     * fields. Any other view name falls back to the standard behavior as
     * defined by the base platform.
     *
     * @param string $moduleName The module name.
     * @param string $view The view name.
     * @param array $displayParams Associative array of field names and their
     *                             display params on the given view.
     *
     * @return array Flat list of fields for the given module and view.
     */
    public function getModuleViewFields($moduleName, $view, &$displayParams = [])
    {
        $allFields = VardefManager::getFieldDefs($moduleName);

        switch (strtolower($view)) {
            case 'full':
                // Include everything.
                break;
            case 'detail':
                // Exclude links and collections.
                $allFields = array_filter(
                    $allFields,
                    function ($fieldDef) {
                        return !in_array($fieldDef['type'], ['collection', 'link']);
                    }
                );
                break;
            default:
                // Fall back to standard behavior.
                $allFields = [];
        }

        // Only the field names are needed.
        $allFields = array_keys($allFields);

        // Get the fields specifically named in the viewdef.
        $viewFields = parent::getModuleViewFields($moduleName, $view, $displayParams);

        // Include fields from the viewdef that would otherwise by suppressed.
        $fields = array_merge($allFields, $viewFields);

        return array_unique($fields);
    }
}
