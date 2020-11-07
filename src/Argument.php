<?php

namespace Consolly\Argument;

class Argument
{
    public const Option = 100;
    public const EqualSeparatedOption = 101;
    public const Value = 200;
    public const Command = 400;

    protected string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    protected string $value;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    protected ?int $position;

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    protected int $type;

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    protected bool $abbreviated;

    /**
     * @return bool
     */
    public function isAbbreviated(): bool
    {
        return $this->abbreviated;
    }

    /**
     * @param bool $abbreviated
     */
    public function setAbbreviated(bool $abbreviated): void
    {
        $this->abbreviated = $abbreviated;
    }

    public function __construct()
    {
        $this->type = 0;
        $this->value = false;
        $this->name = false;
        $this->abbreviated = false;
        $this->position = null;
    }

    public function build(): string
    {
        $value = ($this->isAbbreviated()) ? '-' : '--';

        if ($this->type === self::EqualSeparatedOption)
        {
            return "$value$this->name".(($this->value != false) ? "=$this->value" : false);
        }

        if ($this->type === self::Option)
        {
            return "$value$this->name".(($this->value != false) ? " $this->value" : false);
        }

        if ($this->type === self::Value)
        {
            return "'$this->value'";
        }

        return $this->value;
    }

    public static function isOption(string $option): bool
    {
        return strpos($option, "-") === 0 || strpos($option, "--") === 0;
    }

    public static function isAbbreviation(string $option): bool
    {
        return strpos($option, "-") === 0 && strpos($option, "--") === false;
    }

    public static function isAbbreviations(string $option): bool
    {
        return self::isAbbreviation($option) && strlen($option) > 2;
    }

    public static function isValue(string $value): bool
    {
        $len = strlen($value)-1;

        return ($value[0] === '"' && $value[$len] === '"')
            || ($value[0] === "'" && $value[$len] === "'");
    }

    public static function isCommand(string $command): bool
    {
        return !self::isValue($command);
    }
}