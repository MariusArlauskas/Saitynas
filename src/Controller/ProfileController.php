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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("", name="show_profile", methods={"GET"})
     * @return JsonResponse
     */
    public function getAction()
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

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("", name="edit_profile", methods={"PUT"})
     * @return JsonResponse
     */
    public function editAction(Request $request)
    {
        $result = $this->forward('App\Controller\Api\User\UserController::updateAction', [
            'id' => $this->getUser()->getId(),
            'request' => $request,
        ]);

        return $result;
    }
}