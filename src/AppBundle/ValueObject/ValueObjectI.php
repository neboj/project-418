<?php


namespace AppBundle\ValueObject;


interface ValueObjectI
{
    /**
     * Get value of Value Object
     *
     * @return mixed
     */
    public function value();

    /**
     * Get instance of ValueObject
     *
     * @param $value
     * @return ValueObjectI
     */
    public static function instance($value): ValueObjectI;
}