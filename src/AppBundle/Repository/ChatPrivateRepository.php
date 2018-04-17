<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 22/1/2018
 * Time: 03:11
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ChatPrivateRepository extends EntityRepository
{
    public function getAllChats($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT c.*,u2.first_name as initiated_by_first_name,u2.last_name as initiated_by_last_name,u.first_name as chat_with_first_name,u.last_name as chat_with_last_name FROM `chat_private` c left join user u on c.chat_with=u.id JOIN user u2 on c.initiated_by=u2.id WHERE (c.initiated_by = '.$user.' OR c.chat_with ='.$user.')';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }


/*SELECT c.*,u2.first_name as initiated_by_first_name,u2.last_name as initiated_by_last_name,u.first_name as chat_with_first_name,u.last_name as chat_with_last_name FROM `chat_private` c left join user u on c.chat_with=u.id JOIN user u2 on c.initiated_by=u2.id WHERE (c.initiated_by = 1 OR c.chat_with =1)*/



/*RASPOREDI IH PO POSLEDNJOJ STIGLOJ PORUCI*/
/*SELECT DISTINCT c.chat_name,initiated_by,chat_with FROM `chat_private` c LEFT JOIN chat_message m on c.chat_name=m.chat_name ORDER BY m.created_at DESC */


/*ovo je raspored FULL*/
/*SELECT DISTINCT c.chat_name,c.initiated_by,c.chat_with,c.created_at,u2.first_name as initiated_by_first_name,u2.last_name as initiated_by_last_name,u.first_name as chat_with_first_name,u.last_name as chat_with_last_name FROM `chat_private` c left join user u on c.chat_with=u.id JOIN user u2 on c.initiated_by=u2.id LEFT JOIN chat_message m on c.chat_name=m.chat_name WHERE (c.initiated_by = 1 OR c.chat_with =1) ORDER BY m.created_at DESC*/
    public function getAllChatsOrderByLastMessage($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT DISTINCT c.chat_name,max(m.created_at) as last_msg_created_at,c.initiated_by,c.chat_with,c.created_at,u2.first_name as initiated_by_first_name,u2.last_name as initiated_by_last_name,u2.profile_image as initiated_by_profile_image,u.profile_image as chat_with_profile_image,u.first_name as chat_with_first_name,u.last_name as chat_with_last_name FROM `chat_private` c left join user u on c.chat_with=u.id JOIN user u2 on c.initiated_by=u2.id LEFT JOIN chat_message m on c.chat_name=m.chat_name WHERE (c.initiated_by ='.$user.' OR c.chat_with ='.$user.') GROUP BY c.chat_name  ORDER BY max(m.created_at) DESC';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }


}