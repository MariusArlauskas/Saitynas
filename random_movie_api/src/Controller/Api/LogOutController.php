<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 02/12/2019
 * Time: 13:42
 */

namespace App\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogOutController extends AbstractController
{
    /**
     * @Route("/logout", name="logout_user")
     */
    public function logout()
    {
        $result = new JsonResponse("Logged out!!", Response::HTTP_OK);

        $result->headers->clearCookie('BEARER');
        $result->headers->clearCookie('REFRESH_TOKEN');

        session_unset();
        return $result;
    }
}