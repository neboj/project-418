<?php


namespace AppBundle\Pusher;

use AppBundle\Pusher\Pusher_Credentials;
use Pusher\PusherException;

class Pusher extends \Pusher\Pusher {

    /**
     * Default pusher options
     *
     * @var array
     */
    private static $options = [
        'cluster' => 'eu',
        'encrypted' => true
    ];

    /**
     * @var \Pusher\Pusher | null
     */
    private static $instance;

    /**
     * Returns pusher instance
     *
     * @return \Pusher\Pusher
     * @throws PusherException
     */
    public static function getInstance() {
        if (self::$instance === null) {
            return new Pusher(
                Pusher_Credentials::AUTH_KEY,
                Pusher_Credentials::SECRET_KEY,
                Pusher_Credentials::APP_ID,
                self::$options
            );
        }
        return self::$instance;
    }

    public function __construct($auth_key = '', $secret = '', $app_id = '', $options = array(), $host = null, $port = null, $timeout = null)
    {
        parent::__construct($auth_key, $secret, $app_id, $options, $host, $port, $timeout);
    }

}