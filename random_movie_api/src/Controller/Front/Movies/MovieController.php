<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 09/12/2019
 * Time: 15:50
 */

namespace App\Controller\Front\Movies;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/movies/{id}", name="read_movie", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $data = $this->forward('App\Controller\Api\Movie\MovieController::getOneAction', [
            'id' => $id,
        ]);

        return $this->render('movies/show.html.twig', [
            'movie' => json_decode($data->getContent()),
        ]);
    }
}