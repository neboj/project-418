<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 16/1/2018
 * Time: 00:26
 */

namespace AppBundle\Utils;


class MyLogger
{
    public function log( $msg ) {
        print_r( $msg . "<br />" );
    }
}