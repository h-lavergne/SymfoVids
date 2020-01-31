<?php

namespace App\Utils;

use App\Twig\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{

    // set le html en variable

    public $html_ul_open = '<ul>';
    public $html_li_open = '<li>';
    public $html_a_open = '<a href="';
    public $html_a_href = '">';
    public $html_a_close = '</a>';
    public $html_li_close = '</li>';
    public $html_ul_close = '</ul>';


    public function getCategoryListAndParent($id): string
    {
        $this->slugger = new AppExtension(); //set Instance of AppExtension for filters
        $parentData = $this->getMainParent($id);// recupere le parent si existe en fonction de l'id

        //créér variable pour nom et id du parent récupérer depuis getMainParent
        $this->mainParentName = $parentData["name"];
        $this->mainParentId = $parentData["id"];

        //cherche l'emplacement de l'id en parametre dans le tableau d'id de toutes les catégories
        $key = array_search($id, array_column($this->categoriesArrayFromDb, "id"));// return si ya le meme id

        //creer une variable avec la name de la category récupérée
        $this->currentCategoryName = $this->categoriesArrayFromDb[$key]["name"];

        $categoriesArray = $this->buildTree($parentData["id"]);

        return $this->getCategoryList($categoriesArray);
    }

    public function getCategoryList(array $categoriesArray)
    {


        $this->categoryList .= $this->html_ul_open;
        foreach ($categoriesArray as $value) {

            $catName = $this->slugger->slugify($value["name"]);//utilise l'instance de AppExtension

            $url = $this->generator->generate("video_list", ["name" => $catName, "id" => $value["id"]]);

            $this->categoryList .= $this->html_li_open . $this->html_a_open . $url . $this->html_a_href . $catName . $this->html_a_close;

            if (!empty($value["children"])) {
                $this->getCategoryList($value["children"]);
            }
            $this->categoryList .= $this->html_li_close;

        }
        $this->categoryList .= $this->html_ul_close;
        return $this->categoryList;
    }

    public function getMainParent(int $id): array
    {

        $key = array_search($id, array_column($this->categoriesArrayFromDb, "id"));//cherche la position de la category dans l'array via l'id

        //si d'apres la position -> parent
        if ($this->categoriesArrayFromDb[$key]["parent_id"] != null) {
            //return le parent de la category
            return $this->getMainParent($this->categoriesArrayFromDb[$key]["parent_id"]);
        } else {
            //sinon return la categorie sans enfants en question
            return [
                "id" => $this->categoriesArrayFromDb[$key]["id"],
                "name" => $this->categoriesArrayFromDb[$key]["name"]
            ];
        }
    }

    public function getChildIds(int $parent): array
    {
        static $ids = [];
        //pour chaque category en DB
        foreach ($this->categoriesArrayFromDb as $val) {
            //si l'id passé en params = l'id d'un parent
            if ($val["parent_id"] == $parent) {
                $ids[] = $val["id"] . ","; //on injecte ces ids dans le tableau
                $this->getChildIds($val["id"]); // on recommence la boucle
            }
            return $ids;
        }

    }
}