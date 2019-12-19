<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 16:06
 */

namespace App\Controller\Api\Genre;

use App\Entity\Genre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GenreController
 * @package App\Controller
 * @Route("/api/genres")
 */
class GenreMoviesController extends AbstractController
{
    /**
     * @Route("/{id}/movies", name="genre_show_all_movies", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getAllAction($id)
    {
        // Get genre
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);
        if (!$genre) {
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get movies
        $movies = $genre->getGenreMovies();
        if (!isset($movies[0])){
            return new JsonResponse('No movies found in genre id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Assign data to array
        $data = array(); $nr = 1;
        foreach ($movies as $item) {
            array_push($data, [
                'nr' => $nr++,
                'movie_id' => $item->getId(),
                'likes_count' => $item->getMovieUsersCount(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'release_date' => $item->getReleaseDate()->format("Y-m-d"),
                'author' => $item->getAuthor()
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/movies/{movieId}", name="genre_show_one_movie", methods={"GET"}, requirements={"id"="\d+", "movieId"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id, $movieId)
    {
        // Get genres
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);
        if (!$genre) {
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get genre movie
        $movies = $genre->getGenreMovies();
        $movie = $movies[--$movieId];
        if (!isset($movie)) {
            $movieId++;
            return new JsonResponse('No movie found for id '.$movieId, Response::HTTP_NOT_FOUND);
        }

        // Get movie data from MovieController
        $data = $this->forward('App\Controller\Api\Movie\MovieController::getOneAction', [
            'id' => $movie->getId(),
        ]);

        return $data;
    }
}