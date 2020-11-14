<?php

namespace Consolly\Argument;

/**
 * Class Argument contains main functional for working with console arguments
 *
 * @package Consolly\Argument
 */
class Argument
{
    public const Option = 100;
    public const EqualSeparatedOption = 101;
    public const Value = 200;
    public const Command = 300;

    /**
     * Contains raw argument
     *
     * @var string $arg
     */
    protected string $arg;

    /**
     * Returns raw argument
     *
     * @return string
     */
    public function getArg(): string
    {
        return $this->arg;
    }

    /**
     * Returns raw argument
     *
     * @param string $arg
     */
    public function setArg(string $arg): void
    {
        $this->arg = $arg;
    }

    /**
     * Contains argument name or false if not specified
     *
     * @var false|string $name
     */
    protected string $name;

    /**
     * Returns argument name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Contains argument name
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Contains argument value or false if value not specified
     *
     * @var false|string $value
     */
    protected string $value;

    /**
     * Returns argument value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Sets argument value
     *
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * Contains argument position
     *
     * @var int|null $position
     */
    protected ?int $position;

    /**
     * Returns argument position
     *
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Sets argument position
     *
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * Contains argument type
     *
     * @var int $type
     */
    protected int $type;

    /**
     * Returns argument type
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Sets argument type
     *
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * Contains is option is abbreviated
     *
     * @var bool $abbreviated
     */
    protected bool $abbreviated;

    /**
     * @return bool
     */
    public function isAbbreviated(): bool
    {
        return $this->abbreviated;
    }

    /**
     * Sets is option is abbreviated
     *
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

    /**
     * Builds argument by given data
     *
     * @return string
     * Returns structured argument
     */
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

    /**
     * Shortcut which parses given string and returns argument instance with parsed data
     *
     * @param string $argument
     * String for parse
     *
     * @return Argument
     * Instance for argument with parsed data
     */
    public static function parse(string $argument): Argument
    {
        $arg = new Argument();

        $arg->setArg($argument);
        $arg->setPosition(null);

        if (self::isOption($argument))
        {
            $arg->setAbbreviated(self::isAbbreviation($argument));

            $explodedArg = explode("=", $argument);

            if (count($explodedArg) >= 2)
            {
                $arg->setType(self::EqualSeparatedOption);

                $arg->setName(trim($explodedArg[0], '-'));
                $arg->setValue(trim($explodedArg[1], "'\""));
            }
            else
            {
                $arg->setType(self::Option);

                $arg->setName(trim($argument,'-'));
            }
        }
        elseif (self::isValue($argument))
        {
            $arg->setType(self::Value);

            $arg->setValue(trim($argument, '"\''));
        }
        else
        {
            $arg->setType(self::Command);

            $arg->setValue($argument);
        }

        return $arg;
    }

    /**
     * Splits given string and returns array of chars
     *
     * @param string $option
     * String for split
     *
     * @param bool $prefix
     * If true then prefix '-' will be added for each char
     *
     * @return array
     * Array of char.
     * If prefix true then each item of array will be with prefix
     */
    public static function toAbbreviations(string $option, bool $prefix = false): array
    {
        $options = str_split(trim($option, '-'));

        if ($prefix)
        {
            array_walk($options, function (&$item) {
                $item = "-$item";
            });
        }

        return $options;
    }

    /**
     * Converts given string o option
     *
     * @param string $name
     * @return string
     * Returns option as string
     */
    public static function toOption(string $name): string
    {
        return sprintf('--%s', trim($name, "\"'-"));
    }

    /**
     * Converts given string to abbreviated option
     *
     * @param string $name
     * @return string
     * Returns option as string
     */
    public static function toAbbreviation(string $name): string
    {
        $name = trim($name, "\"'-");

        if (strlen($name) < 1)
        {
            return false;
        }

        return sprintf('-%s', $name[0]);
    }

    /**
     * Checks if given string is option
     *
     * @param string $option
     * @return bool
     */
    public static function isOption(string $option): bool
    {
        return strpos($option, "-") === 0 || strpos($option, "--") === 0;
    }

    /**
     * Checks if given string is abbreviated option
     *
     * @param string $option
     * @return bool
     */
    public static function isAbbreviation(string $option): bool
    {
        return strpos($option, "-") === 0 && strpos($option, "--") === false;
    }

    /**
     * Checks if given string is array of abbreviated options
     *
     * @param string $option
     * @return bool
     */
    public static function isAbbreviations(string $option): bool
    {
        return self::isAbbreviation($option) && strlen($option) > 2;
    }

    /**
     * Checks if given string is value
     *
     * @param string $value
     * @return bool
     */
    public static function isValue(string $value): bool
    {
        $len = strlen($value)-1;

        if ($len <= 0)
        {
            return false;
        }

        return ($value[0] === '"' && $value[$len] === '"')
            || ($value[0] === "'" && $value[$len] === "'");
    }

    /**
     * Checks is given string is a command
     *
     * @param string $command
     * @return bool
     */
    public static function isCommand(string $command): bool
    {
        return !self::isValue($command) && !self::isOption($command);
    }
}