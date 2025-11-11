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

class EAPMViewEdit extends ViewEdit
{
    private $_returnId;

    public function __construct()
    {
        parent::__construct();
        $this->setReturnId();
    }

    protected function setReturnId()
    {
        $returnId = $GLOBALS['current_user']->id;
        $userId = $this->request->getValidInputRequest('user_id', 'Assert\Guid');
        $returnModule = $this->request->getValidInputRequest('return_module', 'Assert\Mvc\ModuleName');
        if (!empty($userId) && !empty($returnModule) && 'Users' == $returnModule) {
            $returnId = $userId;
        }
        $this->_returnId = $returnId;
    }

    protected function _getModuleTab()
    {
        return 'Users';
    }

    /**
     * @see SugarView::_getModuleTitleParams()
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getModuleTitleParams($browserTitle = false)
    {
        global $mod_strings;

        $returnAction = 'DetailView';
        $returnModule = 'Users';
        $returnId = $this->bean->assigned_user_id;
        $returnName = $this->bean->assigned_user_name;

        $returnModuleFromRequest = $this->request->getValidInputRequest('return_module', 'Assert\Mvc\ModuleName');
        $returnActionFromRequest = $this->request->getValidInputRequest('return_action');
        $returnNameFromRequest = $this->request->getValidInputRequest('return_name');
        $returnIdFromRequest = $this->request->getValidInputRequest('user_id', 'Assert\Guid');

        if (!empty($returnActionFromRequest) && !empty($returnModuleFromRequest)) {
            if ('Users' == $returnModuleFromRequest) {
                if ('EditView' == $returnActionFromRequest) {
                    $returnAction = 'EditView';
                }
                if (!empty($returnNameFromRequest)) {
                    $returnName = $returnNameFromRequest;
                }
                if (!empty($returnIdFromRequest)) {
                    $returnId = $returnIdFromRequest;
                }
            }
        }
        $this->_returnId = $returnId;

        $iconPath = $this->getModuleTitleIconPath($this->module);
        $params = [];
        if (!empty($iconPath) && !$browserTitle) {
            $params[] = '<a href="#Users"><!--not_in_theme!-->
                            <img src="' . htmlspecialchars($iconPath, ENT_COMPAT) . '"  alt="' . htmlspecialchars(translate('LBL_MODULE_NAME', 'Users'), ENT_COMPAT) . '" 
                                title="' . htmlspecialchars(translate('LBL_MODULE_NAME', 'Users'), ENT_COMPAT) . '" align="absmiddle" />
                        </a>';
        } else {
            $params[] = htmlspecialchars(translate('LBL_MODULE_NAME', 'Users'), ENT_COMPAT);
        }
        $href = 'index.php?' . http_build_query(['module' => $returnModule, 'action' => 'EditView', 'record' => $returnId]);
        $params[] = '<a href="' . htmlspecialchars($href, ENT_COMPAT) . '">' . htmlspecialchars($returnName, ENT_COMPAT) . '</a>';
        $params[] = htmlspecialchars($GLOBALS['app_strings']['LBL_EDIT_BUTTON_LABEL'], ENT_COMPAT);

        return $params;
    }

    /**
     * @see SugarView::getModuleTitleIconPath()
     */
    protected function getModuleTitleIconPath($module)
    {
        return parent::getModuleTitleIconPath('Users');
    }

    public function display()
    {
        $returnModuleFromRequest = $this->request->getValidInputRequest('return_module', 'Assert\Mvc\ModuleName');
        $returnActionFromRequest = $this->request->getValidInputRequest('return_action');
        $applicationFromRequest = $this->request->getValidInputRequest(
            'application',
            [
                'Assert\Choice' => [
                    'choices' => array_keys(ExternalAPIFactory::loadFullAPIList()),
                ],
            ]
        );

        $this->bean->password = empty($this->bean->password) ? '' : EAPM::$passwordPlaceholder;

        $this->ss->assign('return_id', $this->_returnId);

        $cancelUrl = "index.php?action=EditView&module=Users&record={$this->_returnId}#tab5";

        if ($returnModuleFromRequest !== null && $returnModuleFromRequest == 'Import') {
            $cancelUrl = 'index.php?module=Import&action=Step1&import_module='
                . $returnActionFromRequest . '&application=' . $applicationFromRequest;
        }
        $this->ss->assign('cancelUrl', $cancelUrl);

        if ($GLOBALS['current_user']->is_admin || empty($this->bean) || empty($this->bean->id) || $this->bean->isOwner($GLOBALS['current_user']->id)) {
            if (!empty($this->bean) && empty($this->bean->id) && $this->_returnId != $GLOBALS['current_user']->id) {
                $this->bean->assigned_user_id = $this->_returnId;
            }

            parent::display();
        } else {
            ACLController::displayNoAccess();
        }
    }
}
