<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param CategoryTreeFrontPage $categories
     * @param VideoRepository $videoRepository
     * @param $page
     * @param Request $request
     * @return Response
     */
    public function videoList($id, CategoryTreeFrontPage $categories, VideoRepository $videoRepository, $page, Request $request)
    {
        $categories->getCategoryListAndParent($id);
        // recupere les id enfant
        $ids = $categories->getChildIds($id);
        //Permet d'empiler un ou plusieurs element a la fin d'un tableau
        array_push($ids, $id); // envoie id a la fin de ids


        //check func
        $videos = $videoRepository->findByChildIds($ids, $page, $request->get("sortBy"));

        return $this->render('front/video_list.html.twig', [
            "subcategories" => $categories,
            "videos" => $videos
        ]);
    }

    /**
     * @Route("/video-details", name="video_details")
     */
    public function videoDetails()
    {
        return $this->render('front/video_details.html.twig');
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
     */
    public function register()
    {
        return $this->render('front/register.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('front/login.html.twig');
    }


    public function mainCategories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(["parent" => null], ["name" => "ASC"]);
        return $this->render("front/_main_categories.html.twig", [
            "categories" => $categories
        ]);
    }
}
