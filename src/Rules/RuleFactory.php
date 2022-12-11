<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Rules;

use FeipTestCase\Sanitizer\Rules\Exceptions\UnknownRuleException;
use FeipTestCase\Sanitizer\Rules\Presets\SimpleFloat;
use FeipTestCase\Sanitizer\Rules\Presets\SimpleInteger;
use FeipTestCase\Sanitizer\Rules\Presets\SimpleString;
use FeipTestCase\Sanitizer\Rules\Presets\TelNumber;

class RuleFactory
{
    /**
     * @param string $type
     * @return RuleInterface
     * @throws UnknownRuleException
     */
    public static function make(string $type): RuleInterface
    {
        $rule = match ($type) {
            'integer' => new SimpleInteger(),
            'float' => new SimpleFloat(),
            'string' => new SimpleString(),
            'telNumber' => new TelNumber(),
            default => null,
        };

        if ($rule === null) {
            throw new UnknownRuleException('Не определено правило для типа ' . $type);
        }

        return $rule;
    }
}