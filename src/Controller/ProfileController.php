<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 16:49
 */

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/profile", name="profile", methods={"GET"})
     * @return JsonResponse
     */
    public function showAction()
    {
        $user = $this->getUser();

        $data = [
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail(),
            'movies' => $user->getUserMoviesString(),
        ];

        return new JsonResponse($data);

        return $data;
    }
}