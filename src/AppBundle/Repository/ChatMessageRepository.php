<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 23/1/2018
 * Time: 15:42
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ChatMessageRepository extends EntityRepository
{
    public function getMaxId($chatname){
        $em = $this->getEntityManager();
        $RAW_QUERY = "SELECT MAX(message_id) as maxi FROM chat_message WHERE chat_name='".$chatname."'";
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }

}