<?php


namespace AppBundle\Controller\Movie;


use AppBundle\AppBundle;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return JsonResponse|RedirectResponse|Response
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
        $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/{$this->movieId}?api_key="
            . API_Credentials::API_KEY);
        $jsonObject = json_decode($jsonContent,true);

        // @TODO make this one DB call
        $reviews = $this->entityManager->getRepository(Review::class)->findBy(['movie'=>$this->movieId]);
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $revlikes = $this->entityManager->getRepository(Like2::class)->getLikesOfReviews($this->movieId);



        $lists = $this->entityManager->getRepository(UsersList::class)->findBy(['user'=>$this->userId]);
        $friend_requests = $this->entityManager->getRepository(Friends::class)->getFriendRequests($this->userId);
        $chats = $this->entityManager->getRepository(ChatPrivate::class)->getAllChats($this->userId);
        $unread_msgs = $this->entityManager->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->userId,'is_read'=>false]);

        $notifications = $this->entityManager->getRepository(Notifications::class)->getAllNotifications($this->userId);
        /* AJAX ZAHTEVI*/
        if($request->isXmlHttpRequest()){

            $result = $this->handleAjax($request);
            return $result;

            /*send recommend movie*/
            if($request->request->get('movie_id_to_recommend')!=null){
                $exists = $this->entityManager->getRepository(Recommend::class)->findOneBy([
                    'movie'=>$request->request->get('movie_id_to_recommend'),
                    'received_by'=>$request->request->get('received_by'),
                    'sent_by'=>$this->userId]);
                if(!$exists){
                    $rec = new Recommend();
                    $rec->setReceivedBy($request->request->get('received_by'));
                    $rec->setSentBy($this->getUser()->getId());
                    $rec->setMovie($request->request->get('movie_id_to_recommend'));
                    $rec->setIsSeen(false);
                    $rec->setTitle($request->request->get('title'));
                    $rec->setCreatedAt(new \DateTime());

                    /*implementiraj i onda menjaj na homepage,moviePage i po kontrolerima...ludilo...*/
                    /*najvise kosta na homepage,mozda samo tu,jer ima foreach petlju*/
                    $exists = $this->entityManager->getRepository(Movie::class)->find($request->request->get('movie__id'));
                    if(!$exists){
                        $movie=new Movie();
                        $movie->setId($request->request->get('movie__id'));
                        $movie->setTitle($request->request->get('movie__title'));
                        $movie->setPosterPath($request->request->get('movie__poster_path'));
                        $movie->setVoteAverage($request->request->get('movie__vote_average'));
                        $movie->setOverview( $request->request->get('movie__overview'));
                        $movie->setGenres($request->request->get('movie__genres'));
                        $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                        $this->entityManager->persist($movie);
                        $this->entityManager->flush();
                    }

                    $notif = new Notifications();
                    $notif->setIsRead(false);
                    $notif->setActionPerformer($this->getUser()->getId());
                    $notif->setIsLike(false);
                    $notif->setIsReview(false);
                    $notif->setIsRecommend(true);
                    $notif->setReview('');
                    $notif->setList(0);
                    $notif->setMovie($this->movieId);
                    $notif->setTitle($request->request->get('title'));
                    $notif->setReceivedBy($request->request->get('received_by'));


                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    $data['sent_by']=$this->getUser()->getId();
                    $data['movie']=$request->request->get('movie_id_to_recommend');
                    $data['title']=$request->request->get('title');
                    $data['profile_image']=$this->getUser()->getProfileImage();
                    $this->pusher->trigger(['private-notifications-'.$request->request->get('received_by')],'on_new_recommendation',$data);

                    $this->entityManager->persist($notif);
                    $this->entityManager->persist($rec);
                    $this->entityManager->flush();


                    return new JsonResponse('ok');
                }

                return new JsonResponse('exists');
            }


            /*GIF REACTION*/
            if($request->request->get('user')!=null & $request->request->get('gifurl')!=null){
                $gifurl = $request->request->get('gifurl');

                $user=$request->request->get('user');

                $review = new Review();
                $review->setUser($user);
                $review->setMovie($this->movieId);
                $review->setIsGif(true);
                $review->setReviewTxt("");
                $review->setGifUrl($gifurl);
                $review->setState('public');
                $review->setCreatedAt(new \DateTime());

                $postoji = $this->entityManager->getRepository(Movie::class)->find($request->request->get('movie__id'));
                if(!$postoji){
                    $movie=new Movie();
                    $movie->setId($request->request->get('movie__id'));
                    $movie->setTitle($request->request->get('movie__title'));
                    $movie->setPosterPath($request->request->get('movie__poster_path'));
                    $movie->setVoteAverage($request->request->get('movie__vote_average'));
                    $movie->setOverview( $request->request->get('movie__overview'));
                    $movie->setGenres($request->request->get('movie__genres'));
                    $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                    $this->entityManager->persist($movie);
                    $this->entityManager->flush();
                }


                $notif = new Notifications();
                $notif->setIsRead(false);
                $notif->setActionPerformer($this->getUser()->getId());
                $notif->setIsLike(false);
                $notif->setIsReview(true);
                $notif->setIsRecommend(false);
                $max = $this->entityManager->getRepository(Review::class)->getMaxID();
                $max = $max[0]['maxi'];
                $max = $max+1;
                $notif->setReview($max);
                $notif->setList(0);
                $notif->setMovie($this->movieId);
                $notif->setTitle($request->request->get('title'));
                $notif->setReceivedBy(0);

                $notif_receivers = $this->entityManager->getRepository(Review::class)->getAllWhoReviewed();
                $kanali = array();

                if($notif_receivers!=null){

                    foreach ($notif_receivers as $k){
                        if($k['movie']==$this->movieId && $k['user']!=$this->getUser()->getId()){
                            array_push($kanali,'private-notifications-'.$k['user']);

                            $notif->setReceivedBy($k['user']);

                            $this->entityManager->persist($notif);
                        }
                    }

                    $data['action_performer']=$this->getUser()->getId();
                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    $data['title']=$request->request->get('title');
                    $data['movie']=$this->movieId;
                    $data['profile_image']=$this->getUser()->getProfileImage();
                    $this->pusher->trigger($kanali,'on_new_review',$data);


                }






                $latestNews = new LatestNews();
                $latestNews->setIsLike(false);
                $latestNews->setCreatedAt(new\DateTime());
                $latestNews->setMovie($this->movieId);
                $latestNews->setReview($max);
                $latestNews->setActionPerformer($user);
                $latestNews->setIsReview(true);
                $latestNews->setIsAdd(false);
                $latestNews->setList(0);

                $this->entityManager->persist($latestNews);



                $this->entityManager->persist($review);
                $this->entityManager->flush();

                $reviews = $this->entityManager->getRepository(Review::class)->findAll();
                $users = $this->entityManager->getRepository(User::class)->findAll();

                $normalizer = new ObjectNormalizer();
                $encoder = new JsonEncoder();
                $serializer = new Serializer(array($normalizer), array($encoder));
                $revs=$serializer->serialize($reviews, 'json');
                $usrs=$serializer->serialize($users,'json');


                $kaka =$serializer->serialize($kanali,'json');
                $revlikes1 = $this->entityManager->getRepository(Like2::class)->getLikesOfReviews($this->movieId);


                $reviewLikesObj=$serializer->serialize($revlikes1, 'json');

                $final = '{"kaka":'.$kaka .',"kor":'.$usrs .',"lik":'.$reviewLikesObj.',"kom":'. $revs. '}';
                return new JsonResponse($final);
            }
            /*search gifs on TENOR*/
            if($request->request->get('podacigif')!=null){
                /*https://api.tenor.com/v1/search?key=E2RZR6WJTMEC&q=goodluck&limit=40*/
                $podacigif=  $request->request->get('podacigif');
                $jsonContent = file_get_contents("https://api.tenor.com/v1/search?key=E2RZR6WJTMEC&q=$podacigif&limit=30");
                $jsonObject = json_decode($jsonContent,true);

                return new JsonResponse($jsonContent);
            }
            /*LIKE REVIEW*/
            if($request->request->get('user')!=null & $request->request->get('reviewid')!=null){
                $user=$request->request->get('user');
                $reviewid=$request->request->get('reviewid');

                $postoji = $this->entityManager->getRepository(Like2::class)->findOneBy([
                    'user'=>$user,
                    'review_id'=>$reviewid
                ]);

                if(!$postoji){
                    $like = new Like2();
                    $like->setPost(0);
                    $like->setComment(0);
                    $like->setState('public');
                    $datum = new\DateTime();
                    $like->setCreatedAt($datum);
                    $like->setUser((int)$user);
                    $like->setReviewId((int)$reviewid);
                    $like->setIsReview(true);
                    $like->setIsComment(false);

                    $notif = new Notifications();
                    $notif->setIsRead(false);
                    $notif->setActionPerformer($this->getUser()->getId());
                    $notif->setIsLike(true);
                    $notif->setIsReview(false);
                    $notif->setIsRecommend(false);
                    $notif->setReview((int)$reviewid);
                    $notif->setList(0);
                    $notif->setMovie($this->movieId);
                    $notif->setTitle($request->request->get('title'));

                    $rev = $this->entityManager->getRepository(Review::class)->findOneBy([
                        'id'=>$reviewid
                    ]);
                    $reviewer_id = $rev->getUser();

                    $notif->setReceivedBy($reviewer_id);

                    $data['action_performer']=$this->getUser()->getId();
                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    $data['title']=$request->request->get('title');
                    $data['movie']=(int)$reviewid;
                    $data['profile_image']=$this->getUser()->getProfileImage();
                    $this->pusher->trigger('private-notifications-'.$reviewer_id,'on_new_like',$data);


                    $latestNews = new LatestNews();
                    $latestNews->setIsLike(true);
                    $latestNews->setCreatedAt(new\DateTime());
                    $latestNews->setMovie($this->movieId);
                    $latestNews->setReview($reviewid);
                    $latestNews->setActionPerformer($user);
                    $latestNews->setIsReview(false);
                    $latestNews->setIsAdd(false);
                    $latestNews->setList(0);

                    $this->entityManager->persist($latestNews);

                    $this->entityManager->persist($notif);

                    $this->entityManager->persist($like);
                    $this->entityManager->flush();
                    return new Response('ok');
                }

                $normalizer = new ObjectNormalizer();
                $encoder = new JsonEncoder();
                $serializer = new Serializer(array($normalizer), array($encoder));
                $response=$serializer->serialize($postoji, 'json');
                return new Response($response);
            }

        }
        return $this->render('default/moviePage.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'info' => $jsonObject,
            'lists'=>$lists,
            'reviews'=>$reviews,
            'users'=>$users,
            'likes'=>$revlikes,
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
            default:
                return new JsonResponse('nebojsa : default :  ajax call or smothnig');
        }
    }

    /**
     * @param $movieDataArray array
     * @return JsonResponse
     * @throws Exception
     */
    private function addMovieReview($movieDataArray) {
        $movieData = $this->helper->transformArrayToObject($movieDataArray);
        $movieInDB = $this->entityManager->getRepository(Movie::class)->find($movieData->movie__id);
        if (!$movieInDB) {
            $movie = new Movie($movieData->movie__id, $movieData->movie__title, $movieData->movie__poster_path,
            $movieData->movie__overview, $movieData->movie__genres, $movieData->movie__vote_average,
            $movieData->movie__backdrop_path);
            $this->entityManager->persist($movie);
        }
        $review = new Review($this->userId, $this->movieId, $movieData->review, new \DateTime(), 'public',
            false, '');
        $maxID = $this->entityManager->getRepository(Review::class)->getMaxID();
        $maxID = $maxID[0]['maxID'] + 1;
        $notification = new Notifications(false, $this->userId, false, true, false,
            $maxID, 0, $this->movieId, 0, $movieData->title);
        $this->triggerNotification($movieData, $notification);
        $latestNews = new LatestNews($this->userId, false, true, false, $maxID, 0,
            $this->movieId, new \DateTime());
        $this->entityManager->persist($latestNews);
        $this->entityManager->persist($notification);
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        // @TODO redo this completely
        $reviews = $this->entityManager->getRepository(Review::class)->findAll();
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $revs=$serializer->serialize($reviews, 'json');
        $usrs=$serializer->serialize($users,'json');
        $revlikes1 = $this->entityManager->getRepository(Like2::class)->getLikesOfReviews($this->movieId);
        $reviewLikesObj=$serializer->serialize($revlikes1, 'json');
        $final = '{"kor":'.$usrs .',"lik":'.$reviewLikesObj.',"kom":'. $revs. '}';
        return new JsonResponse($final);
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