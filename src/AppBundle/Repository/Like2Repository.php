<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 7/1/2018
 * Time: 09:43
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class Like2Repository extends EntityRepository
{
    public function getLikesOfReviews($movie){
        return $this->getEntityManager()
            ->createQuery(
                "SELECT l FROM AppBundle:Like2 l join AppBundle:Review r WHERE r.movie=$movie"
            )
            ->getResult();
    }

    public function getLikesOfReviews1($movie){
        return $this->getEntityManager()
            ->createQuery(
                "SELECT l FROM AppBundle:Like2 l join AppBundle:Review r WHERE r.movie=$movie"
            )
            ->getArrayResult();
    }
}