<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 15/1/2018
 * Time: 15:34
 */

namespace AppBundle\Controller;


use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\ChatPrivate;
use AppBundle\Entity\Friends;
use AppBundle\Entity\Notifications;
use AppBundle\Utils\MyLogger;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DependencyInjection\Tests\Compiler\J;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pusher;
class ChatController extends Controller
{
    /**
     * @Route("/chat-api/message",name="see-message")
     */
    public function postMessage(Request $request)
    {
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

        $em = $this->getDoctrine()->getManager();

/*if left current chat set all unread msgs in that chat as read*/
        if($request->request->get('read_by')!=null){
            $set_me_as_read = $em->getRepository(ChatMessage::class)->findBy([
                'chat_name'=>$request->request->get('presence_chat_name'),'received_by'=>$this->getUser()->getId(),'is_read'=>false
            ]);
            $brojac = 0;
            foreach ($set_me_as_read as $m) {
                $brojac++;
                $m->setIsRead(true);
                $em->persist($m);
            }
            $em->flush();
            /*vrati broj koliko si ih ispravio da bi umanjio to u javascriptu na htmlu*/

            $data['number_of_just_read']=$brojac;
            $data['presence_chat_name']=$request->request->get('presence_chat_name');
            $data['read_by']=$request->request->get('read_by');
            $response = new JsonResponse();
            $response->setData($data);
            return $response;
        }

        /*if sent private chat message*/
            if($request->request->get('message')!=null){
                /*insert into db or some- else here*/
                        /*$notif = new Notifications();
                        $notif->setChatName($request->request->get('presence-chat-name'));
                        $notif->setIsMessage(true);
                        $notif->setIsRead(false);
                        $notif->setIsLike(false);
                        $notif->setIsReview(false);
                        $notif->setIsRecommend(false);*/

                        $msg = new ChatMessage();
                        $msg->setIsRead(false);
                        $msg->setSentBy($request->request->get('sent_by'));
                        $msg->setReceivedBy($request->request->get('received_by'));
                        $msg->setCreatedAt(new \DateTime());
                        $msg->setChatName($request->request->get('presence_chat_name'));
                        $msg->setMessageContent($request->request->get('message'));
                        $maxId = $em->getRepository(ChatMessage::class)->getMaxId($request->request->get('presence_chat_name'));
                        $maxId=$maxId[0]['maxi'];
                        $maxId++;
                        $msg->setMessageId($maxId);
                        $em->persist($msg);
                        $em->flush();


                /*prepare data for pushing*/
                $data['message']=$request->request->get('message');
                $data['presence_chat_name']=$request->request->get('presence_chat_name');
                $data['sent_by']=$request->request->get('sent_by');
                $data['received_by']=$request->request->get('received_by');
                $pusher->trigger([$request->request->get('presence_chat_name')],'on_message',$data); /*push*/ /*to messenger*/
                $pusher->trigger(['private-notifications-'.$request->request->get('received_by')],'on_message',$data); /*to receiver notifications*/

                $response = new JsonResponse();
                $response->setData($data);
                return $response;
            }



        /*sub two users for chat*/
        $initiator = $request->request->get('initiated_by');
        $receiver = $request->request->get('chat_with');
        $channel_name_4= 'presence-chat-'.$initiator.'-'.$receiver;


                    /*INSERT FUNCTION get history of chat*/
                    $getChat = $em->getRepository(ChatPrivate::class)->findOneBy(['initiated_by'=>$initiator,'chat_with'=>$receiver]);
                    if(!$getChat){
                        $getChat = $em->getRepository(ChatPrivate::class)->findOneBy(['initiated_by'=>$receiver,'chat_with'=>$initiator]);
                        if(!$getChat){ /*NEW CHAT, ADD TO DB*/

                            $chat = new ChatPrivate();
                            $chat->setChatName($channel_name_4);
                            $chat->setInitiatedBy($initiator);
                            $chat->setChatWith($receiver);
                            $chat->setCreatedAt(new \DateTime());

                            $em->persist($chat);
                            $em->flush();


                            $chatName = $channel_name_4;

                        }else{
                            $chatName = $getChat->getChatName();
                        }
                    }else{
                        $chatName = $getChat->getChatName();
                    }


                    /*INSERT FUNCTION check if chat history exists*/


        $data['channel_name_4']=$chatName;
        $data['initiated_by']=$initiator;
        $data['chat_with']=$receiver;
        $pusher->trigger(['private-notifications-'.$initiator,'private-notifications-'.$receiver],'one-to-one-chat-request',$data);

        $response = new JsonResponse();
        $response->setData($data);
        return $response;
    }

