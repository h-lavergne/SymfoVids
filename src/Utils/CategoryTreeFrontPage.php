<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract {

    public function getCategoryList(array $categoriesArray)
    {
        $this->categoryList .='<ul>';
        foreach ($categoriesArray as $value){

            $catName = $value["name"];
            $url = $this->generator->generate("video_list", ["name" => $catName, "id" => $value["id"]]);

            $this->categoryList .= '<li>' . '<a href="' . $url . '">' . $catName  . '</a>';

            if (!empty($value["children"])){
                $this->getCategoryList($value["children"]);
            }

        }
        $this->categoryList .='</ul>';
        return $this->categoryList;
    }
}