<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth")
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $data = $this->forward('App\Controller\Api\UserController::createUserAction', [
            'request' => $request,
        ]);

        return $data;
    }

    /**
     * api route redirects
     * @return Response
     */
    public function apii()
    {
        return new Response(sprintf("Logged in as %s", $this->getUser()->getUsername()));
    }

}
