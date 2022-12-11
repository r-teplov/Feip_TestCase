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
     * @var array
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
            if (!array_key_exists($key, $payload)) {
                $this->messages[$key] = 'Атрибут "' . $key . '" отсутствует в наборе данных';
                continue;
            }

            $this->handleAttribute($key, $attribute, $payload[$key]);
        }
    }

    /**
     * @param string $parentKey
     * @param array|Attribute $attribute
     * @param mixed $value
     * @return void
     */
    private function handleAttribute(string $parentKey, array|Attribute $attribute, mixed $value): void
    {
        /* Обработка вложенного массива атрибутов */
        if (is_array($attribute)) {
            foreach ($attribute as $nestedKey => $nestedAttr) {
                if (!array_key_exists($nestedKey, $value)) {
                    $this->messages[$parentKey . '.' . $nestedKey] = 'Атрибут "' . $nestedKey . '" отсутствует в наборе данных';
                    continue;
                }

                $this->handleAttribute($parentKey, $nestedAttr, $value[$nestedKey]);
            }

            return;
        }

        if ($attribute->isArray()) {
            foreach ($value as $val) {
                try {
                    $attribute->getRule()->validate($val);
                } catch (RuleValidateException $ex) {
                    $errorKey = $parentKey !== $attribute->getName() ? $parentKey . '.' . $attribute->getName() : $attribute->getName();
                    $this->messages[$errorKey][] = $ex->getMessage();
                }
            }

            return;
        }

        try {
            $attribute->getRule()->validate($value);
        } catch (RuleValidateException $ex) {
            $errorKey = $parentKey !== $attribute->getName() ? $parentKey . '.' . $attribute->getName() : $attribute->getName();
            $this->messages[$errorKey] = $ex->getMessage();
        }
    }
}