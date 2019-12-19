<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 15:41
 */

namespace App\Controller\Api\Movie;

use App\Entity\Genre;
use App\Entity\Movie;
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
class MovieController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("", name="movie_create", methods={"POST"})
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // Check if any of the data is missing
        if (isset($parametersAsArray['name']) &&
            isset($parametersAsArray['author']) &&
            isset($parametersAsArray['release_date']) &&
            isset($parametersAsArray['description']) &&
            isset($parametersAsArray['genreId']))
        {
            $name = htmlspecialchars($parametersAsArray['name']);
            $author = htmlspecialchars(trim($parametersAsArray['author']));
            $release_date = new \DateTime(
                htmlspecialchars(trim($parametersAsArray['release_date'])));
            $description = htmlspecialchars($parametersAsArray['description']);
            $genreId = $parametersAsArray['genreId'];
        }else{
            return new JsonResponse("Missing data!", Response::HTTP_BAD_REQUEST);
        }

        // Validation
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->findBy(['name' => $name]);
        if ($movie) {
            return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
        }

        // Creating Movie object
        $movie = new Movie();
        $movie->setName($name);
        $movie->setAuthor($author);
        $movie->setReleaseDate($release_date);
        $movie->setDescription($description);
        try {
            $this->addGenres($genreId, $movie);
        } catch (BadRequestHttpException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Add movie to Doctrine so that it can be saved
        $em->persist($movie);

        // Save movie
        $em->flush();

        $data = [
            'id' => $movie->getId(),
            'likes_count' => $movie->getMovieUsersCount(),
            'name' => $movie->getName(),
            'release_date' => $movie->getReleaseDate()->format("Y-m-d"),
            'genres' => $movie->getMovieGenres(),
            'author' => $movie->getAuthor(),
            'description' => $movie->getDescription()
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("", name="movie_show_all", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $repository->findAll();

        // Adding data to array
        $newMovies = array();
        foreach ($movies as $item) {
            array_push($newMovies,[
                'id' => $item->getId(),
                'likes_count' => $item->getMovieUsersCount(),
                'name' => $item->getName(),
                'release_date' => $item->getReleaseDate()->format("Y-m-d"),
                'genres' => $item->getMovieGenres(),
                'author' => $item->getAuthor(),
                'description' => $item->getDescription()
            ]);
        }

        return new JsonResponse($newMovies);
    }

    /**
     * @Route("/{id}", name="movie_show_one", methods={"GET"}, requirements={"id"="\d+"})
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
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="movie_update", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateAction(Request $request, $id)
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

        // Getting data from array and validating it
        // If new data is not set do not set it
        if (isset($parametersAsArray['name'])) {
            $name = htmlspecialchars($parametersAsArray['name']);

            // If name is already taken
            $isTaken = $repository->findByNameAndNotId($name,$id);
            if ($isTaken) {
                return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
            }
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

        // If all data was empty
        if (
            empty($name) &&
            empty($author) &&
            empty($description) &&
            empty($release_date)
        ){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }

        // If new data is empty leave old one
        if (!empty($name)){
            $movie->setName($name);
        }
        if (!empty($author)){
            $movie->setAuthor($author);
        }
        if (!empty($release_date)){
            $movie->setReleaseDate($release_date);
        }
        if (!empty($description)){
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
     * @param $genres string
     * @param $movie Movie
     */
    private function addGenres($genres, $movie){
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        foreach ($genres as $id) {
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
    }
}