    /**
     * @Route("chat-api/send",name="server-send")
     */
    public function sendServerMessage(Request $request){

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


        $logger = new MyLogger();
        $pusher->set_logger( $logger );

        $data['message'] = 'hello world2';
        $result = $pusher->trigger('my-channel', 'my-event', $data);
        $logger->log( "---- My Result ---" );
        $logger->log( $result );
        print_r( 'hik' . "\n" );
        var_dump($data);
        exit;
    }

    /**
     * @Route("/pusher/auth",name="pusher_auth")
     */
    public function pusherAuth(Request $request){

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
/*sub for PRESENCE chat */
        $channel_name = $request->request->get('channel_name');

            if (1==1) /* always allow sub to presence chats*/
            {

                $initiator = $request->request->get('initiated_by');
                $receiver = $request->request->get('chat_with');
                $presence_data = array('channel_name' => $channel_name,'initated_by'=>$initiator,'chat_with'=>$receiver);
                echo $pusher->presence_auth($request->request->get('channel_name'),$request->request->get('socket_id'),$initiator,$presence_data);
                /*return new JsonResponse('{"auth":'.$response.'}');*/
                exit;
            }else{
                header('', true, 403);
                echo( "Forbidden" );
                exit;
            }



/*classic sub for private notifications and PRIVATE chats*/




        }

