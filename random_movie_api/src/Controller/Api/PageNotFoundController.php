<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageNotFoundController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    public function pageNotFoundAction()
    {
        return new JsonResponse("Wrong link!!", Response::HTTP_BAD_REQUEST);
    }
}
