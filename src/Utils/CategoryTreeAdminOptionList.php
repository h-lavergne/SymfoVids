<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{

    public function getCategoryList(array $categoriesArray, int $repeat = 0){

        foreach ($categoriesArray as $value){
            $this->categoryList[] = ["name" => str_repeat("-",$repeat).$value["name"], "id" => $value["id"]];

            if (!empty($value["children"])){
                $repeat = $repeat + 2;
                $this->getCategoryList($value["children"], $repeat);
                $repeat = $repeat -2 ;
            }
        } return $this->categoryList;
    }
}