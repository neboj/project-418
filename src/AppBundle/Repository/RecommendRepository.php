<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 25/1/2018
 * Time: 21:42
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class RecommendRepository extends EntityRepository
{
    public function getAllRecommendations($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT r.*,u.first_name,u.last_name FROM `recommend` r left join user u on r.sent_by=u.id WHERE r.received_by ='.$user;
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }
}