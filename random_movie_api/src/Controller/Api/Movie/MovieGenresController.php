<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 15:44
 */

namespace App\Controller\Api\Movie;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\Common\Collections\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovieController
 * @package App\Controller
 * @Route("/api/movies")
 */
class MovieGenresController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}/genres", name="movie_add_genres", methods={"POST"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function addAction($id, Request $request)
    {
        // Get movie
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get data from request
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

        try {
            $genre = $this->addGenres($genreId, $movie);
        } catch (BadRequestHttpException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        $data = [
            'genre_id' => $genre->getId(),
            'name' => $genre->getName(),
            'description' => $genre->getDescription()
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/genres", name="movie_show_genres", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getAllAction($id)
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
                'description' => $item->getDescription(),
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/genres/{genreId}", name="movie_show_genre", methods={"GET"}, requirements={"id"="\d+", "genreId"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id, $genreId)
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
        $data = $this->forward('App\Controller\Api\Genre\GenreController::getOneAction', [
            'id' => $genre->getId(),
        ]);

        return $data;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}/genres/{genreId}", name="movie_delete_one_genre", methods={"DELETE"}, requirements={"id"="\d+", "genreId"="\d+"})
     * @return JsonResponse
     */
    public function deleteAction($id, $genreId)
    {
        // Get movie
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        if (!$movie) {
            return new JsonResponse('No movie found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Get genre
        $movies = $movie->getMovieGenres();
        foreach ($movies as $item) {
            if ($item->getId() == $genreId){
                $genre = $item;
            }
        }
        if (!isset($genre)) {
            $genreId;
            return new JsonResponse('No genre found for id '.$genreId, Response::HTTP_NOT_FOUND);
        }

        $movie->removeMovieGenre($genre);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save user
        $em->flush();

        $genreId++;
        return new JsonResponse('Removed genre nr ' . $genreId . ' from movie id '.$id, Response::HTTP_OK);
    }

    /**
     * @param $genres string
     * @param $movie Movie
     * @return Genre
     */
    private function addGenres($genres, $movie){
        $ids = explode(", ", $genres);
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = new Genre();
        foreach ($ids as $id) {
            // Get genre
            $genre = $repository->find($id);
            if (!$genre) {
                throw new BadRequestHttpException(sprintf('No genre found for id %d', $id));
            }

            // If a movie already has that genre
            if ($movie->getMovieGenres()->contains($genre)) {
                throw new BadRequestHttpException(sprintf('Movie already has a genre id %d', $id));
            }

            // Add genre
            $movie->addMovieGenre($genre);
        }
        return $genre;
    }
}