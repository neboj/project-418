<?php


namespace AppBundle\Controller;

use AppBundle\Component\HttpFoundation\Response;
use AppBundle\Component\HttpFoundation\ResponseI;
use AppBundle\Controller\Constants\API_Credentials;
use AppBundle\Controller\Constants\Pusher\Pusher_Credentials;
use AppBundle\Entity\LatestNews;
use AppBundle\Entity\Movie;
use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pusher;
use AppBundle\Component\HttpFoundation\JsonResponse;
use AppBundle\Component\HttpFoundation\RedirectResponse;

class CommonController extends Controller
{
    /**
     * Renders a view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @param \Symfony\Component\HttpFoundation\Response $response A response instance
     *
     * @return ResponseI
     */
    protected function render($view, array $parameters = array(), \Symfony\Component\HttpFoundation\Response $response = null) {
        $response = parent::render($view, $parameters, $response);
        return new Response($response->getContent());
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route The name of the route
     * @param array $parameters An array of parameters
     * @param int $status The status code to use for the Response
     *
     * @return ResponseI
     */
    protected function redirectToRoute($route, array $parameters = array(), $status = 302): ResponseI
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * @param string $url
     * @param int $status
     * @return ResponseI
     */
    protected function redirect($url, $status = 302): ResponseI
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @return Pusher\Pusher
     * @throws Pusher\PusherException
     */
    protected function getPusherInstance() {
        $options = array(
            'cluster' => 'eu',
            'encrypted' => true
        );
        return $pusher = new Pusher\Pusher(
            Pusher_Credentials::AUTH_KEY,
            Pusher_Credentials::SECRET_KEY,
            Pusher_Credentials::APP_ID,
            $options
        );
    }

    /**
     * @param $movieData
     * @return JsonResponse
     * @throws Exception
     */
    protected function addToPersonalList($movieData) {
        $em = $this->getDoctrine()->getManager();
        $userId = $this->getUser()->getId();
        $listID = $movieData['list'];
        $tmdbID = $movieData['tmdbid'];
        $movieId = $movieData['movie__id'];
        $movieVoteAverage = $movieData['movie__vote_average'];
        $movieBackdropPath = $movieData['movie__backdrop_path'];
        $movieOverview = mb_strimwidth($movieData['movie__overview'],0,254,'utf-8');
        $movieGenres = $movieData['movie__genres'];
        $moviePosterPath = $movieData['movie__poster_path'];
        $movieTitle = $movieData['movie__title'];
        $movieInDB = $em->getRepository(Movie::class)->find($movieId);
        if(!$movieInDB){
            $movie = new Movie($movieId, $movieTitle, $moviePosterPath, $movieOverview, $movieGenres, $movieVoteAverage, $movieBackdropPath);
            $em->persist($movie);
        }
        $listItemMaxID = $this->getDoctrine()->getRepository(UsersListItem::class)->getMaxID($listID);
        $movieInPersonalList = $this->getDoctrine()->getRepository(UsersListItem::class)->alreadyInList($userId,$listID,$tmdbID);
        if(!$movieInPersonalList){
            $listItemID = $listItemMaxID[0]['maxID'] + 1;
            $item = new UsersListItem($listID, $listItemID, $tmdbID, $movieTitle);
            $privacy = $em->getRepository(UsersList::class)->find($listID);
            if($privacy->getIsPrivate() == false){
                $latestNews = new LatestNews($userId, false, false,  true, false, $listID, $tmdbID, new \DateTime());
                $em->persist($latestNews);
            }
            $em->persist($item);
        }
        $em->flush();
        return new JsonResponse('Success');
    }
}