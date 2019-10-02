<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 24/1/2018
 * Time: 21:54
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getPeopleQuery($txt){
        $em = $this->getEntityManager();
        $RAW_QUERY = "SELECT profile_image,username,id,CONCAT(first_name, ' ', last_name) as ime_i_prezime FROM user WHERE CONCAT(first_name, ' ', last_name) LIKE '".$txt."%'";
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }
}