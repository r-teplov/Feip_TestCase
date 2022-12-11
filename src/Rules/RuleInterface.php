<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Rules;

use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;

interface RuleInterface
{
    /**
     * @param mixed $value
     * @return mixed
     * @throws RuleValidateException
     */
    public function validate(mixed $value): mixed;
}