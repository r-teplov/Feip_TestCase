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
            $this->parsedRules[$key] = RuleFactory::make($rule);
        }
    }

    /**
     * @param array $rules
     * @param array $data
     * @return void
     * @throws UnknownRuleException
     */
    public function run(array $rules, array $data): void
    {
        $this->parsedRules = [];
        $this->messages = [];

        $this->initRules($rules);

        foreach ($this->parsedRules as $fieldName => $rule) {
            $valueToValidate = $data[$fieldName];

            if (is_array($valueToValidate)) {
                continue;
            }

            try {
                $rule->validate($valueToValidate);
            } catch (RuleValidateException $ex) {
                $this->messages[] = $ex->getMessage();
            }
        }
    }
}