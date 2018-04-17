<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 28/1/2018
 * Time: 17:09
 */

namespace AppBundle\Entity;


interface UserInterface extends \FOS\UserBundle\Model\UserInterface
{
    public function setFirstName($firstName);
}