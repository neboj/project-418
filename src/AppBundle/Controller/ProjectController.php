<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Component\HttpFoundation\ResponseI;
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
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use AppBundle\Form\PostFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\DateTime;
use Pusher;


class ProjectController extends CommonController
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return ResponseI
     */
    public function indexAction(Request $request): ResponseI {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }
        $jsonContentTopRated = file_get_contents("https://api.themoviedb.org/3/movie/top_rated?api_key=831c33c0ee756b98159b05350405d661");
        $jsonObjectTopRated = json_decode($jsonContentTopRated,true);
        $em = $this->getDoctrine()->getManager();
        $reviewsJoinMovie= $em->getRepository(Review::class)->getLatestReviewsJoinMovie();
        $users = $em->getRepository(User::class)->findAll();
        $news = $em->getRepository(LatestNews::class)->getLatestNews();
        $filmsLatestActivityJoinMovieEntity =$em->getRepository(LatestNews::class)->getLatestNewsJoinMovieEntity();
        $people = $em->getRepository(User::class)->findAll();
        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);
        $recommendations = $em->getRepository(Recommend::class)->getAllRecommendations($this->getUser()->getId());
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        return $this->render(':default:homepage.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'users'=>$users,
            'news'=>$news,
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
     * @Route("/notebook",name="notebook")
     * @param Request $request
     * @return ResponseI
     * @throws Exception
     */
    public function notebookAction(Request $request){
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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

        $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
        $chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());
        $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);
        $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
        return $this->render('default/notebook.html.twig', [
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





}