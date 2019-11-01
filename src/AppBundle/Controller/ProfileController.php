<?php

declare(strict_types=1);

namespace AppBundle\Controller;


use AppBundle\Component\HttpFoundation\ResponseI;
use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\ChatPrivate;
use AppBundle\Entity\Friends;
use AppBundle\Entity\Notifications;
use AppBundle\Entity\Review;
use AppBundle\Entity\User;
use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProfileController extends CommonController {

    /**
     * @Route("/profile/{id}",name="profile")
     * @param Request $request
     * @param $id
     * @return ResponseI
     */
    public function profileAction(Request $request,int $id): ResponseI{
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return $this->redirectToRoute('fos_user_security_login');
        }


        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $user->getProfileImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );
            $user=$this->getUser();
            $user->setProfileImage($fileName);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
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
        $reviewedmovies = [];

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
                if($list_to_change_privacy->getIsPrivate()==false){
                    $list_to_change_privacy->setIsPrivate(true);
                }else{
                    $list_to_change_privacy->setIsPrivate(false);
                }

                $em->persist($list_to_change_privacy);
                $em->flush();
                return new JsonResponse('');
            }

        }

        // @TODO loads in ~9seconds => Cause: VERY EXPENSIVE API CALLS - EVERY CALL IS ~1second => NOT SCALABLE
        $reviewedmovies = $em->getRepository(Review::class)->userReviewedMovies($this->getUser()->getId());



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

}