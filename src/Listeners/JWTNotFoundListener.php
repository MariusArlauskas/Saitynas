<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class JWTNotFoundListener extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    public function __construct(RequestStack $requestStack, EventDispatcherInterface $dispatcher)
    {
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
    }
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        if ($this->requestStack->getCurrentRequest()->cookies->get('REFRESH_TOKEN')) {
            $data = [
                'status'  => Response::HTTP_UNAUTHORIZED . ' Unauthorized',
                'message' => 'Expired token',
            ];
            $response = new JsonResponse($data, 401);
            return $event->setResponse($response);
        }
        $data = [
            'status'  => Response::HTTP_FORBIDDEN . ' Forbidden',
            'message' => 'Missing token',
        ];
        $response = new JsonResponse($data, 401);
        return $event->setResponse($response);
    }
}