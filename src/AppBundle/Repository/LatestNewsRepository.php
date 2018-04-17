<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 11/1/2018
 * Time: 21:23
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class LatestNewsRepository extends EntityRepository
{
    public function getLatestNews(){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT ln.*,r.user as reviewer,u.first_name,u1.first_name as reviewer_name,usl.name FROM `latest_news` ln LEFT JOIN review r on ln.review = r.id JOIN user u on ln.action_performer=u.id LEFT JOIN user u1 on r.user=u1.id LEFT JOIN users_list usl on ln.list=usl.id ORDER BY `ln`.`created_at` DESC LIMIT 30';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }

    /*u skupu sa MOVIE entity*/
/*SELECT ln.*,r.user as reviewer,u.first_name,u1.first_name as reviewer_name,usl.name,mov.* FROM `latest_news` ln LEFT JOIN review r on ln.review = r.id JOIN user u on ln.action_performer=u.id LEFT JOIN user u1 on r.user=u1.id LEFT JOIN users_list usl on ln.list=usl.id LEFT JOIN movie mov on ln.movie=mov.id ORDER BY `ln`.`created_at` DESC */
    public function getLatestNewsJoinMovieEntity(){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT ln.*,r.user as reviewer,u.first_name,u.profile_image,u1.first_name as reviewer_name,usl.name,usl.is_private,mov.* FROM `latest_news` ln LEFT JOIN review r on ln.review = r.id JOIN user u on ln.action_performer=u.id LEFT JOIN user u1 on r.user=u1.id LEFT JOIN users_list usl on ln.list=usl.id LEFT JOIN movie mov on ln.movie=mov.id ORDER BY `ln`.`created_at` DESC LIMIT 30';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }
}