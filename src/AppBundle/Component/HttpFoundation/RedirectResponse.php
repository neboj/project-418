<?php

declare(strict_types=1);

namespace AppBundle\Component\HttpFoundation;


use AppBundle\Component\HttpFoundation\ResponseI;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse implements ResponseI {}