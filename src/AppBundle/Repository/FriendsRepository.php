<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 22/1/2018
 * Time: 00:08
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class FriendsRepository extends EntityRepository
{
    public function getFriendRequests($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT * FROM `friends` f join user u on f.sent_by=u.id WHERE f.received_by='.$user.' AND f.is_accepted=0';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

     /*   var_dump($result);
        exit;*/

        return $result;
    }
/*select f.*,u.first_name as received_by_first_name,u.last_name as received_by_last_name,u2.first_name as sent_by_first_name,u2.last_name as sent_by_last_name from friends f join user u on f.received_by=u.id join user u2 on f.sent_by=u2.id WHERE f.is_accepted=1 AND (f.sent_by=1 OR f.received_by=1)*/

    public function getFriendsAccepted($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'select f.*,u.first_name as received_by_first_name,u.last_name as received_by_last_name,u2.first_name as sent_by_first_name,u2.last_name as sent_by_last_name from friends f join user u on f.received_by=u.id join user u2 on f.sent_by=u2.id WHERE f.is_accepted=1 AND (f.sent_by='.$user.' OR f.received_by='.$user.')';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*   var_dump($result);
           exit;*/

        return $result;
    }

    public function getRequestsFromAndToUser($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'select f.*,u.first_name as received_by_first_name,u.last_name as received_by_last_name,u2.first_name as sent_by_first_name,u2.last_name as sent_by_last_name from friends f join user u on f.received_by=u.id join user u2 on f.sent_by=u2.id WHERE  (f.sent_by='.$user.' OR f.received_by='.$user.')';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*   var_dump($result);
           exit;*/

        return $result;
    }
}