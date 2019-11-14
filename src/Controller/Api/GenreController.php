<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GenreController
 * @package App\Controller
 * @Route("/api/genres")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", name="genre_create", methods={"POST"})
     * @return JsonResponse
     */
    public function createGenreAction(Request $request)
    {
        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $name = htmlspecialchars($parametersAsArray['name']);
        $description = htmlspecialchars($parametersAsArray['description']);

        // Validation
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->findBy(['name' => $name]);
        if ($genre) {
            return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
        }
        elseif (empty($name) || empty($description)){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }

        // Creating Movie object
        $genre = new Genre();
        $genre->setName($name);
        $genre->setDescription($description);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Add movie to Doctrine so that it can be saved
        $em->persist($genre);

        // Save movie
        $em->flush();

        return new JsonResponse('Saved new genre with id '.$genre->getId(), Response::HTTP_OK);
    }

    /**
     * @Route("/", name="genre_show_list", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->findAll();

        // Adding genres to object and removing information overflow
        $newGenres = array();
        foreach ($genre as $item) {
            array_push($newGenres,[
                'id' => $item->getId(),
                'name' => $item->getName(),
                'movies_count' => $item->getGenreMoviesCount(),
            ]);
        }

        return new JsonResponse($newGenres);
    }

    /**
     * @Route("/{id}", name="genre_show", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);

        if (!$genre) {
//            throw $this->createNotFoundException(
//                'No genre found for id '.$id
//            );
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $genre->getId(),
            'name' => $genre->getName(),
            'movies_count' => $genre->getGenreMoviesCount(),
            'description' => $genre->getDescription(),
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/movies", name="genre_show_movies", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getGenreMoviesAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);

        if (!$genre) {
//            throw $this->createNotFoundException(
//                'No genre found for id '.$id
//            );
            return new JsonResponse('No user found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        $movies = $genre->getGenreMovies();

        if (!isset($movies[0])){
            return new JsonResponse('No movies found in genre id '.$id, Response::HTTP_NOT_FOUND);
        }

        $data = array(); $nr = 1;
        foreach ($movies as $item) {
            array_push($data, [
                'nr' => $nr++,
                'movie_id' => $item->getId(),
                'likes_count' => $item->getMovieUsersCount(),
                'name' => $item->getName(),
                'genres' => $item->getMovieGenresString(),
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/movies/{movieId}", name="genre_show_movie", methods={"GET"}, requirements={"id"="\d+", "movieId"="\d+"})
     * @return JsonResponse
     */
    public function getMovieOneGenreAction($id, $movieId)
    {
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);

        if (!$genre) {
//            throw $this->createNotFoundException(
//                'No genre found for id '.$id
//            );
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        $movies = $genre->getGenreMovies();
        $movie = $movies[--$movieId];

        if (!isset($movie)) {
            $movieId++;
//            throw $this->createNotFoundException(
//                'No genre found for id '.$id
//            );
            return new JsonResponse('No movie found for id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        $data = $this->forward('App\Controller\MovieController::getOneAction', [
            'id' => $movie->getId(),
        ]);

        return $data;
    }

    /**
     * @Route("/{id}", name="genre_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $genre = $entityManager->getRepository(Genre::class)->find($id);

        if (!$genre) {
//            throw $this->createNotFoundException(
//                'No genre found for id '.$id
//            );
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($genre);
        $entityManager->flush();

        return new JsonResponse('Deleted genre with id '.$id, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="genre_update", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateGenreAction(Request $request, $id)
    {
        // Checking if item exists
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);
        if (!$genre) {
//            throw $this->createNotFoundException(
//                'No genre found for id '.$id
//            );
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // Getting data from array
        // If new data is not set do not set it
        if (isset($parametersAsArray['name'])) {
            $name = htmlspecialchars($parametersAsArray['name']);
        }
        if (isset($parametersAsArray['description'])) {
            $description = htmlspecialchars($parametersAsArray['description']);
        }

        // Validation
        $isTaken = $repository->findByNameAndNotId($name,$id);
        if ($isTaken) {
            return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
        }
        elseif (empty($name) && empty($description)){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }

        // If new data empty leave old one
        if (isset($name)){
            $genre->setName($name);
        }elseif (isset($description)){
            $genre->setDescription($description);
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save movie
        $em->flush();

//        return $this->redirectToRoute('genre_show_list');

        return new JsonResponse('Updated genre with id '.$genre->getId(), Response::HTTP_OK);
    }
}
