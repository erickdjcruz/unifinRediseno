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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EnumValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Enum) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Enum');
        }

        if (empty($value)) {
            return;
        }

        if (!is_array($value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('%msg%', 'is not array')
                ->setInvalidValue($value)
                ->setCode(Enum::ERROR_IS_NOT_ARRAY)
                ->addViolation();
        }

        $diff = array_diff($value, $constraint->allowedValues);

        if (!empty($diff)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('%msg%', 'disallowed values')
                ->setInvalidValue(implode(', ', $diff))
                ->setCode(Enum::ERROR_USED_NOT_ALLOWED_VALUE)
                ->addViolation();
        }
    }
}
