<?php


namespace AppBundle\Controller\Movie;


use AppBundle\AppBundle;
use AppBundle\Component\HttpFoundation\ResponseI;
use AppBundle\Controller\CommonController;
use AppBundle\Controller\Movie\Constants\Constants as Constants;
use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\ChatPrivate;
use AppBundle\Entity\Friends;
use AppBundle\Entity\LatestNews;
use AppBundle\Entity\Like2;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Notifications;
use AppBundle\Entity\Recommend;
use AppBundle\Entity\Review;
use AppBundle\Entity\User;
use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use AppBundle\Utils\Helper;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pusher;
use AppBundle\Controller\Constants\API_Credentials;


class MovieController extends CommonController
{

    /**
     * @var ObjectManager | Object
     */
    private $entityManager;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $movieId;

    /**
     * @var Pusher\Pusher
     */
    private $pusher;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @Route("/movies/{movieId}", name="moviePage")
     * @param Request $request
     * @param $movieId
     * @return ResponseI
     * @throws Exception
     */
    public function movieAction(Request $request, $movieId){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        $this->movieId = (int)$movieId;
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->userId = $this->getUser()->getId();
        $this->setUnreadMovieNotificationsAsRead();
        $this->helper = new Helper();
        $this->pusher = $this->getPusherInstance();
        if($request->isXmlHttpRequest()){
            $result = $this->handleAjax($request);
            return $result;
        }
        $movieJSON = file_get_contents("https://api.themoviedb.org/3/movie/{$this->movieId}?api_key="
            . API_Credentials::API_KEY);
        $movieObj = json_decode($movieJSON,true);
        $reviewsData = $this->entityManager->getRepository(Review::class)->getReviewsInformation(
            $this->userId, $this->movieId);
        $lists = $this->entityManager->getRepository(UsersList::class)->findBy(['user'=>$this->userId]);
        $friend_requests = $this->entityManager->getRepository(Friends::class)->getFriendRequests($this->userId);
        $chats = $this->entityManager->getRepository(ChatPrivate::class)->getAllChats($this->userId);
        $unread_msgs = $this->entityManager->getRepository(ChatMessage::class)->findBy(
            ['received_by' => $this->userId, 'is_read' => false]);
        $notifications = $this->entityManager->getRepository(Notifications::class)->getAllNotifications($this->userId);
        return $this->render('default/moviePage.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'info' => $movieObj,
            'lists'=>$lists,
            'reviewsData' => $reviewsData,
            'friend_requests'=>$friend_requests,
            'chats'=>$chats,
            'unread_msgs'=>$unread_msgs,
            'notifications'=>$notifications
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    private function handleAjax(Request $request) {
        switch ($request->request->get('methodName')) {
            case Constants::ADD_MOVIE_REVIEW:
                return $this->addMovieReview($request->request->get('movieData'));
            case Constants::ADD_TO_PERSONAL_LIST:
                return $this->addToPersonalList($request->request->get('movieData'));
            case Constants::ADD_GIF_REACTION:
                return $this->addGifReaction($request->request->get('movieData'));
            case Constants::SEARCH_GIFS:
                return $this->searchGifs($request->request->get('gifData'));
            case Constants::SEND_MOVIE_RECOMMENDATION:
                return $this->sendMovieRecommendation($request->request->get('recommendationData'));
            case Constants::LIKE_REVIEW:
                return $this->likeReview($request->request->get('reviewData'));
            default:
                return new JsonResponse('nebojsa : default :  ajax call or smothnig');
        }
    }

    /**
     * @param $reviewData
     * @return JsonResponse
     * @throws Exception
     */
    private function likeReview($reviewData) {
        $reviewObj = $this->helper->transformArrayToObject($reviewData);
        $postoji = $this->entityManager->getRepository(Like2::class)->findOneBy([
            'user' => $reviewObj->user, 'review_id' => $reviewObj->reviewid]);
        if(!$postoji){
            $like = new Like2(new \DateTime(), false, true, 0, 0, $reviewObj->user,
                $reviewObj->reviewid, 'public');
            $reviewer = $this->entityManager->getRepository(Review::class)->findOneBy([
                'id'=>$reviewObj->reviewid
            ]);
            $reviewer_id = $reviewer->getUser();
            $notification = new Notifications(false, $this->userId, true, false,
                false, $reviewObj->reviewid, 0, $this->movieId, $reviewer_id, $reviewObj->title);
            $data['action_performer']=$this->getUser()->getId();
            $data['first_name']=$this->getUser()->getFirstName();
            $data['last_name']=$this->getUser()->getLastName();
            $data['title']=$reviewObj->title;
            $data['movie']=(int)$this->movieId;
            $data['profile_image']=$this->getUser()->getProfileImage();
            $this->pusher->trigger('private-notifications-'.$reviewer_id,'on_new_like',$data);

            $latestNews = new LatestNews($this->userId, true, false, false, $reviewObj->reviewid,
            0, $this->movieId, new \DateTime());
            $this->entityManager->persist($latestNews);
            $this->entityManager->persist($notification);
            $this->entityManager->persist($like);
            $this->entityManager->flush();
        }
        return new JsonResponse('Success');
    }

    /**
     * @param $recommendationData array
     * @return JsonResponse
     * @throws Exception
     */
    private function sendMovieRecommendation($recommendationData) {
        $responseText = 'Exists';
        $recommendationObj = $this->helper->transformArrayToObject($recommendationData);
        $existsInDB = $this->entityManager->getRepository(Recommend::class)->findOneBy([
            'movie' => $this->movieId, 'received_by'=> $recommendationObj->received_by, 'sent_by'=>$this->userId]);
        if(!$existsInDB){
            $recommendation = new Recommend($recommendationObj->received_by, $this->userId,
                false, new \DateTime(), $this->movieId, $recommendationObj->title);
            $exists = $this->entityManager->getRepository(Movie::class)->find($this->movieId);
            if(!$exists){
                $movie=new Movie($this->movieId, $recommendationObj->title, $recommendationObj->poster_path,
                    $recommendationObj->movie__overview, $recommendationObj->movie__genres,
                    $recommendationObj->movie__vote, $recommendationObj->movie__backdrop_path);
                $this->entityManager->persist($movie);
            }
            $notification = new Notifications(false, $this->userId, false, false, true,
            0, 0, $this->movieId, $recommendationObj->received_by, $recommendationObj->title);
            $data['first_name']=$this->getUser()->getFirstName();
            $data['last_name']=$this->getUser()->getLastName();
            $data['sent_by']=$this->getUser()->getId();
            $data['movie']=$this->movieId;
            $data['title']=$recommendationObj->title;
            $data['profile_image']=$this->getUser()->getProfileImage();
            $this->pusher->trigger(['private-notifications-' . $recommendationObj->received_by],
                'on_new_recommendation', $data);
            $this->entityManager->persist($notification);
            $this->entityManager->persist($recommendation);
            $this->entityManager->flush();
            $responseText = 'Success';
        }
        return new JsonResponse($responseText);
    }

    /**
     * @param $gifData array
     * @return JsonResponse
     */
    private function searchGifs($gifData) {
        $gifObj = $this->helper->transformArrayToObject($gifData);
        $queryStringGIF = $gifObj->queryStringGIF;
        $resultJSON = file_get_contents(
            "https://api.tenor.com/v1/search?key=E2RZR6WJTMEC&q=$queryStringGIF&limit=30");
        return new JsonResponse($resultJSON);
    }

    /**
     * @param $movieData array
     * @return JsonResponse
     * @throws Exception
     */
    private function addGifReaction($movieData) {
        $movieObj = $this->helper->transformArrayToObject($movieData);
        $review = new Review($this->userId, $movieObj->movie__id, "", new \DateTime(),
            UsersList::LIST_STATE_PUBLIC, true, $movieObj->gifurl);
        return $this->handleSubmittedReview($movieObj, $review);
    }

    /**
     * @param $movieDataArray array
     * @return JsonResponse
     * @throws Exception
     */
    private function addMovieReview($movieDataArray) {
        $movieObj = $this->helper->transformArrayToObject($movieDataArray);
        $review = new Review($this->userId, $this->movieId, $movieObj->review, new \DateTime(),
            UsersList::LIST_STATE_PUBLIC, false, '');
        return $this->handleSubmittedReview($movieObj, $review);
    }

    private function handleSubmittedReview($movieObj, $review) {
        $movieInDB = $this->entityManager->getRepository(Movie::class)->find($movieObj->movie__id);
        if (!$movieInDB) {
            $movie = new Movie($movieObj->movie__id, $movieObj->movie__title, $movieObj->movie__poster_path,
                $movieObj->movie__overview, $movieObj->movie__genres, $movieObj->movie__vote_average,
                $movieObj->movie__backdrop_path);
            $this->entityManager->persist($movie);
        }
        $maxID = $this->entityManager->getRepository(Review::class)->getMaxID();
        $maxID = $maxID[0]['maxID'] + 1;
        $notification = new Notifications(false, $this->userId, false, true, false,
            $maxID, 0, $this->movieId, 0, $movieObj->title);
        $this->triggerNotification($movieObj, $notification);
        $latestNews = new LatestNews($this->userId, false, true, false, $maxID, 0,
            $this->movieId, new \DateTime());
        $this->entityManager->persist($latestNews);
        $this->entityManager->persist($notification);
        $this->entityManager->persist($review);
        $this->entityManager->flush();
        $today = date("M j, Y");                 // March 10, 2001
        return new JsonResponse(json_encode(['review_id' => $maxID, 'review_date' => $today]));
    }

    /**
     * Set all movie related unread notifications as read
     */
    private function setUnreadMovieNotificationsAsRead() {
        $unreadMovieNotifications = $this->entityManager->getRepository(Notifications::class)->findBy([
            'received_by' => $this->userId, 'is_read' => false, 'movie' => $this->movieId]);
        foreach ($unreadMovieNotifications as $notification){
            /** @var Notifications $notification  */
            $notificationToSetToRead = new Notifications($notification->getId(), $notification->getIsRead(),
                $notification->getActionPerformer(), $notification->getIsLike(), $notification->getIsReview(),
                $notification->getIsRecommend(), $notification->getReview(), $notification->getList(),
                $notification->getMovie(), $notification->getTitle());
            $this->entityManager->persist($notificationToSetToRead);
        }
        $this->entityManager->flush();
    }

    /**
     * @param $movieData
     * @param Notifications $notification
     * @throws Pusher\PusherException
     */
    private function triggerNotification($movieData, $notification) {
        $movieReviews = $this->entityManager->getRepository(Review::class)->getAllWhoReviewed();
        if ($movieReviews != null) {
            $channels = [];
            foreach ($movieReviews as $movieReview) {
                if ($movieReview['movie']==$this->movieId && $movieReview['user']!=$this->userId) {
                    array_push($channels,'private-notifications-' . $movieReview['user']);
                    $newNotification = new Notifications($notification->getIsRead(), $this->userId,
                    $notification->getIsLike(), $notification->getIsReview(), $notification->getIsRecommend(),
                    $notification->getReview(), $notification->getList(), $notification->getMovie(), $movieReview['user'],
                    $notification->getTitle());
                    $this->entityManager->persist($newNotification);
                }
            }
            $data = [
                'action_performer' => $this->userId,
                'first_name' => $this->getUser()->getFirstName(),
                'last_name' => $this->getUser()->getLastName(),
                'title' => $movieData->title,
                'movie' => $this->movieId,
                'profile_image' => $this->getUser()->getProfileImage()
            ];
            $this->pusher->trigger($channels, 'on_new_review', $data);
        }
    }

}