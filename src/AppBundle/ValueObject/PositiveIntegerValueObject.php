<?php


namespace AppBundle\ValueObject;


use AppBundle\ValueObject\Exceptions\InvalidValueException;
use AppBundle\ValueObject\Validators\PositiveInteger;

class PositiveIntegerValueObject extends ValueObject
{
    /**
     * @param $value
     * @throws InvalidValueException
     */
    public static function validateValue($value) {
        parent::validateValue($value);
        if (!is_int($value) || ($value <= 0)) {
            throw new InvalidValueException("Invalid value");
        }
    }

}