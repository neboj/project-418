<?php
namespace AppBundle\Controller\Movies;

use AppBundle\Controller\CommonController;
use AppBundle\Controller\Movies\Constants\Constants as Constants;
use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\ChatPrivate;
use AppBundle\Entity\Friends;
use AppBundle\Entity\LatestNews;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Notifications;
use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\Constants\API_Credentials;


class MoviesController extends CommonController {

    /**
     * @var ObjectManager | Object
     */
    private $entityManager;

    /**
     * @var int
     */
    private $userId;

    /**
     * @Route("/movies",name="movies")
     * @param Request $request
     * @return JsonResponse|RedirectResponse|Response
     * @throws Exception
     */
    public function moviesAction(Request $request){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->userId = $this->getUser()->getId();
        if($request->isXmlHttpRequest()){
            return $this->handleAjax($request);
        }
        $popularMoviesJSON = file_get_contents(
            "https://api.themoviedb.org/3/movie/popular?api_key=" . API_Credentials::API_KEY);
        $popularMovies = json_decode($popularMoviesJSON,true);
        $usersMovieLists = $this->entityManager->getRepository(UsersList::class)->findBy(
            ['user' => $this->userId]);
        $friend_requests = $this->entityManager->getRepository(Friends::class)->getFriendRequests(
            $this->userId);
        $chats = $this->entityManager->getRepository(ChatPrivate::class)->getAllChats(
            $this->getUser()->getId());
        $unread_msgs = $this->entityManager->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->userId,'is_read'=>false]);
        $notifications = $this->entityManager->getRepository(Notifications::class)->getAllNotifications($this->userId);
        return $this->render('default/moviesDark.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'info' => $popularMovies,
            'lists' => $usersMovieLists,
            'friend_requests' => $friend_requests,
            'chats' => $chats,
            'unread_msgs' => $unread_msgs,
            'notifications' => $notifications
        ]);


    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    private function handleAjax(Request $request) {
        switch ($request->request->get('methodName')) {
            case Constants::MOVIES_SEARCH_BY_STRING:
                return $this->searchMovieByString(
                    $request->request->get('movieName'),
                    $request->request->get('page'));
            case Constants::NEXT_PAGE:
                return $this->nextPage($request->request->get('page'));
            case Constants::ADD_TO_PERSONAL_LIST:
                return $this->addToPersonalList($request->request->get('movieData'));
            default:
                return new JsonResponse('nebojsa : default :  ajax call or smothnig');
        }
    }

    /**
     * @param $movieName
     * @param $pageNumber
     * @return JsonResponse
     */
    private function searchMovieByString($movieName, $pageNumber) {
        $searchMovieByString = $movieName;
        $searchMovieByString = preg_replace('/\s+/', '+', $searchMovieByString);
        $searchResultJSON = file_get_contents(
            "https://api.themoviedb.org/3/search/movie?query=$searchMovieByString&api_key=" . API_Credentials::API_KEY . "&language=en-US&page=" . ($pageNumber+1));
        return new JsonResponse($searchResultJSON);
    }

    /**
     * @param $pageNumber int|null
     * @return JsonResponse
     */
    private function nextPage($pageNumber) {
        $popularMoviesJSON = file_get_contents("https://api.themoviedb.org/3/movie/popular?api_key=" . API_Credentials::API_KEY . "&page=" . ($pageNumber + 1));
        return new JsonResponse($popularMoviesJSON);
    }

}