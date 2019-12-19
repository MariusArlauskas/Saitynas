<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 14:45
 */

namespace App\Controller\Api\User;

use App\Entity\Movie;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/api/users")
 */
class UserMoviesController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies", name="user_add_one_movie", methods={"POST"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function addAction($id, Request $request)
    {
        // Get current user if not admin
        if (!$this->isGranted("ROLE_ADMIN")){
            // Setting current user
            $user = $this->getUser();

            if ($user->getId() != $id) {
                throw new HttpException(Response::HTTP_FORBIDDEN, "Access denied!!");
            }
        }else{
            // Finding user
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);
            if (!$user) {
                return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
            }
        }

        // Getting data to array
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // If movie was not set in request
        if (isset($parametersAsArray['movieId']) && !empty($parametersAsArray['movieId'])) {
            $movieId = htmlspecialchars($parametersAsArray['movieId']);
        }else{
            return new JsonResponse('No movie id to add!', Response::HTTP_BAD_REQUEST);
        }

        // Find movie
        $repository2 = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository2->find($movieId);
        if (!$movie) {
            return new JsonResponse('No movie found for id '. $movieId, Response::HTTP_NOT_FOUND);
        }

        // If user already has this movie
        if ($user->getUserMovies()->contains($movie)){
            return new JsonResponse('User already has a movie id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        // Add movie
        $user->addUserMovie($movie);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        if ($user->getId() == $id){
            // Setting current user
            return new JsonResponse('Added movie id ' . $movieId, Response::HTTP_OK);
        }
        return new JsonResponse('Added movie id ' . $movieId . ' to user id '.$id, Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies", name="user_show_movies", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getAllAction($id)
    {
        // Get current user if not admin
        if (!$this->isGranted("ROLE_ADMIN")){
            // Setting current user
            $user = $this->getUser();

            if ($user->getId() != $id) {
                throw new HttpException(Response::HTTP_FORBIDDEN, "Access denied!!");
            }
        }else{
            // Getting user
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);
            if (!$user) {
                return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
            }
        }

        // Getting users movies
        $movies = $user->getUserMovies();
        if (!isset($movies[0])){
            if ($user->getId() == $id){
                return new JsonResponse('You have no movies in your list', Response::HTTP_NOT_FOUND);
            }
            return new JsonResponse('No movies found for user id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Getting movie data to array
        $data = array(); $nr = 1;
        foreach ($movies as $item) {
            array_push($data, [
                'nr' => $nr++,
                'movie_id' => $item->getId(),
                'name' => $item->getName(),
                'author' => $item->getAuthor(),
                'release_date' => $item->getReleaseDate()->format('Y-m-d'),
                'description' => $item->getDescription(),
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies/{movieId}", name="user_show_movie", methods={"GET"}, requirements={"id"="\d+", "movieId"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id, $movieId)
    {
        // Get current user if not admin
        if (!$this->isGranted("ROLE_ADMIN")){
            // Setting current user
            $user = $this->getUser();

            if ($user->getId() != $id) {
                throw new HttpException(Response::HTTP_FORBIDDEN, "Access denied!!");
            }
        }else{
            // Getting user
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);
            if (!$user) {
                return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
            }
        }


        // Getting user movies and selecting needed movie
        $movies = $user->getUserMovies();
        $movie = $movies[--$movieId];
        if (!isset($movie)) {
            $movieId++;
            return new JsonResponse('No movie found for id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        // Calling other controller to show a movie
        $data = $this->forward('App\Controller\Api\Movie\MovieController::getOneAction', [
            'id' => $movie->getId(),
        ]);

        return $data;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies/{movieId}", name="user_delete_one_movie", methods={"DELETE"}, requirements={"id"="\d+", "movieId"="\d+"})
     * @return JsonResponse
     */
    public function deleteAction($id, $movieId)
    {
        // Get current user if not admin
        if (!$this->isGranted("ROLE_ADMIN")){
            // Setting current user
            $user = $this->getUser();

            if ($user->getId() != $id) {
                throw new HttpException(Response::HTTP_FORBIDDEN, "Access denied!!");
            }
        }else{
            // Getting user
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);
            if (!$user) {
                return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
            }
        }

        // Getting users movies
        $movies = $user->getUserMovies();
        foreach ($movies as $item) {
            if ($item->getId() == $movieId){
                $movie = $item;
            }
        };

        if (!isset($movie)) {
            $movieId;
            return new JsonResponse('No movie found for id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        $user->removeUserMovie($movie);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        $movieId++;
        if ($user->getId() == $id) {
            // Setting current user
            return new JsonResponse('Removed movie nr ' . $movieId, Response::HTTP_OK);
        }
        return new JsonResponse('Removed movie nr ' . $movieId . ' from user id '.$id, Response::HTTP_OK);
    }
}