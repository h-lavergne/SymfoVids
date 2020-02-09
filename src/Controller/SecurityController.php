<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $encoder->encodePassword($user, $request->request->get("user")["password"]["first"]);

            $user->setName($request->request->get("user")["name"]);
            $user->setLastName($request->request->get("user")["last_name"]);
            $user->setEmail($request->request->get("user")["email"]);
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);

            $em->persist($user);
            $em->flush();

            $this->loginUserAutomatically($user, $password);
            return $this->redirectToRoute("admin_main_page");
        }

        return $this->render('front/register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {


        return $this->render('front/login.html.twig', [
            "username" => $utils->getLastUsername(),
            "error" => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
    }


    private function loginUserAutomatically($user, $password){
        $token = new UsernamePasswordToken(
            $user,
            $password,
            "main",
            $user->getRoles()
        );

        $this->get('security.token_storage')->setToken($token);
        $this->get("session")->set("_security_main", serialize($token));
    }
}
