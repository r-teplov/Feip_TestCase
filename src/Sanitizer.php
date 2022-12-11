<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer;

use FeipTestCase\Sanitizer\Rules\Exceptions\RuleValidateException;
use FeipTestCase\Sanitizer\Rules\Exceptions\UnknownRuleException;
use FeipTestCase\Sanitizer\Rules\RuleFactory;
use FeipTestCase\Sanitizer\Rules\RuleInterface;
use LengthException;

class Sanitizer
{
    public const ARRAY_DELIMITER = 'array:';

    /**
     * @var array|RuleInterface[]
     */
    private array $parsedRules = [];

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
     * @param array $rules
     * @return void
     * @throws LengthException
     * @throws UnknownRuleException
     */
    private function initRules(array $rules): void
    {
        if (count($rules) === 0) {
            throw new LengthException('Правила валидации не заданы');
        }

        foreach ($rules as $key => $rule) {
            $isArray = strpos($rule, static::ARRAY_DELIMITER);

            if ($isArray > -1) {
                $type = substr($rule, mb_strlen(static::ARRAY_DELIMITER));
                $this->parsedRules[$key] = RuleFactory::make($type);
                continue;
            }

            $this->parsedRules[$key] = RuleFactory::make($rule);
        }
    }

    /**
     * @param array $rules
     * @param array $payload
     * @return void
     * @throws UnknownRuleException
     */
    public function run(array $rules, array $payload): void
    {
        $this->parsedRules = [];
        $this->messages = [];

        $this->initRules($rules);

        foreach ($this->parsedRules as $fieldName => $rule) {
            $valueToValidate = $payload[$fieldName];

            try {
                if (is_array($valueToValidate)) {
                    foreach ($valueToValidate as $value) {
                        $rule->validate($value);
                    }

                    continue;
                }

                $rule->validate($valueToValidate);
            } catch (RuleValidateException $ex) {
                $this->messages[] = $ex->getMessage();
            }
        }
    }
}