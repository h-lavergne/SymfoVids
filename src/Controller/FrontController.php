<?php

namespace App\Controller;

use App\Controller\Traits\Likes;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    use Likes;

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
     * @Route("/video-details/{video}", name="video_details")
     * @param VideoRepository $repository
     * @param Video $video
     * @return Response
     */
    public function videoDetails(VideoRepository $repository,Video $video)
    {
//        dd($video);
//        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_REMEMBERED");

        return $this->render('front/video_details.html.twig', [
            "video" => $repository->videoDetails($video)
        ]);
    }

    /**
     * @Route("/new-comment/{video}", methods={"POST"}, name="new_comment")
     * @param Video $video
     * @param Request $request
     * @return RedirectResponse
     */
    public function newComment(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_REMEMBERED");

        $video = $this->getDoctrine()->getRepository(Video::class)->find($video);
        if (!empty(trim($request->request->get("comment"))))
        {
            $comment = new Comment();
            $comment->setContent($request->request->get("comment"));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute("video_details",[
            "video" => $video->getId()
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

    public function mainCategories()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['parent'=>null], ['name'=>'ASC']);
        return $this->render('front/_main_categories.html.twig',[
            'categories'=>$categories
        ]);
    }

    /**
     * @Route("/video-list/{video}/like", name="like_video", methods={"POST"})
     * @Route("/video-list/{video}/dislike", name="dislike_video", methods={"POST"})
     * @Route("/video-list/{video}/unlike", name="undo_like_video", methods={"POST"})
     * @Route("/video-list/{video}/undodislike", name="undo_dislike_video", methods={"POST"})
     * @param Video $video
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_REMEMBERED");

        switch ($request->get("_route"))
        {
            case 'like_video':
                $res = $this->likeVideo($video);
                break;

            case "dislike_video":
                $res = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $res = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $res = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json(["action" => $res, "id"=>$video->getId()]);
    }



}
