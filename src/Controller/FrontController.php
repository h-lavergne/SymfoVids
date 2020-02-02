<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/video-list/category-{id}/{name}/{page}", defaults={"page": "1"}, name="video_list")
     * @param $id
     * @param $page
     * @param CategoryTreeFrontPage $categories
     * @param Request $request
     * @return Response
     */
    public function videoList($id, $page, CategoryTreeFrontPage $categories, Request $request)
    {
        $ids = $categories->getChildIds($id);
        array_push($ids, $id);

        $videos = $this->getDoctrine()
            ->getRepository(Video::class)
            ->findByChildIds($ids ,$page, $request->get("sortBy"));

        $categories->getCategoryListAndParent($id);
        return $this->render('front/video_list.html.twig',[
            'subcategories' => $categories,
            'videos'=>$videos
        ]);
    }

    /**
     * @Route("/video-details/{id}", name="video_details")
     * @param Video $video
     * @return Response
     */
    public function videoDetails(Video $video)
    {
//        dd($video);
        return $this->render('front/video_details.html.twig', [
            "video" => $video
        ]);
    }


    /**
     * @Route("/search-results/{page}", name="search_results", methods={"GET"}, defaults={"page":"1"})
     * @param $page
     * @param Request $request
     * @param VideoRepository $repository
     * @return Response
     */
    public function searchResults($page, Request $request, VideoRepository $repository)
    {
        $videos = null;
        $query = null;

        //cherche name=query
        if ($query = $request->get("query")) {
            $videos = $repository->findByTitle($query, $page, $request->get("sortBy"));

            if (!$videos->getItems()) $videos = null;
        }


        return $this->render('front/search_results.html.twig', [
            "videos" => $videos,
            "query" => $query
        ]);
    }


    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('front/pricing.html.twig');
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment()
    {
        return $this->render('front/payment.html.twig');
    }

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

    public function mainCategories()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['parent'=>null], ['name'=>'ASC']);
        return $this->render('front/_main_categories.html.twig',[
            'categories'=>$categories
        ]);
    }
}
