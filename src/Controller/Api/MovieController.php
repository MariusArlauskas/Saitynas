<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovieController
 * @package App\Controller
 * @Route("/api/movies")
 */
class MovieController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("", name="movie_create", methods={"POST"})
     * @return JsonResponse
     */
    public function createMovieAction(Request $request)
    {
        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $name = htmlspecialchars($parametersAsArray['name']);
        $author = htmlspecialchars(trim($parametersAsArray['author']));
        $release_date = new \DateTime(
            htmlspecialchars(trim($parametersAsArray['release_date'])));
        $description = htmlspecialchars($parametersAsArray['description']);

        // Validation
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->findBy(['name' => $name]);
        if ($movie) {
            return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
        }
        elseif (
            empty($name) ||
            empty($author) ||
            empty($description)
        ){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }

        // Creating Movie object
        $movie = new Movie();
        $movie->setName($name);
        $movie->setAuthor($author);
        $movie->setReleaseDate($release_date);
        $movie->setDescription($description);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Add movie to Doctrine so that it can be saved
        $em->persist($movie);

        // Save movie
        $em->flush();

        return new JsonResponse('Saved new movie with id '.$movie->getId(), Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("", name="movie_show_list", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $repository->findAll();

        // Adding genres to array
        $newMovies = array();
        foreach ($movies as $item) {
            array_push($newMovies,[
                'id' => $item->getId(),
                'likes_count' => $item->getMovieUsersCount(),
                'name' => $item->getName(),
                'genres' => $item->getMovieGenresString(),
            ]);
        }

        return new JsonResponse($newMovies);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="movie_show", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $movie->getId(),
            'likes_count' => $movie->getMovieUsersCount(),
            'name' => $movie->getName(),
            'author' => $movie->getAuthor(),
            'release_date' => $movie->getReleaseDate()->format('Y-m-d'),
            'genres' => $movie->getMovieGenresString(),
            'description' => $movie->getDescription(),
        ];
        return new JsonResponse($data);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/genres", name="movie_show_genres", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getMovieGenresAction($id)
    {
        // Get movie
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get genres
        $genres = $movie->getMovieGenres();
        if (!isset($genres[0])){
            return new JsonResponse('No genres found for movie id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Add genre info to array
        $data = array(); $nr = 1;
        foreach ($genres as $item) {
            array_push($data, [
                'nr' => $nr++,
                'genre_id' => $item->getId(),
                'name' => $item->getName(),
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="Access denied!!")
     * @Route("/{id}/genres/{genreId}", name="movie_show_genre", methods={"GET"}, requirements={"id"="\d+", "genreId"="\d+"})
     * @return JsonResponse
     */
    public function getMovieOneGenreAction($id, $genreId)
    {
        // Get movie
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get genre
        $genres = $movie->getMovieGenres();
        $genre = $genres[--$genreId];
        if (!isset($genre)) {
            $genreId++;
            return new JsonResponse('No genre found for id '.$genreId, Response::HTTP_NOT_FOUND);
        }

        // Get genres data from GenreController
        $data = $this->forward('App\Controller\Api\GenreController::getOneAction', [
            'id' => $genre->getId(),
        ]);

        return $data;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="movie_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        // Get movie
        $entityManager = $this->getDoctrine()->getManager();
        $movie = $entityManager->getRepository(Movie::class)->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Delete movie
        $entityManager->remove($movie);
        $entityManager->flush();

        return new JsonResponse('Deleted movie with id '.$id, Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="movie_update", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateMovieAction(Request $request, $id)
    {
        // Checking if item exists
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Assingning data from request
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // Getting data from array and removing unnecessary symbols
        // If new data is not set do not set it
        if (isset($parametersAsArray['name'])) {
            $name = htmlspecialchars($parametersAsArray['name']);
        }
        if (isset($parametersAsArray['author'])) {
            $author = htmlspecialchars(trim($parametersAsArray['author']));
        }
        if (isset($parametersAsArray['release_date'])) {
            $release_date = new \DateTime(
                htmlspecialchars(trim($parametersAsArray['release_date'])));
        }
        if (isset($parametersAsArray['description'])) {
            $description = htmlspecialchars($parametersAsArray['description']);
        }

        // Validation
        $isTaken = $repository->findByNameAndNotId($name,$id);
        if ($isTaken) {
            return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
        }
        elseif (
            empty($name) &&
            empty($author) &&
            empty($description) &&
            empty($release_date)
        ){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }

        // If new data is empty leave old one
        if (isset($name)){
            $movie->setName($name);
        }elseif (isset($author)){
            $movie->setAuthor($author);
        }elseif (isset($release_date)){
            $movie->setReleaseDate($release_date);
        }elseif (isset($description)){
            $movie->setDescription($description);
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save movie
        $em->flush();

//        return $this->redirectToRoute('genre_show_list');

        return new JsonResponse('Updated movie with id '.$movie->getId(), Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}/genres", name="movie_add_genre", methods={"POST"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function addMovieGenreAction($id, Request $request)
    {
        // Get movie
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get dadta from request
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // If movie not set in request
        if (isset($parametersAsArray['genreId']) && !empty($parametersAsArray['genreId'])) {
            $genreId = htmlspecialchars($parametersAsArray['genreId']);
        }else{
            return new JsonResponse('Bad data!', Response::HTTP_BAD_REQUEST);
        }

        // Get genre
        $repository2 = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository2->find($genreId);
        if (!$genre) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // If a movie already has that genre
        if ($movie->getMovieGenres()->contains($genre)){
            return new JsonResponse('Movie already has a genre id '.$genreId, Response::HTTP_NOT_FOUND);
        }

        // Add genre
        $movie->addMovieGenre($genre);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        return new JsonResponse('Added genre id ' . $genreId . ' to movie id '.$id, Response::HTTP_OK);
    }


    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}/genres/{genreId}", name="movie_delete_genre", methods={"DELETE"}, requirements={"id"="\d+", "genreId"="\d+"})
     * @return JsonResponse
     */
    public function deleteMovieGenreAction($id, $genreId)
    {
        // Get movie
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get genre
        $movies = $movie->getMovieGenres();
        $genre = $movies[--$genreId];
        if (!isset($genre)) {
            $genreId++;
            return new JsonResponse('No movie found for id '.$genreId, Response::HTTP_NOT_FOUND);
        }

        $movie->removeMovieGenre($genre);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        $genreId++;
        return new JsonResponse('Deleted movie nr ' . $genreId . ' (in users list) from user id '.$id, Response::HTTP_OK);
    }
}
