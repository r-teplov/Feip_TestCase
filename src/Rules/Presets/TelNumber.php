<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Rules\Presets;

use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;
use FeipTestCase\Sanitizer\Rules\RuleInterface;

class TelNumber implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $value): void
    {
        if (is_integer($value)) {
            $value = strval($value);
        }

        if (!is_string($value)) {
            throw new RuleValidateException('Значение не является номером телефона');
        }

        /* Строка должна стоятоять из цифр и разделителей */
        if (preg_match('~[^0-9()+\-\s]~', $value) === 1) {
            throw new RuleValidateException('Значение содержит символы отличные от цифр');
        }

        /* Вырезать разделители */
        $parsedValue = preg_replace('~[()+\-\s]~', '', $value);

        if (mb_strlen($parsedValue) !== 11) {
            throw new RuleValidateException('Номер телефона должен состоять из 11 цифр');
        }
    }
}