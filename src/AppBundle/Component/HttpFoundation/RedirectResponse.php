<?php

declare(strict_types=1);

namespace AppBundle\Component\HttpFoundation;


use AppBundle\Component\HttpFoundation\ResponseI;

/**
 * Class RedirectResponse
 * @package AppBundle\Component\HttpFoundation
 */
class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse implements ResponseI {

    public function __construct($url = 'fos_user_security_login', $status = 302, $headers = []) {
        parent::__construct($url, $status, $headers);
    }
}