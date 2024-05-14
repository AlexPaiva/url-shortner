<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidUrl extends Constraint
{
    public $message = 'The URL "{{ value }}" is not a valid URL.';
}
