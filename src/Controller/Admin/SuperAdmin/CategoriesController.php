<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/su/categories", name="categories", methods={"GET","POST"})
     * @param CategoryTreeAdminList $categories
     * @param Request $request
     * @param CategoryRepository $repository
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function categories(CategoryTreeAdminList $categories, Request $request, CategoryRepository $repository, EntityManagerInterface $em)
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);


        if ($this->saveCategory($form, $category, $request, $repository, $em)) {
            return $this->redirectToRoute("categories");
        }

        return $this->render('admin/categories.html.twig', [
            "categories" => $categories,
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/su/edit-category/{id}", name="edit_category", methods={"GET", "POST"})
     * @param Category $category
     * @param Request $request
     * @param CategoryRepository $repository
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function editCategory(Category $category, Request $request, CategoryRepository $repository, EntityManagerInterface $em)
    {
        $form = $this->createForm(CategoryType::class, $category);

        if ($this->saveCategory($form, $category, $request, $repository, $em)) {
            return $this->redirectToRoute("categories");
        }

        return $this->render('admin/edit_category.html.twig', [
            "category" => $category,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/su/delete-category/{id}", name="delete_category")
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

    private function saveCategory($form, $category, $request, $repository, $em)
    {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $parent = $repository->find($request->request->get("category")["parent"]);
            $category->setParent($parent);

            $em->persist($category);
            $em->flush();

            return true;
        }

        return false;
    }
}