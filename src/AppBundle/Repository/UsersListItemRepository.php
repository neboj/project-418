<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 26/12/2017
 * Time: 19:12
 */

namespace AppBundle\Repository;

use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use Doctrine\ORM\EntityRepository;
class UsersListItemRepository extends EntityRepository
{

    public function getMaxID($listId){
        return $this->getEntityManager()
            ->createQuery(
                "SELECT count(p.listitemid) as maxID FROM AppBundle:UsersListItem p WHERE p.listid={$listId}"
            )
            ->getResult();
    }

    public function alreadyInList($userId, $listId, $movieId){

        return $this->getEntityManager()
            ->createQuery(
                "SELECT l FROM AppBundle:UsersList ul JOIN AppBundle:UsersListItem l  WHERE ul.user={$userId} AND l.listid={$listId} AND l.movie={$movieId}"
            )
            ->getResult();
    }

    public function getListsAndLtems($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT item.listid,item.listitemid,item.name as movie_title,item.movie as movie_id,list.name as list_name,list.user FROM `users_list_item` item join users_list list on item.listid=list.id';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }

    public function findItemsForThisUser($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT items.*,list.name as list_name FROM `users_list_item` items join users_list list on items.listid=list.id where list.user=' . $user;
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }

}