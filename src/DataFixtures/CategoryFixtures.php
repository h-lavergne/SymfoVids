<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadMainCategories($manager);
        $this->loadElectronics($manager);
    }

    private function loadMainCategories($manager){
        foreach ($this->getMainCategoriesData() as [$name]) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);

        }
        $manager->flush();
    }

    private function getMainCategoriesData(){
        return array(
            ["Electronics", 1],
            ["Toys", 2],
            ["Books", 3],
            ["Movies", 4],
        );
    }


    private function loadElectronics($manager){
        foreach ($this->getMainElectronicsData() as [$name]) {

            $parent = $manager->getRepository(Category::class)->find(1);
            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);
            $manager->persist($category);

        }
        $manager->flush();
    }

    private function getMainElectronicsData(){
        return array(
            ["Cameras", 5],
            ["Computers", 6],
            ["Cell Phones", 7],
        );
    }
}
