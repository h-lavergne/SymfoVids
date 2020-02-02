<?php

namespace App\Utils;

use App\Twig\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract {

    public $html_1 = '<ul>';
    public $html_2 = '<li>';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a>';
    public $html_6 = '</li>';
    public $html_7 = '</ul>';

    public function getCategoryListAndParent(int $id): string
    {
        $this->slugger = new AppExtension; // Twig extension to slugify url's for categories
        $parentData = $this->getMainParent($id); // main parent of subcategory
        $this->mainParentName = $parentData['name']; // for accesing in view
        $this->mainParentId = $parentData['id']; // for accesing in view
        $key = array_search($id, array_column($this->categoriesArrayFromDb,'id'));
        $this->currentCategoryName = $this->categoriesArrayFromDb[$key]['name']; // for accesing in view
        $categories_array = $this->buildTree($parentData['id']); // builds array for generating nested html list
        return $this->getCategoryList($categories_array);
    }

    public function getCategoryList(array $categories_array)
    {
        $this->categoryList .= $this->html_1;
        foreach ($categories_array as $value)
        {
            $catName = $this->slugger->slugify($value['name']);

            $url = $this->generator->generate('video_list', ['name'=>$catName, 'id'=>$value['id']]);
            $this->categoryList .= $this->html_2 . $this->html_3 . $url . $this->html_4 . $value['name'] . $this->html_5;
            if(!empty($value['children']))
            {
                $this->getCategoryList($value['children']);
            }
            $this->categoryList .= $this->html_6;

        }
        $this->categoryList .= $this->html_7;
        return $this->categoryList;
    }

    public function getMainParent(int $id): array
    {
        $key = array_search($id, array_column($this->categoriesArrayFromDb, 'id'));
        if($this->categoriesArrayFromDb[$key]['parent_id'] != null)
        {
            return $this->getMainParent($this->categoriesArrayFromDb[$key]['parent_id']);
        }
        else
        {
            return [
                'id'=>$this->categoriesArrayFromDb[$key]['id'],
                'name'=>$this->categoriesArrayFromDb[$key]['name']
            ];
        }
    }


    public function getChildIds(int $parent): array
    {
        static $ids = [];
        foreach($this->categoriesArrayFromDb as $val)
        {
            if($val['parent_id'] == $parent)
            {
                $ids[] = $val['id'].',';
                $this->getChildIds($val['id']);
            }
        }

        return $ids;
    }

}
