<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Rules\Presets;

use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;
use FeipTestCase\Sanitizer\Rules\RuleInterface;

class SimpleFloat implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $value): void
    {
        if (!is_float($value) && !is_integer($value)) {
            throw new RuleValidateException('Значение не является числом с плавающей точкой');
        }
    }
}