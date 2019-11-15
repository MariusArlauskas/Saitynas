<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthSuccessListener {

    private $secure = false;
    private $tokenTtl;

    public function __construct($tokenTtl)
    {
        $this->tokenTtl = $tokenTtl;
    }

    public function onAuthSuccess(AuthenticationSuccessEvent $event){
        $response = $event->getResponse();
        $data = $event->getData();

        $token = $data['token'];
        unset($data['token']);
//        unset($data['refresh_token']);
        $event->setData($data);

        $response->headers->setCookie(
            new Cookie('BEARER', $token,
                (new \DateTime())
                    ->add(new \DateInterval('PT' . $this->tokenTtl . 'S'))
                ), '/', null, $this->secure
        );
    }

}