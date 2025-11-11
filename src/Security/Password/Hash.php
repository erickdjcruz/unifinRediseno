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

namespace Sugarcrm\Sugarcrm\Security\Password;

/**
 *
 * Password hashing main class
 *
 */
class Hash
{
    public const DEFAULT_BACKEND = 'native';

    /**
     * @var Hash
     */
    protected static $instance;

    /**
     * Password hash backend
     * @var BackendInterface
     */
    protected $backend;

    /**
     * Enable/disable backend rehash check
     * @var boolean
     */
    protected $rehash = true;

    /**
     * Ctor
     * @param BackendInterface $hash
     */
    public function __construct(BackendInterface $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Overload method for hash backend
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        return call_user_func_array([$this->backend, $method], $arguments);
    }

    /**
     * Get instance based on \SugarConfig settings
     *
     *  $sugar_config['passwordHash']['rehash'] = true/false
     *      Enable or disable the rehash capability for the selected
     *      backend, defaults to true
     *
     *  $sugar_config['passwordHash']['backend'] = 'native' | 'sha2'
     *      Select the backend, defaults to 'native'
     *
     *  $sugar_config['passwordHash']['algo']
     *      Hash algorithm to be used, see selected backend
     *
     *  $sugar_config['passwordHash']['options']
     *      Backend configuration options, see selected backend
     *
     * @return Hash
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            //Due to security issue we don't allow use Sha2 strategy and move all clients without IDM to Native strategy
            self::$instance = $instance = new self(self::getHashBackend(self::DEFAULT_BACKEND, null, []));

            $instance->setRehash(true);
        }
        return self::$instance;
    }

    /**
     * Create password hash backend object. Use self::getInstance unless
     * you know what you are doing.
     *
     * @param string $class Backend short class name
     * @param string $algo
     * @param array $options
     * @return BackendInterface
     */
    public static function getHashBackend($class, $algo = null, array $options = [])
    {
        $class = \SugarAutoLoader::customClass('\\Sugarcrm\\Sugarcrm\\Security\\Password\\Backend\\' . ucfirst($class));

        /* @var $backend BackendInterface */
        $backend = new $class();
        $backend->setOptions($options);

        if ($algo !== null) {
            $backend->setAlgo($algo);
        }

        // inject salt generator if required
        if ($backend instanceof SaltConsumerInterface) {
            $backend->setSalt(new Salt());
        }

        return $backend;
    }

    /**
     * Set rehash flag
     * @param boolean $toggle
     */
    public function setRehash($toggle)
    {
        $this->rehash = (bool)$toggle;
    }

    /**
     * Validate given password against hash
     * @param string $password
     * @return boolean
     */
    public function verify($password, $hash)
    {
        return $this->verifyMd5(md5($password), $hash);
    }

    /**
     * Validate given md5 encoded password against hash
     * @param string $password MD5 encoded password
     * @return boolean
     */
    public function verifyMd5($password, $hash)
    {
        if (empty($password) || empty($hash)) {
            return false;
        }

        // using lower case encoded password
        $password = strtolower($password);

        return $this->backend->verify($password, $hash);
    }

    /**
     * Create hash for given password
     * @param unknown $password
     */
    public function hash($password)
    {
        // Passwords are always hashed first using md5 before passing to the
        // backend. This is for historical reasons and more in specific to
        // be able to support the SOAP api which can take directly md5 hashed
        // passwords.

        return $this->backend->hash(md5($password));
    }

    /**
     * Verify if given hash needs rehashing. If rehash support is not
     * enabled this will always return false regardless of the backend.
     *
     * @param string $hash
     * @return boolean
     */
    public function needsRehash($hash)
    {
        return $this->rehash ? $this->backend->needsRehash($hash) : false;
    }
}
