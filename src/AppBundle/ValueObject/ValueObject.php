<?php
declare(strict_types=1);

namespace AppBundle\ValueObject;


use AppBundle\ValueObject\Exceptions\InvalidValueException;

class ValueObject implements ValueObjectI
{
    /**
     * @var ValueObjectI[]
     */
    protected static $instances;

    /**
     * @var ValueObjectI
     */
    private $value;

    /**
     * @var string
     */
    private $valueType;

    private function __construct($value)
    {
        $this->value = $value;
        $this->valueType = gettype($value)[0];
    }

    /**
     * @param $class
     * @param $value
     * @return ValueObjectI
     */
    protected static function makeInstance($class, $value) {
        if (!self::$instances[$value]) {
            self::$instances[$value] = new $class($value);
        }
        return self::$instances[$value];
    }

    /**
     * Get value of Value Object
     *
     * @return mixed
     */
    public function value() {
        return $this->value;
    }

    /**
     * Get instance of ValueObject
     *
     * @param $value
     * @return ValueObjectI
     * @throws InvalidValueException
     */
    public static function instance($value): ValueObjectI
    {
        self::validateValue($value);
        return self::makeInstance(self::class, $value);
    }

    /**
     * @param $value
     * @throws InvalidValueException
     */
    public static function validateValue($value) {
        if (is_array($value) || is_object($value)) {
            throw new InvalidValueException('Invalid value');
        }
    }
}