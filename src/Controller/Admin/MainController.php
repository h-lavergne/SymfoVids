<?php

namespace App\Controller\Admin;

use App\Repository\VideoRepository;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class MainController extends AbstractController {

    /**
     * @Route("", name="admin_main_page")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }

    /**
     * @Route("/videos", name="videos")
     * @param VideoRepository $repository
     * @return Response
     */
    public function videos(VideoRepository $repository)
    {
        if ($this->isGranted("ROLE_ADMIN")) {
            $videos = $repository->findAll();
        }
        else {
            $videos = $this->getUser()->getLikedVideos();
        }

        return $this->render('admin/videos.html.twig', [
            "videos" => $videos
        ]);
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $categories->getCategoryList($categories->buildTree());
        return $this->render("admin/_all_categories.html.twig", [
            "categories" => $categories,
            "editedCategory" => $editedCategory
        ]);
    }
}