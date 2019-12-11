<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 01/12/2019
 * Time: 12:31
 */

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JWTInvalid extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        if ($this->requestStack->getCurrentRequest()->cookies->get('BEARER') == "deleted") {
            throw new HttpException(403, "Access denied!!");
        }
    }
}