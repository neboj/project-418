<?php


namespace AppBundle\ValueObject\Validators;


interface ValidatorI
{
    /**
     * @param $value
     * @return mixed
     */
    public function validate($value);

}