<?php

namespace App\Utils;

use App\Twig\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{

    public $html_ul_open = '<ul>';
    public $html_li_open = '<li>';
    public $html_a_open = '<a href="';
    public $html_a_href = '">';
    public $html_a_close = '</a>';
    public $html_li_close = '</li>';
    public $html_ul_close = '</ul>';

    public function getCategoryListAndParent($id): string {
        $this->slugger = new AppExtension();
        $parentData = $this->getMainParent($id);
        $this->mainParentName = $parentData["name"];
        $this->mainParentId = $parentData["id"];

        $key = array_search($id, array_column($this->categoriesArrayFromDb, "id"));
        $this->currentCategoryName = $this->categoriesArrayFromDb[$key]["name"];

        $categoriesArray = $this->buildTree($parentData["id"]);

        return $this->getCategoryList($categoriesArray);
    }

    public function getCategoryList(array $categoriesArray)
    {


        $this->categoryList .= $this->html_ul_open;
        foreach ($categoriesArray as $value) {

            $catName = $this->slugger->slugify($value["name"]);
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
        $key = array_search($id, array_column($this->categoriesArrayFromDb, "id"));
        if ($this->categoriesArrayFromDb[$key]["parent_id"] != null) {
            return $this->getMainParent($this->categoriesArrayFromDb[$key]["parent_id"]);
        } else {
            return [
                "id" => $this->categoriesArrayFromDb[$key]["id"],
                "name" => $this->categoriesArrayFromDb[$key]["name"]
            ];
        }
    }
}