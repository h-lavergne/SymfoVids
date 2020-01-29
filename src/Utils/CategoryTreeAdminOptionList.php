<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{

    //la fonction abstraite de la classe parent
    public function getCategoryList(array $categoriesArray, int $repeat = 0){

        foreach ($categoriesArray as $value){
            //rempli la categoryList pour les display ensuite
            //str_repeat return la chaine de carac passée en 1er param, multiplié par le 2eme param
            $this->categoryList[] = ["name" => str_repeat("-",$repeat) . $value["name"], "id" => $value["id"]];

            //si il y a des enfants
            if (!empty($value["children"])){
                //on commence a set des "--" devant les enfants et les enfants des enfants etc
                $repeat = $repeat + 2;
                $this->getCategoryList($value["children"], $repeat);
                $repeat = $repeat -2 ;
            }
        } return $this->categoryList;
    }
}