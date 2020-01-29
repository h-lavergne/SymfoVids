<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{

    // set le html en variable pour changer facilement si besoin

    public $html_1 = '<ul class="fa-ul text-left mb-5">';
    public $html_2 = '<li><i class="fa-li fa fa-arrow-right"></i>';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a> <a onclick="return confirm(\' Are you sure ? \')" href="';
    public $html_6 = '">';
    public $html_7 = '</a>';
    public $html_8 = '</li>';
    public $html_9 = '</ul>';


    //implemente la fonction abstraite de la classe parent -> set le html pour le display --SIMPLE HTML--
    public function getCategoryList(array $categoriesArray)
    {
        $this->categoryList .= $this->html_1;

        foreach ($categoriesArray as $value) {

            $url_edit = $this->generator->generate("edit_category", ["id" => $value["id"]]);
            $url_delete = $this->generator->generate("delete_category", ["id" => $value["id"]]);

            $this->categoryList .= $this->html_2 . $value["name"] .
                $this->html_3 . $url_edit . $this->html_4 . " Edit" .
                $this->html_5 . $url_delete . $this->html_6 . "Delete" . $this->html_7;

            if (!empty($value["children"])) {
                $this->getCategoryList($value["children"]);
            }
            $this->categoryList .= $this->html_8;

        }
        $this->categoryList .= $this->html_9;
        return $this->categoryList;
    }
}