<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 16:49
 */

namespace App\Controller\Api;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/api/profile")
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
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/movies", name="profile_add_movie", methods={"POST"}, requirements={"movieId"="\d+"})
     * @return JsonResponse
     */
    public function addMovie(Request $request)
    {
        $result = $this->forward('App\Controller\Api\User\UserMoviesController::addAction', [
            'id' => $this->getUser()->getId(),
            'request' => $request,
        ]);

        return $result;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/movies", name="profile_show_movies", methods={"GET"})
     * @return JsonResponse
     */
    public function showMovies(Request $request)
    {
        $result = $this->forward('App\Controller\Api\User\UserMoviesController::getAllAction', [
            'id' => $this->getUser()->getId(),
            'request' => $request,
        ]);

        return $result;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/movies/{movieId}", name="profile_show_one_movie", methods={"GET"}, requirements={"movieId"="\d+"})
     * @return JsonResponse
     */
    public function showOneMovie($movieId)
    {
        $result = $this->forward('App\Controller\Api\User\UserMoviesController::getOneAction', [
            'id' => $this->getUser()->getId(),
            'movieId' => $movieId,
        ]);

        return $result;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/movies/{movieId}", name="profile_delete_movie", methods={"DELETE"}, requirements={"movieId"="\d+"})
     * @return JsonResponse
     */
    public function deleteMovie($movieId)
    {
        $result = $this->forward('App\Controller\Api\User\UserMoviesController::deleteAction', [
            'id' => $this->getUser()->getId(),
            'movieId' => $movieId,
        ]);

        return $result;
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

        if (!empty(json_decode($request->getContent(), true)['username'])){
            $result->headers->clearCookie('BEARER');
            $result->headers->clearCookie('REFRESH_TOKEN');

            $result = $result->setContent(trim("\"" . $result->getContent(), "\"") . ". Please relogin \"");
        }

        return $result;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("", name="delete_profile", methods={"DELETE"})
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $result = $this->forward('App\Controller\Api\User\UserController::deleteAction', [
            'id' => $this->getUser()->getId(),
            'request' => $request,
        ]);

        $result->headers->clearCookie('BEARER');
        $result->headers->clearCookie('REFRESH_TOKEN');

        return $result;
    }
}