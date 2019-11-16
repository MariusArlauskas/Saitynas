<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/api/users")
 */
class UserController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("", name="user_create", methods={"POST"})
     * @return JsonResponse
     */
    public function createUserAction(Request $request)
    {
        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $username = htmlspecialchars($parametersAsArray['username']);
        $password = htmlspecialchars(trim($parametersAsArray['password']));
        $confirm_password = htmlspecialchars(trim($parametersAsArray['confirm_password']));
        $email = htmlspecialchars($parametersAsArray['email']);

        // Validation
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findBy(['username' => $username]);
        if ($user) {
            return new JsonResponse('Username '.$username.' is already taken.', Response::HTTP_BAD_REQUEST);
        }
        elseif (
            empty($username) ||
            empty($password) ||
            empty($confirm_password)
        ){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }
        elseif ($password != $confirm_password){
            return new JsonResponse("Passwords don't match!", Response::HTTP_BAD_REQUEST);
        }

        // Creating user object
        $user = new User();
        $user->setUsername($username);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Add user to Doctrine so that it can be saved
        $em->persist($user);

        // Save user
        $em->flush();

        return new JsonResponse('Saved new user with id '.$user->getId(), Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("", name="user_show_list", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllAction()
    {
        // Fetching all users
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        // Assigning only data that we will return
        $newUsers = array();
        foreach ($users as $item) {
            array_push($newUsers,[
                'id' => $item->getId(),
                'username' => $item->getUsername(),
                'email' => $item->getEmail(),
                'favoritesCount' => $item->getUserMoviesCount(),
            ]);
        }

        return new JsonResponse($newUsers);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="user_show", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id)
    {
        // Finding user
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        if (!$user) {
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Assigning only user valuees which we will return
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
     * @Route("/{id}/movies", name="user_show_movies", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getUserMoviesAction($id)
    {
        // Getting user
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        if (!$user) {
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Getting users movies
        $movies = $user->getUserMovies();
        if (!isset($movies[0])){
            return new JsonResponse('No movies found for user id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Getting movie data to array
        $data = array(); $nr = 1;
        foreach ($movies as $item) {
            array_push($data, [
                'nr' => $nr++,
                'movie_id' => $item->getId(),
                'name' => $item->getName(),
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies/{movieId}", name="user_show_movie", methods={"GET"}, requirements={"id"="\d+", "movieId"="\d+"})
     * @return JsonResponse
     */
    public function getUserOneMovieAction($id, $movieId)
    {
        // Getting user
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        if (!$user) {
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Getting user movies and selecting needed movie
        $movies = $user->getUserMovies();
        $movie = $movies[--$movieId];
        if (!isset($movie)) {
            $movieId++;
            return new JsonResponse('No movie found for id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        // Calling other controller to show a movie
        $data = $this->forward('App\Controller\Api\MovieController::getOneAction', [
            'id' => $movie->getId(),
        ]);

        return $data;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="user_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        // Getting user
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Removing user
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse('Deleted user with id '.$id, Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="user_update", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateUserAction(Request $request, $id)
    {
        // Getting user
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        if (!$user) {
            return new JsonResponse('No user found for id ' . $id, Response::HTTP_NOT_FOUND);
        }

        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // Getting data from array
        $password = null; $confirm_password = null;

        // If new data is not set do not set it
        if (isset($parametersAsArray['username'])) {
            $username = htmlspecialchars($parametersAsArray['username']);
        }
        if (isset($parametersAsArray['password'])) {
            $password = htmlspecialchars(trim($parametersAsArray['password']));
        }
        if (isset($parametersAsArray['confirm_password'])) {
            $confirm_password = htmlspecialchars(trim($parametersAsArray['confirm_password']));
        }
        if (isset($parametersAsArray['email'])) {
            $email = htmlspecialchars($parametersAsArray['email']);
        }

        // Validation
        // If username is already taken (user with another id has the same username)
        $isTaken = $repository->findByNameAndNotId($username, $id);
        if ($isTaken) {
            return new JsonResponse('Username ' . $username . ' is already taken.', Response::HTTP_BAD_REQUEST);
        }
        elseif (
            // If all data is empty
            empty($username) &&
            empty($email) &&
            (empty($confirm_password) || empty($password))
        ) {
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }
        elseif ($password != $confirm_password){
            return new JsonResponse("Passwords do not match!", Response::HTTP_BAD_REQUEST);
        }

        // If new data was not set leave old one
        if (isset($username)){
            $user->setUsername($username);
        }elseif (isset($password)){
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }elseif (isset($email)){
            $user->setEmail($email);
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save movie
        $em->flush();

//        return $this->redirectToRoute('genre_show_list');

        return new JsonResponse('Updated movie with id '.$user->getId(), Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies", name="user_add_movie", methods={"POST"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function addUserMoviesAction($id, Request $request)
    {
        // Getting user
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        // If user was not found
        if (!$user) {
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // If movie was not set in request
        if (isset($parametersAsArray['movieId']) && !empty($parametersAsArray['movieId'])) {
            $movieId = htmlspecialchars($parametersAsArray['movieId']);
        }else{
            return new JsonResponse('Bad data!', Response::HTTP_BAD_REQUEST);
        }

        // Find movie
        $repository2 = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository2->find($movieId);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // If user already has this movie
        if ($user->getUserMovies()->contains($movie)){
            return new JsonResponse('User already has a movie id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        $user->addUserMovie($movie);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        return new JsonResponse('Added movie id ' . $movieId . ' to user id '.$id, Response::HTTP_OK);
    }


    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}/movies/{movieId}", name="user_delete_movie", methods={"DELETE"}, requirements={"id"="\d+", "movieId"="\d+"})
     * @return JsonResponse
     */
    public function deleteUserMovieAction($id, $movieId)
    {
        // Getting user
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        // If user not found
        if (!$user) {
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Getting users movies
        $movies = $user->getUserMovies();
        $movie = $movies[--$movieId];
        if (!isset($movie)) {
            $movieId++;
            return new JsonResponse('No movie found for id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        $user->removeUserMovie($movie);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        $movieId++;
        return new JsonResponse('Deleted movie nr ' . $movieId . ' (in users list) from user id '.$id, Response::HTTP_OK);
    }
}
