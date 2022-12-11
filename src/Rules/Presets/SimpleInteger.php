<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Rules\Presets;

use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;
use FeipTestCase\Sanitizer\Rules\RuleInterface;

class SimpleInteger implements RuleInterface
{
    private const ERROR_MESSAGE = 'Значение "%s" не является целым числом';

    /**
     * @param mixed $value
     * @return string
     */
    private function getErrorMessage(mixed $value): string
    {
        return sprintf(static::ERROR_MESSAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function validate(mixed $value): int
    {
        if (is_string($value)) {
            if (preg_match('~[^0-9]~', $value) === 1) {
                throw new RuleValidateException($this->getErrorMessage($value));
            }

            $value = (int)$value;
        }

        if (!is_integer($value)) {
            throw new RuleValidateException($this->getErrorMessage($value));
        }

        return $value;
    }
}