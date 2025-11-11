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

namespace Sugarcrm\Sugarcrm\Security\Validator\Constraints;

use SugarConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 *
 * Platform Client validator
 *
 * This constraint validates if a given client name is allowed or not for the current platform:
 *  - Validate against a list of allowed clients for the platform
 *
 */
class PlatformClientValidator extends ConstraintValidator
{
    /**
     * Allowed clients for platforms
     * @var array
     */
    protected array $platformClients = [];

    /**
     * @param array|null $platformClients
     */
    public function __construct(array $platformClients = null)
    {
        $this->platformClients = $platformClients ?: SugarConfig::getInstance()->get('api.allowedClients', []);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PlatformClient) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\PlatformClient');
        }

        $allowedClients = $this->platformClients[$constraint->platform]?? [];
        if (empty($allowedClients) || !is_array($allowedClients)) {
            return;
        }

        if (null === $value || '' === $value) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }


        $value = (string)$value;

        foreach ($allowedClients as $allowedClient) {
            if (preg_match('#\b' . preg_quote($allowedClient, '#') . '\b#i', $value)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setInvalidValue($value)
            ->setCode(PlatformClient::ERROR_INVALID_PLATFORM_CLIENT)
            ->setParameters([
                '%platform%' => $constraint->platform,
                '%client%' => $value,
                '%reason%' => 'unknown client or platform',
            ])
            ->addViolation();
    }
}
