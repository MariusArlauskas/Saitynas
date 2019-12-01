<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="user_registration")
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $data = $this->forward('App\Controller\Api\User\UserController::createAction', [
            'request' => $request,
        ]);

        return $data;
    }
}
