<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\ChatPrivate;
use AppBundle\Entity\Friends;
use AppBundle\Entity\Notifications;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class SearchPeopleController
 * @package AppBundle\Controller
 */
class SearchPeopleController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller {
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

                $final = '{"responseData":'.$results .'}';
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