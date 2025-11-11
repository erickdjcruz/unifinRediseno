<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Portal;

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Psr\SimpleCache\CacheInterface;

class Session
{
    /**
     * @var string
     */
    protected $contactId;

    /**
     * @var string
     */
    protected $type = 'support_portal';

    /**
     * @var string
     */
    protected $sessionContactId = 'contact_id';

    /**
     * @var \Contact
     */
    protected $contact;

    /**
     * @var array
     */
    protected $accountIds;

    /**
     * @var int
     */
    protected $accountIdsCacheTTL = 30;

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (isset($_SESSION['type']) && $_SESSION['type'] === $this->type);
    }

    /**
     * @return string
     */
    public function getContactId(): string
    {
        if (!isset($this->contactId)) {
            $this->contactId = !empty($_SESSION[$this->sessionContactId]) ? $_SESSION[$this->sessionContactId] : '';
        }

        return $this->contactId;
    }

    /**
     * @param string $contactId
     */
    public function setContactId(string $contactId): void
    {
        $this->unsetCachedContact();
        $this->contactId = $_SESSION[$this->sessionContactId] = $contactId;
    }

    /**
     * Unset previously loaded Contact and Contact id
     */
    protected function unsetCachedContact(): void
    {
        unset($this->contact);
        unset($this->contactId);
    }

    /**
     * Unset previously loaded Account ids, Contact and Contact Ids
     */
    public function unsetCache(): void
    {
        $this->getCacheObject()->delete($this->getContactCacheKey($this->getContactId()));
        $this->unsetCachedContact();
        unset($this->accountIds);
    }

    /**
     * This method decouples the cache object retrieval for testing purposes
     *
     * @return CacheInterface
     */
    public function getCacheObject(): CacheInterface
    {
        return Container::getInstance()->get(CacheInterface::class);
    }

    /**
     * @return \Contact|null
     */
    public function getContact(): ?\Contact
    {
        if (!isset($this->contact)) {
            $contactId = ($id = $this->getContactId()) ? $id : null;
            if (!empty($contactId)) {
                $this->contact = ($contact = $this->executeRetrieveContact($contactId)) ? $contact : null;
            } else {
                $this->contact = null;
            }
        }
        return $this->contact;
    }

    /**
     * This method decouples the retrieval for testing automation purposes
     *
     * @return \Contact|null
     */
    protected function executeRetrieveContact($contactId): ?\Contact
    {
        return \BeanFactory::retrieveBean('Contacts', $contactId);
    }

    /**
     * @param \SugarBean $bean
     *
     * @return Sugarcrm\Sugarcrm\Visibility\Portal\Context
     */
    public function getVisibilityContext(\SugarBean $bean): \Sugarcrm\Sugarcrm\Visibility\Portal\Context
    {
        \SugarAutoLoader::requireWithCustom('data/visibility/portal/Context.php');
        $contextClass = \SugarAutoLoader::customClass(\Sugarcrm\Sugarcrm\Visibility\Portal\Context::class);

        $visContext = new $contextClass($this->getContactId(), $this->getBeanVisibilityLinks($bean));
        $visContext->setBean($bean);

        return $visContext;
    }

    /**
     * @param \SugarBean $bean
     *
     * @return array
     */
    public function getBeanVisibilityLinks(\SugarBean $bean): array
    {
        return
            !empty($GLOBALS['dictionary'][$bean->getObjectName()]['portal_visibility']['links']) ?
                $GLOBALS['dictionary'][$bean->getObjectName()]['portal_visibility']['links'] :
                [];
    }

    /**
     * @param String $contactId
     *
     * @return string
     */
    protected function getContactCacheKey(string $contactId): string
    {
        return !empty($contactId) ? sprintf('portal_accounts_%s', strtolower(preg_replace('/\s/', '', $contactId))) : '';
    }

    /**
     * The retrieval of the contact's accounts ids is executed during the portal visibility computation
     * We use get() to bypass visibility
     *
     * @return array|null
     */
    public function getAccountIds(): ?array
    {
        if (!isset($this->accountIds)) {
            if (!empty($this->getContactId())) {
                // caching of account ids
                $cache = $this->getCacheObject();
                $cachedAccountIds = $cache->get($this->getContactCacheKey($this->getContactId()));
                if (isset($cachedAccountIds)) {
                    return json_decode(base64_decode($cachedAccountIds), true);
                }
                $this->accountIds = [];

                $contact = $this->getContact();
                if (!empty($contact)) {
                    // Link2 get() does not apply visibility, this is what we want in this occasion
                    // default to 'accounts' link field but allow for possible extensibility
                    $accountsLinkField = 'accounts';
                    if (($pvl = $this->getBeanVisibilityLinks($contact)) && !empty($pvl['Accounts'])) {
                        $accountsLinkField = $pvl['Accounts'];
                    }
                    // retrieve the related accounts ids
                    $contact->load_relationship($accountsLinkField);
                    if (isset($contact->$accountsLinkField)) {
                        $this->accountIds = $contact->$accountsLinkField->get();
                    }
                }

                // caching of account ids with short ttl
                $cache->set($this->getContactCacheKey($this->getContactId()), base64_encode(json_encode($this->accountIds)), $this->accountIdsCacheTTL);
            } else {
                $this->accountIds = [];
            }
        }
        return $this->accountIds;
    }
}
