<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 28/12/2017
 * Time: 14:06
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
class UsersListRepository extends EntityRepository
{
    public function maxID(){
        $query=$this->createQueryBuilder('a');
        $query->select('MAX(a.id) as max_id');
        return $query->getQuery()->getSingleScalarResult();

    }


}