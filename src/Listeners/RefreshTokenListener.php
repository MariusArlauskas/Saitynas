<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

class RefreshTokenListener implements EventSubscriberInterface{

    private $secure = false;
    private $Ttl;

    public function __construct($tokenTtl)
    {
        $this->Ttl = $tokenTtl;
    }

    public function setRefreshToken(AuthenticationSuccessEvent $event){
        $refreshToken = $event->getData()['refreshToken'];
        $response = $event->getResponse();

        if ($refreshToken){
            $response->headers->setCookie(
                new Cookie('REFRESH_TOKEN', $refreshToken,
                    (new \DateTime())
                        ->add(new \DateInterval('PT' . $this->Ttl . 'S'))), '/', null, $this->secure);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => [
                ['setRefreshToken']
            ]
        ];
    }
}