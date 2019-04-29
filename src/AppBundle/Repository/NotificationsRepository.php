<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 26/1/2018
 * Time: 00:21
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class NotificationsRepository extends EntityRepository
{
        public function getAllNotifications($user){
            $em = $this->getEntityManager();
            $RAW_QUERY = 'SELECT n.*,u.first_name,u.last_name,u.profile_image FROM `notifications` n  LEFT JOIN user u on n.received_by=u.id  WHERE n.received_by='.$user;
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();

            $result = $statement->fetchAll();

            /*var_dump($result);
            exit;*/

            return $result;
        }


}