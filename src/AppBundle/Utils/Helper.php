<?php


namespace AppBundle\Utils;


class Helper
{
    /**
     * @param $array
     * @return \stdClass
     */
    public function transformArrayToObject($array) {
        $result = new \stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->transformArrayToObject($value);
            }
            $result->$key = $value;
        }
        return $result;
    }
}