        /**
         * @Route("/chat",name="messenger")
         */
        public function messengerAction(Request $request){
            $securityContext = $this->container->get('security.authorization_checker');
            if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
                return $this->redirectToRoute('fos_user_security_login');
            }
            $em = $this->getDoctrine()->getManager();
            /*izlistaj frend requests*/
            $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
            /*izlistaj chatove*/
            /*$chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());*/
            $chats=$em->getRepository(ChatPrivate::class)->getAllChatsOrderByLastMessage($this->getUser()->getId());
            /*izlistaj postojece prijatelje*/
            $friends = $em->getRepository(Friends::class)->getFriendsAccepted($this->getUser()->getId());
            /*izlistaj neprocitane poruke gde je receiver=current user*/
            $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);
            /*izlistaj notifications*/
            $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
            return $this->render(':default:messenger.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
                'chats'=>$chats,
                'friend_requests'=>$friend_requests,
                'friends'=>$friends,
                'unread_msgs'=>$unread_msgs,
                'notifications'=>$notifications
            ]);

        }



        /**
         * @Route("/chat/{chatname}",name="chat")
         */
        public function chatAction(Request $request,$chatname){
            $securityContext = $this->container->get('security.authorization_checker');
            if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
                return $this->redirectToRoute('fos_user_security_login');
            }

            $niz = explode("-",$chatname);
            $initiator = $niz[2];
            $receiver = $niz[3];
            /*echo $initiator.'-'.$receiver.'-'.$this->getUser()->getId();
            exit;*/



            if($this->getUser()->getId()!=$initiator && $this->getUser()->getId()!=$receiver){
                return $this->redirectToRoute('messenger');
            }

            $loggedInUserID =$this->getUser()->getId();
            $profileID = '';

            if($loggedInUserID==$initiator){
                $profileID=$receiver;
            }else{
                $profileID=$initiator;
            }

            $em = $this->getDoctrine()->getManager();
            /*izlistaj frend requests*/
            $friend_requests = $em->getRepository(Friends::class)->getFriendRequests($this->getUser()->getId());
            /*izlistaj chatove*/
            /*$chats = $em->getRepository(ChatPrivate::class)->getAllChats($this->getUser()->getId());*/
            $chats=$em->getRepository(ChatPrivate::class)->getAllChatsOrderByLastMessage($this->getUser()->getId());
            /*izlistaj postojece prijatelje*/
            $friends = $em->getRepository(Friends::class)->getFriendsAccepted($this->getUser()->getId());
            /*izlistaj sve poruke*/
            $msgs = $em->getRepository(ChatMessage::class)->findBy(['chat_name'=>$chatname]);
            /*izlistaj notifications*/
            $notifications = $em->getRepository(Notifications::class)->getAllNotifications($this->getUser()->getId());
            /*stavi da su sve poruke procitane u ovom chatu*/
            $need_chage_of_status=$em->getRepository(ChatMessage::class)->findBy([
                'received_by'=>$this->getUser()->getId(),
                'is_read'=>false,
                'chat_name'=>$chatname
            ]);
            foreach ($need_chage_of_status as $c){
                $c->setIsRead(true);
                $em->persist($c);
            }
            $em->flush();

            /*izlistaj neprocitane poruke gde je receiver=current user*/
            $unread_msgs = $em->getRepository(ChatMessage::class)->findBy(['received_by'=>$this->getUser()->getId(),'is_read'=>false]);




            return $this->render(':default:chat.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
                'chatname'=>$chatname,
                'profileId'=>$profileID,
                'chats'=>$chats,
                'friend_requests'=>$friend_requests,
                'friends'=>$friends,
                'msgs'=>$msgs,
                'unread_msgs'=>$unread_msgs,
                'notifications'=>$notifications
            ]);
        }


        /**
         * @Route("/friend/add",name="friend_requests")
         */
        public function friendsAction(Request $request){
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

            $em = $this->getDoctrine()->getManager();

/*if is_friend_request*/
            if($request->request->get('is_friend_request')!=null){
                /*insert code here*/
                $all_fr_requests = $em->getRepository(Friends::class)->getRequestsFromAndToUser($this->getUser()->getId());
                if($all_fr_requests!=null){
                    return new JsonResponse('');
                }

                $friends = new Friends();
                $friends->setIsAccepted(false);
                $friends->setReceivedBy($request->request->get('received_by'));
                $friends->setSentBy($request->request->get('sent_by'));
                $em->persist($friends);
                $em->flush();



                $requestor = $em->getRepository(User::class)->findOneBy(['id'=>$request->request->get('sent_by')]);


                $data['sent_by']=$requestor->getId();
                $data['first_name']=$requestor->getFirstName();
                $data['last_name']=$requestor->getLastName();
                $data['profile_image']=$requestor->getProfileImage();
                $pusher->trigger(['private-notifications-'.$request->request->get('received_by')],'on_received_friend_request',$data); /*to receiver notifications*/
                $response = new JsonResponse();
                $response->setData($data);
                return $response;
            }
/*responded to friend request*/
            if($request->request->get('accept_friend_request')!=null){
                if($request->request->get('accept_friend_request')=='accept'){ /*accepted */

                    $handleFriendRequest = $em->getRepository(Friends::class)->findOneBy(['sent_by'=>$request->request->get('sent_by')]);
                    $handleFriendRequest->setIsAccepted(true);
                    $em->persist($handleFriendRequest);
                    $em->flush();

                    /*alert of acceptance*/
                    $data['accepted_by']=$this->getUser()->getId();
                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();

                    $pusher->trigger(['private-notifications-'.$request->request->get('sent_by')],'accepted_friendship',$data); /*to receiver notifications*/

                    $response = new JsonResponse();
                    $response->setData($data);
                    return $response;

                }else{ /*declined */

                    $handleFriendRequest = $em->getRepository(Friends::class)->findOneBy(['sent_by'=>$request->request->get('sent_by')]);
                    $em->remove($handleFriendRequest);
                    $em->flush();

                    $data['declined_by']=$this->getUser()->getId();
                    $data['first_name']=$this->getUser()->getFirstName();
                    $data['last_name']=$this->getUser()->getLastName();
                    /* do not alert user*/
                    $response = new JsonResponse();
                    $response->setData($data);
                    return $response;
                }
            }
        }

}