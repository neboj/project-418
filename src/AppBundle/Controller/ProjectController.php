<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
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
use AppBundle\Form\PostType;
use AppBundle\Form\UserType;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use AppBundle\Form\PostFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\DateTime;
use Pusher;


class ProjectController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }



        $jsonContentTopRated = file_get_contents("https://api.themoviedb.org/3/movie/top_rated?api_key=831c33c0ee756b98159b05350405d661");
        $jsonObjectTopRated = json_decode($jsonContentTopRated,true);

        $em = $this->getDoctrine()->getManager();

 /*izlistaj latest reviews*/
        $max = $em->getRepository(Review::class)->getMaxID();
        $max = $max[0]['maxi'];
        $offset = (int)$max - 11-6;

        /*$reviews = $em->getRepository(Review::class)->findBy(array('is_gif'=>0),array('created_at'=>'DESC'));*/
        $reviewsJoinMovie= $em->getRepository(Review::class)->getLatestReviewsJoinMovie();

        $users = $em->getRepository(User::class)->findAll();
/*        $revlikes='';*/
        /*$films = array();
        foreach ($reviews as $r){
            $f = $r->getMovie();
            $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/$f?api_key=831c33c0ee756b98159b05350405d661");
            $jsonObject = json_decode($jsonContent,true);

            $revlikes = $em->getRepository(Like2::class)->getLikesOfReviews($f);


            array_push($films,$jsonObject);
        }*/


  /*izlistaj latest activity*/
        $news = $em->getRepository(LatestNews::class)->getLatestNews();
        $filmsLatestActivityJoinMovieEntity =$em->getRepository(LatestNews::class)->getLatestNewsJoinMovieEntity();
        /*$filmsLatestActivity=array();
        foreach ($news as $n){
            $f = $n['movie'];
            $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/$f?api_key=831c33c0ee756b98159b05350405d661");
            $jsonObject = json_decode($jsonContent,true);
            array_push($filmsLatestActivity,$jsonObject);
        }*/
        /*$a = $news[0]['movie'];*/

        /*izlistaj sve ljude*/
        $people = $em->getRepository(User::class)->findAll();
        /*izlistaj frend requests*/
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        /*izlistaj chatove*/
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        /*izlistaj neprocitane poruke gde je receiver=current user*/
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);

    /*izlistaj recommendations */
        $recommendations = $em->getRepository(Recommend::class)->getAllRecommendations($this->getUser()->getId());

    /*izlistaj notifications*/
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        return $this->render(':default:homepage.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
/*            'reviews'=>$reviews,*/
            'users'=>$users,
/*            'films'=>$films,*/
/*            'likes'=>$revlikes,*/
            'news'=>$news,
/*            'filmsAct'=>$filmsLatestActivity,*/
            'top_rated'=>$jsonObjectTopRated,
            'friend_requests'=>$friend_requests,
            'chats'=>$chats,
            'unread_msgs'=>$unread_msgs,
            'people'=>$people,
            'recommendations'=>$recommendations,
            'notifications'=>$notifications,
            'filmsActJoinMovie'=>$filmsLatestActivityJoinMovieEntity,
            'reviewsJoinMovie'=>$reviewsJoinMovie
        ]);
    }

    /**
     * @Route("/movies",name="movies")
     */
    public function moviesAction(Request $request){


        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }


        $em = $this->getDoctrine()->getManager();
        /*izlistaj frend requests*/
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());

        $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/popular?api_key=831c33c0ee756b98159b05350405d661");
        $jsonObject = json_decode($jsonContent,true);

        $lists = $em->getRepository(UsersList::class)->findBy([
           'user'=>$this->getUser()->getId()
        ]);

        /*izlistaj chatove*/
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        /*izlistaj neprocitane poruke gde je receiver=current user*/
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);

        /*izlistaj notifications*/
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        if($request->isXmlHttpRequest()){
            if($request->request->get('foo')==null){
                if($request->request->get('page')==null){

                }else{
                    $page = $request->request->get('page');
                    $page = $page+1;
                    $jContent = file_get_contents("https://api.themoviedb.org/3/movie/popular?api_key=831c33c0ee756b98159b05350405d661&page=$page");

                }
            }else{
                $ajax = $request->request->get('foo');
                $ajax = preg_replace('/\s+/','+',$ajax);

                if($request->request->get('foo')==''){//foo=''
                    if($request->request->get('page')==null){//page=null
                        $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/popular?api_key=831c33c0ee756b98159b05350405d661");
                    }else{//page=2
                        $page = $request->request->get('page');
                        $page = $page+1;
                        $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/popular?api_key=831c33c0ee756b98159b05350405d661&page=$page");
                    }
                }else{ //foo = 'asdf'
                    if($request->request->get('page')==null){//page=null
                        $jContent = file_get_contents("https://api.themoviedb.org/3/search/movie?query=$ajax&api_key=831c33c0ee756b98159b05350405d661&language=en-US&page=1");
                    }else{//page=2
                        $page = $request->request->get('page');
                        $page = $page+1;
                        $jContent = file_get_contents("https://api.themoviedb.org/3/search/movie?query=$ajax&api_key=831c33c0ee756b98159b05350405d661&language=en-US&page=$page");
                    }

                }
            }

/*DODAJ U LISTU , ADD TO LIST , ADD MOVIE TO LIST*/
            if($request->request->get('user')!=null & $request->request->get('list')!=null & $request->request->get('tmdbid')!=null){
                $usrID = $request->request->get('user');
                $listID = $request->request->get('list');
                $tmdbID = $request->request->get('tmdbid');
                $title = $request->request->get('title');

                /*implementiraj i onda menjaj na homepage,moviePage i po kontrolerima...ludilo...*/
                /*najvise kosta na homepage,mozda samo tu,jer ima foreach petlju*/
                $postoji = $em->getRepository(Movie::class)->find($request->request->get('movie__id'));
                if(!$postoji){
                    $movie=new Movie();
                    $movie->setId($request->request->get('movie__id'));
                    $movie->setTitle($request->request->get('movie__title'));
                    $movie->setPosterPath($request->request->get('movie__poster_path'));
                    $movie->setVoteAverage($request->request->get('movie__vote_average'));
                    $movie->setOverview( $request->request->get('movie__overview'));
                    $movie->setGenres($request->request->get('movie__genres'));
                    $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                    $em->persist($movie);
                    $em->flush();
                }



                $listaMaxID = $this->getDoctrine()->getRepository(UsersList::class)->maxID();
                $stavkaListeMaxID = $this->getDoctrine()->getRepository(UsersListItem::class)->getMaxID($listID);

                $em = $this->getDoctrine()->getManager();
                $postoji = $this->getDoctrine()->getRepository(UsersListItem::class)->alreadyInList($usrID,$listID,$tmdbID);
                if(!$postoji){
                    $item = new UsersListItem();


                    $item->setName($title);
                    $item->setListid($listID);
                    $item->setMovie($tmdbID);
                    $item->setListitemid($stavkaListeMaxID[0]['broj']+1);

                    $privatnost = $em->getRepository(UsersList::class)->find($listID);

                    if($privatnost->getisPrivate()==false){
                        $latestNews = new LatestNews();
                        $latestNews->setIsLike(false);
                        $latestNews->setCreatedAt(new\DateTime());
                        $latestNews->setMovie($tmdbID);
                        $latestNews->setReview(0);
                        $latestNews->setActionPerformer($usrID);
                        $latestNews->setIsReview(false);
                        $latestNews->setIsAdd(true);
                        $latestNews->setList($listID);

                        $em->persist($latestNews);
                    }



                    $em->persist($item);
                    $em->flush();
                }

                /*$a=$postoji[0]->getName();
                $br = $stavkaListeMaxID[0]['broj']+1;*/

                /*return new JsonResponse($stavkaListeMaxID);*/

                /*return new Response($postoji[0]->getMovie());*/
                return new Response('sve ok');
            }

            return new JsonResponse($jContent);
        }


        return $this->render('default/moviesDark.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'info' => $jsonObject,
            'lists'=> $lists,
            'friend_requests'=>$friend_requests,
            'chats'=>$chats,
            'unread_msgs'=>$unread_msgs,
            'notifications'=>$notifications
        ]);


    }


    /**
     * @Route("/movies/{id}", name="moviePage")
     */
    public function movieAction(Request $request,$id){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }

        $options = array(
            'cluster' => 'eu',
            'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
            '82800fdb37dfd38f4722',
            'e5e2af578bbd993d3cc2',
            '457440',
            $options
        );


        $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/$id?api_key=831c33c0ee756b98159b05350405d661");
        $jsonObject = json_decode($jsonContent,true);
        $em=$this->getDoctrine()->getManager();
        $lists = $em->getRepository(UsersList::class)->findBy([
            'user'=>$this->getUser()->getId()
        ]);

        $reviews = $em->getRepository(Review::class)->findBy([
            'movie'=>$id
        ]);
        $users = $em->getRepository(User::class)->findAll();

        $revlikes = $em->getRepository(Like2::class)->getLikesOfReviews($id);

        /*izlistaj frend requests*/
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        /*izlistaj chatove*/
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        /*izlistaj neprocitane poruke gde je receiver=current user*/
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);



        /*set all notifications about this movie as READ */
        $set_to_read=$em->getRepository(Notifications::class)->findBy([
            'received_by'=>$this->getUser()->getId(),
            'is_read'=>false,
            'movie'=>$id
        ]);
        foreach ($set_to_read as $n){
            $n->setIsRead(true);
            $em->persist($n);
        }
        $em->flush();

        /*izlistaj notifications*/
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
/* AJAX ZAHTEVI*/
        if($request->isXmlHttpRequest()){
            /*send recommend movie*/
            if($request->request->get('movie_id_to_recommend')!=null){
                $postoji = $em->getRepository(Recommend::class)->findOneBy([
                   'movie'=>$request->request->get('movie_id_to_recommend'),
                    'received_by'=>$request->request->get('received_by'),
                    'sent_by'=>$this->getUser()->getId()
                ]);

                if(!$postoji){
                    $rec = new Recommend();
                    $rec->setReceivedBy($request->request->get('received_by'));
                    $rec->setSentBy($this->getUser()->getId());
                    $rec->setMovie($request->request->get('movie_id_to_recommend'));
                    $rec->setIsSeen(false);
                    $rec->setTitle($request->request->get('title'));
                    $rec->setCreatedAt(new \DateTime());

                    /*implementiraj i onda menjaj na homepage,moviePage i po kontrolerima...ludilo...*/
                    /*najvise kosta na homepage,mozda samo tu,jer ima foreach petlju*/
                    $postoji = $em->getRepository(Movie::class)->find($request->request->get('movie__id'));
                    if(!$postoji){
                        $movie=new Movie();
                        $movie->setId($request->request->get('movie__id'));
                        $movie->setTitle($request->request->get('movie__title'));
                        $movie->setPosterPath($request->request->get('movie__poster_path'));
                        $movie->setVoteAverage($request->request->get('movie__vote_average'));
                        $movie->setOverview( $request->request->get('movie__overview'));
                        $movie->setGenres($request->request->get('movie__genres'));
                        $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                        $em->persist($movie);
                        $em->flush();
                    }

                    $notif = new Notifications();
                    $notif->setIsRead(false);
                    $notif->setActionPerformer($this->getUser()->getId());
                    $notif->setIsLike(false);
                    $notif->setIsReview(false);
                    $notif->setIsRecommend(true);
                    $notif->setReview('');
                    $notif->setList(0);
                    $notif->setMovie($id);
                    $notif->setTitle($request->request->get('title'));
                    $notif->setReceivedBy($request->request->get('received_by'));


                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    $data['sent_by']=$this->getUser()->getId();
                    $data['movie']=$request->request->get('movie_id_to_recommend');
                    $data['title']=$request->request->get('title');
                    $data['profile_image']=$this->getUser()->getProfileImage();
                    $pusher->trigger(['private-notifications-'.$request->request->get('received_by')],'on_new_recommendation',$data);

                    $em->persist($notif);
                    $em->persist($rec);
                    $em->flush();


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
                $review->setMovie($id);
                $review->setIsGif(true);
                $review->setReviewTxt("");
                $review->setGifUrl($gifurl);
                $review->setState('public');
                $review->setCreatedAt(new \DateTime());

                /*implementiraj i onda menjaj na homepage,moviePage i po kontrolerima...ludilo...*/
                /*najvise kosta na homepage,mozda samo tu,jer ima foreach petlju*/
                $postoji = $em->getRepository(Movie::class)->find($request->request->get('movie__id'));
                if(!$postoji){
                    $movie=new Movie();
                    $movie->setId($request->request->get('movie__id'));
                    $movie->setTitle($request->request->get('movie__title'));
                    $movie->setPosterPath($request->request->get('movie__poster_path'));
                    $movie->setVoteAverage($request->request->get('movie__vote_average'));
                    $movie->setOverview( $request->request->get('movie__overview'));
                    $movie->setGenres($request->request->get('movie__genres'));
                    $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                    $em->persist($movie);
                    $em->flush();
                }


                $notif = new Notifications();
                $notif->setIsRead(false);
                $notif->setActionPerformer($this->getUser()->getId());
                $notif->setIsLike(false);
                $notif->setIsReview(true);
                $notif->setIsRecommend(false);
                $max = $em->getRepository(Review::class)->getMaxID();
                $max = $max[0]['maxi'];
                $max = $max+1;
                $notif->setReview($max);
                $notif->setList(0);
                $notif->setMovie($id);
                $notif->setTitle($request->request->get('title'));
                $notif->setReceivedBy(0);

                $notif_receivers = $em->getRepository(Review::class)->getAllWhoReviewed();
                $kanali = array();

                if($notif_receivers!=null){

                    foreach ($notif_receivers as $k){
                        if($k['movie']==$id && $k['user']!=$this->getUser()->getId()){
                            array_push($kanali,'private-notifications-'.$k['user']);

                            $notif->setReceivedBy($k['user']);

                            $em->persist($notif);
                        }
                    }

                    $data['action_performer']=$this->getUser()->getId();
                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    $data['title']=$request->request->get('title');
                    $data['movie']=$id;
                    $data['profile_image']=$this->getUser()->getProfileImage();
                    $pusher->trigger($kanali,'on_new_review',$data);


                }






                $latestNews = new LatestNews();
                $latestNews->setIsLike(false);
                $latestNews->setCreatedAt(new\DateTime());
                $latestNews->setMovie($id);
                $latestNews->setReview($max);
                $latestNews->setActionPerformer($user);
                $latestNews->setIsReview(true);
                $latestNews->setIsAdd(false);
                $latestNews->setList(0);

                $em->persist($latestNews);



                $em->persist($review);
                $em->flush();

                $reviews = $em->getRepository(Review::class)->findAll();
                $users = $em->getRepository(User::class)->findAll();

                $normalizer = new ObjectNormalizer();
                $encoder = new JsonEncoder();
                $serializer = new Serializer(array($normalizer), array($encoder));
                $revs=$serializer->serialize($reviews, 'json');
                $usrs=$serializer->serialize($users,'json');


                $kaka =$serializer->serialize($kanali,'json');
                $revlikes1 = $em->getRepository(Like2::class)->getLikesOfReviews($id);


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

                $postoji = $em->getRepository(Like2::class)->findOneBy([
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
                    $notif->setMovie($id);
                    $notif->setTitle($request->request->get('title'));

                    $rev = $em->getRepository(Review::class)->findOneBy([
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
                    $pusher->trigger('private-notifications-'.$reviewer_id,'on_new_like',$data);


                    $latestNews = new LatestNews();
                    $latestNews->setIsLike(true);
                    $latestNews->setCreatedAt(new\DateTime());
                    $latestNews->setMovie($id);
                    $latestNews->setReview($reviewid);
                    $latestNews->setActionPerformer($user);
                    $latestNews->setIsReview(false);
                    $latestNews->setIsAdd(false);
                    $latestNews->setList(0);

                    $em->persist($latestNews);

                    $em->persist($notif);

                    $em->persist($like);
                    $em->flush();
                    return new Response('ok');
                }

                $normalizer = new ObjectNormalizer();
                $encoder = new JsonEncoder();
                $serializer = new Serializer(array($normalizer), array($encoder));
                $response=$serializer->serialize($postoji, 'json');
                return new Response($response);
            }

            /*ADD REVIEW*/
            if($request->request->get('user')!=null & $request->request->get('review')!=null){
                $user=$request->request->get('user');
                $review_text=$request->request->get('review');

                /*implementiraj i onda menjaj na homepage,moviePage i po kontrolerima...ludilo...*/
                /*najvise kosta na homepage,mozda samo tu,jer ima foreach petlju*/
                $postoji = $em->getRepository(Movie::class)->find($request->request->get('movie__id'));
                if(!$postoji){
                    $movie=new Movie();
                    $movie->setId($request->request->get('movie__id'));
                    $movie->setTitle($request->request->get('movie__title'));
                    $movie->setPosterPath($request->request->get('movie__poster_path'));
                    $movie->setVoteAverage($request->request->get('movie__vote_average'));
                    $movie->setOverview( $request->request->get('movie__overview'));
                    $movie->setGenres($request->request->get('movie__genres'));
                    $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                    $em->persist($movie);
                    $em->flush();
                }


                $review = new Review();
                $review->setUser($user);
                $review->setMovie($id);
                $review->setReviewTxt($review_text);
                $review->setState('public');
                $review->setCreatedAt(new \DateTime());
                $review->setIsGif(false);
                $review->setGifUrl("");


                $notif = new Notifications();
                $notif->setIsRead(false);
                $notif->setActionPerformer($this->getUser()->getId());
                $notif->setIsLike(false);
                $notif->setIsReview(true);
                $notif->setIsRecommend(false);
                $max = $em->getRepository(Review::class)->getMaxID();
                $max = $max[0]['maxi'];
                $max = $max+1;
                $notif->setReview($max);
                $notif->setList(0);
                $notif->setMovie($id);
                $notif->setTitle($request->request->get('title'));
                $notif->setReceivedBy(0);

                $notif_receivers = $em->getRepository(Review::class)->getAllWhoReviewed();
                $kanali = array();

                if($notif_receivers!=null){
                    foreach ($notif_receivers as $k){
                        if($k['movie']==$id && $k['user']!=$this->getUser()->getId()){
                            array_push($kanali,'private-notifications-'.$k['user']);

                            $notif->setReceivedBy($k['user']);

                            $em->persist($notif);
                        }
                    }

                    $data['action_performer']=$this->getUser()->getId();
                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    $data['title']=$request->request->get('title');
                    $data['movie']=$id;
                    $data['profile_image']=$this->getUser()->getProfileImage();
                    $pusher->trigger($kanali,'on_new_review',$data);
                }



                $latestNews = new LatestNews();
                $latestNews->setIsLike(false);
                $latestNews->setCreatedAt(new\DateTime());
                $latestNews->setMovie($id);
                $latestNews->setReview($max);
                $latestNews->setActionPerformer($user);
                $latestNews->setIsReview(true);
                $latestNews->setIsAdd(false);
                $latestNews->setList(0);

                $em->persist($latestNews);

                $em->persist($notif);

                $em->persist($review);
                $em->flush();

                $reviews = $em->getRepository(Review::class)->findAll();
                $users = $em->getRepository(User::class)->findAll();

                $normalizer = new ObjectNormalizer();
                $encoder = new JsonEncoder();
                $serializer = new Serializer(array($normalizer), array($encoder));
                $revs=$serializer->serialize($reviews, 'json');
                $usrs=$serializer->serialize($users,'json');


                $revlikes1 = $em->getRepository(Like2::class)->getLikesOfReviews($id);


                $reviewLikesObj=$serializer->serialize($revlikes1, 'json');

                $final = '{"kor":'.$usrs .',"lik":'.$reviewLikesObj.',"kom":'. $revs. '}';
                return new JsonResponse($final);
            }

            /* UBACIVANJE U LISTU */
            if($request->request->get('user')!=null & $request->request->get('list')!=null & $request->request->get('tmdbid')!=null) {
                $usrID = $request->request->get('user');
                $listID = $request->request->get('list');
                $tmdbID = $request->request->get('tmdbid');
                $title = $request->request->get('title');

                /*implementiraj i onda menjaj na homepage,moviePage i po kontrolerima...ludilo...*/
                /*najvise kosta na homepage,mozda samo tu,jer ima foreach petlju*/
                $postoji = $em->getRepository(Movie::class)->find($request->request->get('movie__id'));
                if(!$postoji){
                    $movie=new Movie();
                    $movie->setId($request->request->get('movie__id'));
                    $movie->setTitle($request->request->get('movie__title'));
                    $movie->setPosterPath($request->request->get('movie__poster_path'));
                    $movie->setVoteAverage($request->request->get('movie__vote_average'));
                    $movie->setOverview( $request->request->get('movie__overview'));
                    $movie->setGenres($request->request->get('movie__genres'));
                    $movie->setBackdropPath($request->request->get('movie__backdrop_path'));

                    $em->persist($movie);
                    $em->flush();
                }


                $listaMaxID = $this->getDoctrine()->getRepository(UsersList::class)->maxID();
                $stavkaListeMaxID = $this->getDoctrine()->getRepository(UsersListItem::class)->getMaxID($listID);


                $postoji = $this->getDoctrine()->getRepository(UsersListItem::class)->alreadyInList($usrID, $listID, $tmdbID);
                if (!$postoji) {
                    $item = new UsersListItem();


                    $item->setName($title);
                    $item->setListid($listID);
                    $item->setMovie($tmdbID);
                    $item->setListitemid($stavkaListeMaxID[0]['broj'] + 1);

                    $privatnost = $em->getRepository(UsersList::class)->find($listID);

                    if($privatnost->getisPrivate()==false){
                        $latestNews = new LatestNews();
                        $latestNews->setIsLike(false);
                        $latestNews->setCreatedAt(new\DateTime());
                        $latestNews->setMovie($tmdbID);
                        $latestNews->setReview(0);
                        $latestNews->setActionPerformer($usrID);
                        $latestNews->setIsReview(false);
                        $latestNews->setIsAdd(true);
                        $latestNews->setList($listID);

                        $em->persist($latestNews);
                    }

                    $em->persist($item);
                    $em->flush();
                }

                /*$a = $postoji[0]->getName();*/
                $br = $stavkaListeMaxID[0]['broj'] + 1;

                /*return new JsonResponse($stavkaListeMaxID);*/

                return new Response('odje');
            }
        }


        // replace this example code with whatever you need
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
     * @Route("/notebook",name="notebook")
     */
    public function notebookAction(Request $request){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }

        $post = new Post();
        $form = $this->createForm(PostFormType::class,$post);

        $form->handleRequest($request);
        $status = "novost: ";
        if($form->isSubmitted() && $form->isValid())
        {
            $text=$form->get('text')->getData();


            $post->setText($text);
            $post->setUrl("");
            $datum = new\DateTime();
            $post->setCreatedAt($datum);
            $id = $this->getUser()->getId();


            $post->setUser($id);
            $post->setState("public");

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            unset($post);
            unset($form);
            $post = new Post();
            $form = $this->createForm(PostFormType::class,$post);
            $status = $status."Korisnik ".$this->getUser()->getFirstName()." je napravio post: ".$text;
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
        }



        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findAll();

        $users = $em->getRepository(User::class)->findAll();

        /*izlistaj frend requests*/
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        /*izlistaj chatove*/
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        /*izlistaj neprocitane poruke gde je receiver=current user*/
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);
        /*izlistaj notifications*/
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'form'=>$form->createView(),
            'status'=>$status,
            'posts'=>$posts,
            'users'=>$users,
            'friend_requests'=>$friend_requests,
            'chats'=>$chats,
            'unread_msgs'=>$unread_msgs,
            'notifications'=>$notifications
        ]);
    }

    /**
     * @Route("/profile/{id}",name="profile")
     */
    public function profileAction(Request $request,$id){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }


        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var UploadedFile $file */
            $file = $user->getProfileImage();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $user=$this->getUser();
            $user->setProfileImage($fileName);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... persist the $product variable or any other work

            return $this->redirect($this->generateUrl('profile',['id'=>$id]));
        }

        $em = $this->getDoctrine()->getManager();
        /*izlistaj frend requests*/
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        /*izlistaj chatove*/
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        $profile = $em->getRepository(User::class)->findOneBy(['id'=>$id]);
        /*$lists = $em->getRepository(UsersListItem::class)->getListsAndLtems($id);*/
        $lists= $em->getRepository(UsersList::class)->findBy(['user'=>$id]);
        $listitems=$em->getRepository(UsersListItem::class)->findItemsForThisUser($id);
        $reviews=$em->getRepository(Review::class)->userReviewedMovies($id);
        /*izlistaj neprocitane poruke gde je receiver=current user*/
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);
        /*izlistaj notifications*/
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        $reviewedmovies=array();

        if($request->isXmlHttpRequest()) {
            /*kreiranje nove liste*/
            if ($request->request->get('ime_nove_liste') != null) {
                $postoji = $em->getRepository(UsersList::class)->findOneBy([
                    'name'=>$request->request->get('ime_nove_liste'),
                    'user'=>$id
                ]);

                if(!$postoji){
                    $list = new UsersList();
                    $list->setUser($id);
                    $list->setName($request->request->get('ime_nove_liste'));
                    $list->setIsPrivate(false);

                    $em->persist($list);
                    $em->flush();
                    return new JsonResponse('ok');
                }else{
                    return new JsonResponse('exists');
                }
            }
            /*brisanje liste*/
            if($request->request->get('list_name_to_delete')!=null){
                $list_to_remove = $em->getRepository(UsersList::class)->findOneBy([
                   'user'=>$id,
                   'name'=>$request->request->get('list_name_to_delete')
                ]);

                $em->remove($list_to_remove);
                $em->flush();
                return new JsonResponse('');
            }
            /*brisanje filma iz liste */
            if($request->request->get('movie_id_to_delete')!=null){
                $list_item_to_remove = $em->getRepository(UsersListItem::class)->findOneBy([
                    'listid'=>$request->request->get('list_id'),
                    'movie'=>$request->request->get('movie_id_to_delete')
                ]);

                $em->remove($list_item_to_remove);
                $em->flush();
                return new JsonResponse('');
            }
            /*privatnost liste promeni*/
            if($request->request->get('list_id_for_privacy_change')!=null){
                $list_to_change_privacy = $em->getRepository(UsersList::class)->findOneBy([
                    'id'=>$request->request->get('list_id_for_privacy_change'),
                ]);
                if($list_to_change_privacy->getisPrivate()==false){
                    $list_to_change_privacy->setIsPrivate(true);
                }else{
                    $list_to_change_privacy->setIsPrivate(false);
                }

                $em->persist($list_to_change_privacy);
                $em->flush();
                return new JsonResponse('');
            }

        }


        foreach ($reviews as $r){
            $f = $r['movie'];
            $jsonContent = file_get_contents("https://api.themoviedb.org/3/movie/$f?api_key=831c33c0ee756b98159b05350405d661");
            $jsonObject = json_decode($jsonContent,true);
            array_push($reviewedmovies,$jsonObject);
        }

        $all_fr_requests = $em->getRepository(Friends::class)->getRequestsFromAndToUser($this->getUser()->getId());

        return $this->render(':default:profile.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'profile'=>$profile,
            'lists'=>$lists,
            'listitems'=>$listitems,
            'reviews'=>$reviews,
            'reviewedmovies'=>$reviewedmovies,
            'friend_requests'=>$friend_requests,
            'chats'=>$chats,
            'unread_msgs'=>$unread_msgs,
            'all_fr_requests'=>$all_fr_requests,
            'notifications'=>$notifications,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search",name="search_people")
     */
    public function searchPeopleAction(Request $request){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }
        $em=$this->getDoctrine()->getManager();
        /*izlistaj sve ljude*/
        $people = $em->getRepository(User::class)->findAll();
        /*izlistaj frend requests*/
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        /*izlistaj chatove*/
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        /*izlistaj neprocitane poruke gde je receiver=current user*/
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);
        /*izlistaj notifications*/
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        if($request->isXmlHttpRequest()) {
            if ($request->request->get('query') != null) {
                $results = $em->getRepository(User::class)->getPeopleQuery($request->request->get('query'));


                $normalizer = new ObjectNormalizer();
                $encoder = new JsonEncoder();
                $serializer = new Serializer(array($normalizer), array($encoder));
                $results=$serializer->serialize($results, 'json');

                $final = '{"rez":'.$results .'}';
                return new JsonResponse($final);
            }
        }


        return $this->render(':default:searchPeople.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'friend_requests'=>$friend_requests,
            'chats'=>$chats,
            'unread_msgs'=>$unread_msgs,
            'people'=>$people,
            'notifications'=>$notifications
        ]);
    }


}