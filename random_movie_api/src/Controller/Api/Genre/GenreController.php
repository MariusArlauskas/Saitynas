<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 16:01
 */

namespace App\Controller\Api\Genre;

use App\Entity\Genre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("", name="genre_create", methods={"POST"})
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // Checking if none of the data is missing
        if (isset($parametersAsArray['name']) &&
            isset($parametersAsArray['description']))
        {
            $name = htmlspecialchars($parametersAsArray['name']);
            $description = htmlspecialchars($parametersAsArray['description']);
        }else{
            return new JsonResponse("Missing data!", Response::HTTP_BAD_REQUEST);
        }

        // Validation
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->findBy(['name' => $name]);
        if ($genre) {
            return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
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

        $data = [
            'id' => $genre->getId(),
            'name' => $genre->getName(),
            'movies_count' => $genre->getGenreMoviesCount(),
            'description' => $genre->getDescription()
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("", name="genre_show_all", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllAction()
    {
        // Getting all gneres
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->findAll();

        // Adding genres to object and removing information overflow
        $newGenres = array();
        foreach ($genre as $item) {
            array_push($newGenres,[
                'id' => $item->getId(),
                'name' => $item->getName(),
                'movies_count' => $item->getGenreMoviesCount(),
                'description' => $item->getDescription(),
                'movies' => $item->getGenreMovies()
            ]);
        }

        return new JsonResponse($newGenres);
    }

    /**
     * @Route("/{id}", name="genre_show_one", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getOneAction($id)
    {
        // Get genre
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);
        if (!$genre) {
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Assign data
        $data = [
            'id' => $genre->getId(),
            'name' => $genre->getName(),
            'movies_count' => $genre->getGenreMoviesCount(),
            'description' => $genre->getDescription(),
        ];

        return new JsonResponse($data);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="genre_update", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateAction(Request $request, $id)
    {
        // Getting genre
        $repository = $this->getDoctrine()->getRepository(Genre::class);
        $genre = $repository->find($id);
        if (!$genre) {
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        // Getting data from array and validating it
        // If new data is not set do not set it
        if (isset($parametersAsArray['name'])) {
            $name = htmlspecialchars($parametersAsArray['name']);

            // If the name is already taken
            $isTaken = $repository->findByNameAndNotId($name,$id);
            if ($isTaken) {
                return new JsonResponse('Name '.$name.' is already taken.', Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($parametersAsArray['description'])) {
            $description = htmlspecialchars($parametersAsArray['description']);
        }

        // If all data was empty
        if (empty($name) && empty($description)){
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
        }

        // If new data empty leave old one
        if (!empty($name)){
            $genre->setName($name);
        }
        if (!empty($description)){
            $genre->setDescription($description);
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Save movie
        $em->flush();

//        return $this->redirectToRoute('genre_show_list');

        return new JsonResponse('Updated genre with id '.$genre->getId(), Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="genre_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        // Get genre
        $entityManager = $this->getDoctrine()->getManager();
        $genre = $entityManager->getRepository(Genre::class)->find($id);
        if (!$genre) {
            return new JsonResponse('No genre found for id '.$id, Response::HTTP_NOT_FOUND);
        }

        // Remove genre
        $entityManager->remove($genre);
        $entityManager->flush();

        return new JsonResponse('Deleted genre with id '.$id, Response::HTTP_OK);
    }
}