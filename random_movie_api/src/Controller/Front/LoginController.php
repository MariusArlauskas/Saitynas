<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 02/12/2019
 * Time: 13:42
 */

namespace App\Controller\Front;


use App\Entity\User;
use App\Form\UserLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login_user")
     */
    public function login(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('read_movie', 2);
        }

        $user = new User();
        $form = $this->createForm(UserLoginType::class, $user);
        $error = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $this->redirectToRoute("api_login_check", array(
                    'username' =>$user->getUsername(),
                    'password' =>$user->getPassword(),
                ));
//            $user2 = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$user->getUsername()]);
//            if($user2 != null){
//                $this->redirectToRoute("api_login_check", array(
//                    'username'=>$user->getUsername(),
//                    'password' =>$user->getPassword(),
//                ));
//            }else{
//                $error = "Wrong username or password1!!";
//            }
        }
        if (!$error){
            $error = "";
        }

        return $this->render('login.html.twig', array(
            'form'=>$form->createView(),
            'error' => $error,
        ));
    }
}