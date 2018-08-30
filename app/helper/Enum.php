<?php

trait Enum
{
    private $scalar;

    final public function __construct($k)
    {
        if (!self::isValidValue($k)) {
            throw new \InvalidArgumentException("Valie [{$k}] is not defined.");
        }

        $ks = array_flip(self::ENUM);
        $this->scalar = $ks[$k];
    }

    final public static function isValidValue($k)
    {
        return in_array($k, self::ENUM, true);
    }

    final public function __toString()
    {
        return (string)$this->scalar;
    }

    final public function __invoke()
    {
        return $this->scalar;
    }

    final public static function __callStatic($method, array $args)
    {
        return new self($method);
    }

    final public function __set($k, $v)
    {
        throw new \BadMethodCallException('All setter is forbbiden');
    }
}
