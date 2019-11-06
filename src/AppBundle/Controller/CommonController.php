<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Component\HttpFoundation\Response;
use AppBundle\Component\HttpFoundation\ResponseI;
use AppBundle\Controller\Constants\API_Credentials;
use AppBundle\Pusher\Pusher_Credentials;
use AppBundle\Entity\LatestNews;
use AppBundle\Entity\Movie;
use AppBundle\Entity\UsersList;
use AppBundle\Entity\UsersListItem;
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pusher;
use AppBundle\Component\HttpFoundation\JsonResponse;
use AppBundle\Component\HttpFoundation\RedirectResponse;

/**
 * Class CommonController
 * @package AppBundle\Controller
 */
class CommonController extends Controller
{

    /**
     * Renders a view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @param \Symfony\Component\HttpFoundation\Response $response A response instance
     *
     * @return ResponseI
     */
    protected function render($view, array $parameters = array(), \Symfony\Component\HttpFoundation\Response $response = null) {
        $response = parent::render($view, $parameters, $response);
        return new Response($response->getContent());
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route The name of the route
     * @param array $parameters An array of parameters
     * @param int $status The status code to use for the Response
     *
     * @return ResponseI
     */
    protected function redirectToRoute($route, array $parameters = array(), $status = 302): ResponseI
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * @param string $url
     * @param int $status
     * @return ResponseI
     */
    protected function redirect($url, $status = 302): ResponseI
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @return Pusher\Pusher
     * @throws Pusher\PusherException
     */
    protected function getPusherInstance() {
        return \AppBundle\Pusher\Pusher::getInstance();
    }

}