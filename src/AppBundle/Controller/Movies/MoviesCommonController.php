<?php


namespace AppBundle\Controller\Movies;


use AppBundle\Component\HttpFoundation\JsonResponse;
use AppBundle\Component\HttpFoundation\RedirectResponse;
use AppBundle\Component\HttpFoundation\ResponseI;
use AppBundle\Controller\CommonController;
use AppBundle\Entity\LatestNews;
use AppBundle\Entity\Movie;
use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use AppBundle\Pusher\Pusher;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Psr\Container\ContainerInterface;
use Pusher\PusherException;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;

class MoviesCommonController extends CommonController
{

    /**
     * @var ObjectManager
     */
    protected $entityManager;


    /**
     * @var Pusher
     */
    protected $pusher;

    /**
     * MoviesCommonController constructor.
     * @param ObjectManager $entityManager
     * @param ContainerInterface $container
     * @throws PusherException
     */
    public function __construct(
        ObjectManager $entityManager,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->pusher = $this->getPusherInstance();
    }

    /**
     * @param $movieData
     * @return JsonResponse
     * @throws Exception
     * @throws Exception
     */
    protected function addToPersonalList($movieData) {
        $listID = $movieData['list'];
        $tmdbID = $movieData['tmdbid'];
        $movieId = $movieData['movie__id'];
        $userId = $movieData['user'];
        $movieVoteAverage = $movieData['movie__vote_average'];
        $movieBackdropPath = $movieData['movie__backdrop_path'];
        $movieOverview = mb_strimwidth($movieData['movie__overview'],0,254,'utf-8');
        $movieGenres = $movieData['movie__genres'];
        $moviePosterPath = $movieData['movie__poster_path'];
        $movieTitle = $movieData['movie__title'];
        $movieInDB = $this->entityManager->getRepository(Movie::class)->find($movieId);
        if(!$movieInDB){
            $movie = new Movie($movieId, $movieTitle, $moviePosterPath, $movieOverview, $movieGenres, $movieVoteAverage, $movieBackdropPath);
            $this->entityManager->persist($movie);
        }
        $listItemMaxID = $this->entityManager->getRepository(UsersListItem::class)->getMaxID($listID);
        $movieInPersonalList = $this->entityManager->getRepository(UsersListItem::class)->alreadyInList($userId,$listID,$tmdbID);
        if(!$movieInPersonalList){
            $listItemID = $listItemMaxID[0]['maxID'] + 1;
            $item = new UsersListItem($listID, $listItemID, $tmdbID, $movieTitle);
            $privacy = $this->entityManager->getRepository(UsersList::class)->find($listID);
            if($privacy->getIsPrivate() == false){
                $latestNews = new LatestNews($userId, false, false,  true, false, $listID, $tmdbID, new \DateTime());
                $this->entityManager->persist($latestNews);
            }
            $this->entityManager->persist($item);
        }
        $this->entityManager->flush();
        return new JsonResponse('Success');
    }

}