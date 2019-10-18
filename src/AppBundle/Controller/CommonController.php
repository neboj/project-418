<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pusher;

class CommonController extends Controller
{
    /**
     * @return Pusher\Pusher
     */
    protected function getPusherInstance() {
        $options = array(
            'cluster' => 'eu',
            'encrypted' => true
        );
        return $pusher = new Pusher\Pusher(
            '82800fdb37dfd38f4722',
            'e5e2af578bbd993d3cc2',
            '457440',
            $options
        );
    }

//    protected function handleAuth() {
//        $result = true;
//        $securityContext = $this->container->get('security.authorization_checker');
//        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
//            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
//            $result = $this->redirectToRoute('fos_user_security_login');
//        }
//        return $result;
//    }
}