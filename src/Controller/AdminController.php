<?php

namespace App\Controller;

use App\Entity\Category;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="admin_main_page")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }


    /**
     * @Route("/videos", name="videos")
     */
    public function videos()
    {
        return $this->render('admin/videos.html.twig');
    }

    /**
     * @Route("/upload_video", name="upload_video")
     */
    public function uploadVideo()
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users()
    {
        return $this->render('admin/users.html.twig');
    }


    /**
     * @Route("/categories", name="categories")
     * @param CategoryTreeAdminList $categories
     * @return Response
     */
    public function categories(CategoryTreeAdminList $categories)
    {
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/categories.html.twig', [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/edit-category/{id}", name="edit_category")
     * @param Category $category
     * @return Response
     */
    public function editCategory(Category $category)
    {

        dump($category);
        return $this->render('admin/edit_category.html.twig', [
            "category" => $category
        ]);
    }

    /**
     * @Route("/delete-category/{id}", name="delete_category")
     * @param Category $category
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function deleteCategory(Category $category, EntityManagerInterface $em)
    {
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute("categories");
    }



    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null){
        $categories->getCategoryList($categories->buildTree());
        return $this->render("admin/_all_categories.html.twig", [
            "categories" => $categories,
            "editedCategory" => $editedCategory
        ]);
    }
}
