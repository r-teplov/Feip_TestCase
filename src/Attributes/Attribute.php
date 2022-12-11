<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Attributes;

use FeipTestCase\Sanitizer\Rules\RuleInterface;

class Attribute
{
    public const TYPE_SCALAR = 0;
    public const TYPE_ARRAY = 1;

    public function __construct(
        private readonly int $type,
        private readonly string $name,
        private readonly RuleInterface $rule,
    )
    {
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    /**
     * @return bool
     */
    public function isScalar(): bool
    {
        return $this->getType() === static::TYPE_SCALAR;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->getType() === static::TYPE_ARRAY;
    }
}