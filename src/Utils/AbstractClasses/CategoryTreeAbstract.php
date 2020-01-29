<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    public $categoriesArrayFromDb;
    public $categoryList;
    protected static $dbConnection;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
        $this->categoriesArrayFromDb = $this->getCategories();
    }


    abstract public function getCategoryList(array $categoriesArray);

    public function buildTree(int $parent_id = null): array {
        $subcategories = [];
        foreach ($this->categoriesArrayFromDb as $category) {

            if ($category["parent_id"] == $parent_id) {
                $children = $this->buildTree($category["id"]);

                if ($children){
                    $category["children"] = $children;
                }

                $subcategories[] = $category;
            }
         }
        return $subcategories;
    }

    private function getCategories(): array {
        if (self::$dbConnection){
            return self::$dbConnection;
        }
        else {
            $conn = $this->em->getConnection();
            $sql = "SELECT * FROM categories";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return self::$dbConnection = $stmt->fetchAll();
        }

    }
}