<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 4/1/2018
 * Time: 20:51
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ReviewRepository extends EntityRepository
{
    public function getMaxID(){
        return $this->getEntityManager()
            ->createQuery(
                "SELECT MAX(r.id) as maxi FROM AppBundle:Review r"
            )->getResult();
    }

    public function userReviewedMovies($user){
        $em = $this->getEntityManager();
        $RAW_QUERY = 'SELECT DISTINCT movie FROM `review` WHERE user='.$user;
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }

    public function getAllWhoReviewed(){
        $em = $this->getEntityManager();
        $RAW_QUERY = "SELECT movie,user FROM `review` GROUP BY user,movie ";
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }

/*ovo za latest reviews*/
/*SELECT r.*,u.first_name,u.last_name,m.* FROM `review`r LEFT JOIN user u on r.user=u.id LEFT JOIN movie m on r.movie=m.id WHERE is_gif=false ORDER BY created_at DESC LIMIT 6 */

    public function getLatestReviewsJoinMovie(){
        $em = $this->getEntityManager();
        $RAW_QUERY = "SELECT r.*,u.first_name,u.last_name,u.profile_image,m.*,count(l.id) as broj FROM `review`r LEFT JOIN user u on r.user=u.id LEFT JOIN movie m on r.movie=m.id LEFT JOIN like2 l on r.id=l.review_id WHERE is_gif=false GROUP BY r.id ORDER BY r.created_at DESC LIMIT 6 ";
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        /*var_dump($result);
        exit;*/

        return $result;
    }
    /*getLatestReviewsJoinMovieJoinLikes*/
/*SELECT r.*,u.first_name,u.last_name,m.*,count(l.id) FROM `review`r LEFT JOIN user u on r.user=u.id LEFT JOIN movie m on r.movie=m.id LEFT JOIN like2 l on r.id=l.review_id WHERE is_gif=false GROUP BY r.id ORDER BY created_at DESC LIMIT 6 */
}