<?php

declare(strict_types=1);

namespace FeipTestCase\Sanitizer\Attributes;

use FeipTestCase\Sanitizer\Rules\RuleInterface;

class Attribute
{
    public const TYPE_SCALAR = 0;
    public const TYPE_ARRAY = 1;
    public const TYPE_NESTED = 2;

    /**
     * @var array|Attribute[]
     */
    private array $nested = [];

    /**
     * @var ?RuleInterface
     */
    private ?RuleInterface $rule = null;

    public function __construct(
        private readonly int $type,
        private readonly string $name,
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
     * @param RuleInterface $rule
     * @return $this
     */
    public function setRule(RuleInterface $rule): self
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * @return array|Attribute[]
     */
    public function getNested(): array
    {
        return $this->nested;
    }

    /**
     * @param array|Attribute[] $nested
     * @return $this
     */
    public function setNested(array $nested): self
    {
        $this->nested = $nested;
        return $this;
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

    /**
     * @return bool
     */
    public function isNested(): bool
    {
        return $this->getType() === static::TYPE_NESTED;
    }
}