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
    private array $values = [];

    /**
     * @var array
     */
    private array $messages = [];

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
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
                $result[$key] = (new Attribute(Attribute::TYPE_NESTED, $key))
                    ->setNested($this->parseAttributes($value));
                continue;
            }

            /* Массив из простых типов */
            $isArray = strpos($value, static::ARRAY_DELIMITER) > -1;

            $attrType = $isArray ? Attribute::TYPE_ARRAY : Attribute::TYPE_SCALAR;
            $ruleName = $isArray ? substr($value, mb_strlen(static::ARRAY_DELIMITER)) : $value;

            $result[$key] = (new Attribute($attrType, $key))
                ->setRule(RuleFactory::make($ruleName));
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
        $this->values = [];
        $this->messages = [];

        $rootAttr = (new Attribute(Attribute::TYPE_NESTED, ''))
            ->setNested($this->parseAttributes($attrs));

        $this->handleAttribute($rootAttr, $payload);
    }

    /**
     * @param Attribute $attribute
     * @param mixed $value
     * @param string $rootKey
     * @return void
     */
    private function handleAttribute(Attribute $attribute, mixed $value, string $rootKey = ''): void
    {
        /* Обработка вложенного массива атрибутов */
        if ($attribute->isNested()) {
            foreach ($attribute->getNested() as $nestedKey => $nestedAttr) {
                $compositeKey = $rootKey === '' ? $nestedKey : $rootKey . '.' . $nestedKey;

                if (!array_key_exists($nestedKey, $value)) {
                    $this->messages[$compositeKey] = 'Атрибут "' . $nestedKey . '" отсутствует в наборе данных';
                    continue;
                }

                $this->handleAttribute($nestedAttr, $value[$nestedKey], $compositeKey);
            }

            return;
        }

        if ($attribute->isArray()) {
            $this->validateArray($rootKey, $attribute, $value);
            return;
        }

        $this->validateScalar($rootKey, $attribute, $value);
    }

    /**
     * @param string $attributeKey
     * @param Attribute $attribute
     * @param array $value
     * @return void
     */
    private function validateArray(string $attributeKey, Attribute $attribute, array $value): void
    {
        foreach ($value as $index => $val) {
            $this->validateScalar($attributeKey . '.' . $index, $attribute, $val);
        }
    }

    /**
     * @param string $attributeKey
     * @param Attribute $attribute
     * @param mixed $value
     * @return void
     */
    private function validateScalar(string $attributeKey, Attribute $attribute, mixed $value): void
    {
        try {
            $this->values[$attributeKey] = $attribute->getRule()->validate($value);
        } catch (RuleValidateException $ex) {
            $this->messages[$attributeKey] = $ex->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getExpandedValues(): array
    {
        $result = array();

        foreach ($this->getValues() as $key => $value) {
            $parts = explode(".", $key);
            $currentArray = &$result;

            for ($i = 0; $i < count($parts) - 1; $i++) {
                if (!isset($currentArray[$parts[$i]])) {
                    $currentArray[$parts[$i]] = array();
                }
                $currentArray = &$currentArray[$parts[$i]];
            }

            $currentArray[$parts[count($parts) - 1]] = $value;
        }

        return $result;
    }
}