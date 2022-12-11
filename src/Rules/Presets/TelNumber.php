<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Rules\Presets;

use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;
use FeipTestCase\Sanitizer\Rules\RuleInterface;

class TelNumber implements RuleInterface
{
    private const ERROR_NOT_A_STRING = 0;
    private const ERROR_WRONG_CHARACTERS = 1;
    private const ERROR_LENGTH = 2;

    private const ERROR_MESSAGES = [
        self::ERROR_NOT_A_STRING => 'Значение "%s" должно быть строкой',
        self::ERROR_WRONG_CHARACTERS => 'Значение "%s" ',
        self::ERROR_LENGTH => 'Номер телефона должен состоять из 11 цифр',
    ];

    /**
     * @param int $type
     * @param mixed $value
     * @return string
     */
    private function getErrorMessage(int $type, mixed $value): string
    {
        return array_key_exists($type, static::ERROR_MESSAGES)
            ? sprintf(static::ERROR_MESSAGES[$type], $value)
            : '';
    }

    /**
     * @inheritDoc
     */
    public function validate(mixed $value): void
    {
        if (is_integer($value)) {
            $value = strval($value);
        }

        if (!is_string($value)) {
            throw new RuleValidateException($this->getErrorMessage(static::ERROR_NOT_A_STRING, $value));
        }

        /* Строка должна стоятоять из цифр и разделителей */
        if (preg_match('~[^0-9()+\-\s]~', $value) === 1) {
            throw new RuleValidateException($this->getErrorMessage(static::ERROR_WRONG_CHARACTERS, $value));
        }

        /* Вырезать разделители */
        $parsedValue = preg_replace('~[()+\-\s]~', '', $value);

        if (mb_strlen($parsedValue) !== 11) {
            throw new RuleValidateException($this->getErrorMessage(static::ERROR_LENGTH, $value));
        }
    }
}