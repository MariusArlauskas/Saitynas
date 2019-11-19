<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 18/11/2019
 * Time: 14:17
 */

namespace App\Controller\Api\User;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetAllUsers
 * @package App\Controller\Api\User
 * @Route("/api/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="user_create", methods={"POST"})
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        // Assingning data from request and removing unnecessary symbols
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        // Check if none of the data is missing
        if (isset($parametersAsArray['username']) &&
            isset($parametersAsArray['password']) &&
            isset($parametersAsArray['confirm_password']) &&
            isset($parametersAsArray['email']))
        {
            $username = htmlspecialchars($parametersAsArray['username']);
            $password = htmlspecialchars(trim($parametersAsArray['password']));
            $confirm_password = htmlspecialchars(trim($parametersAsArray['confirm_password']));
            $email = htmlspecialchars($parametersAsArray['email']);
        }else{
            return new JsonResponse("Missing data!", Response::HTTP_BAD_REQUEST);
        }

        // Validation
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findBy(['username' => $username]);
        if ($user) {
            return new JsonResponse('Username '.$username.' is already taken.', Response::HTTP_BAD_REQUEST);
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
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("", name="user_show_all", methods={"GET"})
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
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Access denied!!")
     * @Route("/{id}", name="user_show_one", methods={"GET"}, requirements={"id"="\d+"})
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
     * @Route("/{id}", name="user_update", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        // User can only update his own account
        if (!$this->isGranted("ROLE_ADMIN")){
            // Setting current user
            $user = $this->getUser();
        }else{
            // Getting user
            $user = $repository->find($id);
            if (!$user) {
                return new JsonResponse('No user found for id ' . $id, Response::HTTP_NOT_FOUND);
            }
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

            // If username is already taken (user with another id has the same username)
            $isTaken = $repository->findByNameAndNotId($username, $id);
            if ($isTaken) {
                return new JsonResponse('Username ' . $username . ' is already taken.', Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($parametersAsArray['password']) && isset($parametersAsArray['confirm_password'])) {
            $password = htmlspecialchars(trim($parametersAsArray['password']));
            $confirm_password = htmlspecialchars(trim($parametersAsArray['confirm_password']));

            // If passwords dont match
            if ($password != $confirm_password){
                return new JsonResponse("Passwords do not match!", Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($parametersAsArray['email'])) {
            $email = htmlspecialchars($parametersAsArray['email']);
        }

        // If all dadta was empty
        if (
            empty($username) &&
            empty($email) &&
            (empty($confirm_password) || empty($password))
        ) {
            return new JsonResponse("Inavlid data!", Response::HTTP_BAD_REQUEST);
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

        return new JsonResponse('Updated user with id '.$user->getId(), Response::HTTP_OK);
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
}