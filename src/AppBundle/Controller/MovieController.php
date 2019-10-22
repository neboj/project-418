<?php


namespace AppBundle\Controller;


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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pusher;


class MovieController extends CommonController
{
    /**
     * @Route("/movies/a/{id}", name="moviePage")
     */
    public function movieAction(Request $request,$id){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }

        $pusher = $this->getPusherInstance();


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

                    if($privatnost->getIsPrivate()==false){
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

}