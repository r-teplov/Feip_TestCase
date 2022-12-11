<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer;

use FeipTestCase\Sanitizer\Attributes\Attribute;
use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;
use FeipTestCase\Sanitizer\Rules\Exceptions\UnknownRuleException;
use FeipTestCase\Sanitizer\Rules\RuleFactory;
use LengthException;

class Sanitizer
{
    /* Разделитель для массива простых значений */
    public const ARRAY_DELIMITER = 'array:';

    /**
     * @var array|string[]
     */
    private array $messages = [];

    public function __construct()
    {
    }

    /**
     * @return array|string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array $attrs
     * @return array|Attribute[]
     * @throws LengthException
     * @throws UnknownRuleException
     */
    private function parseAttributes(array $attrs): array
    {
        if (count($attrs) === 0) {
            throw new LengthException('Правила валидации не заданы');
        }

        $result = [];

        foreach ($attrs as $key => $value) {
            /* Вложенный массив атрибутов */
            if (is_array($value)) {
                $result[$key] = $this->parseAttributes($value);
                continue;
            }

            /* Массив из простых типов */
            $isArray = strpos($value, static::ARRAY_DELIMITER) > -1;

            $attrType = $isArray ? Attribute::TYPE_ARRAY : Attribute::TYPE_SCALAR;
            $ruleName = $isArray ? substr($value, mb_strlen(static::ARRAY_DELIMITER)) : $value;

            $result[$key] = new Attribute($attrType, $key, RuleFactory::make($ruleName));
        }

        return $result;
    }

    /**
     * @param array|string[] $attrs
     * @param array $payload
     * @return void
     * @throws UnknownRuleException
     */
    public function run(array $attrs, array $payload): void
    {
        $this->messages = [];

        $attributes = $this->parseAttributes($attrs);

        foreach ($attributes as $key => $attribute) {
            $valueToValidate = $payload[$key];

            /* todo: проверить наличие ключа в payload */

            $this->validateAttribute($attribute, $valueToValidate);
        }
    }

    /**
     * @param array|Attribute $attribute
     * @param mixed $value
     * @return void
     */
    private function validateAttribute(array|Attribute $attribute, mixed $value): void
    {
        /* Обработка вложенного массива атрибутов */
        if (is_array($attribute)) {
            foreach ($attribute as $nestedKey => $nestedAttr) {
                $this->validateAttribute($nestedAttr, $value[$nestedKey]);
            }

            return;
        }

        try {
            if ($attribute->isArray()) {
                foreach ($value as $val) {
                    $attribute->getRule()->validate($val);
                }

                return;
            }

            $attribute->getRule()->validate($value);
        } catch (RuleValidateException $ex) {
            $this->messages[] = $ex->getMessage();
        }
    }
}