<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
