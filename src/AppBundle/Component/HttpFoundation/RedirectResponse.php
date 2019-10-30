<?php


namespace AppBundle\Component\HttpFoundation;


use AppBundle\Component\HttpFoundation\ResponseI;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse implements ResponseI {

    /**
     * RedirectResponse constructor.
     * @param $url
     * @param int $status
     * @param array $headers
     */
    public function __construct($url, $status = 302, $headers = array()) {
        parent::__construct($url, $status, $headers);
    }
}