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


    // fonction a implementer dans les classes enfants
    abstract public function getCategoryList(array $categoriesArray);

    public function buildTree(int $parent_id = null): array {
        //initialise un tableau vide
        $subcategories = [];

        foreach ($this->categoriesArrayFromDb as $category) {
            //si l'id parent de la category == id fournit en argument alors ...
            if ($category["parent_id"] == $parent_id) {
                //reprend la fonction pour créer un arbre dans un arbre avec l'id de la catégorie
                $children = $this->buildTree($category["id"]);

                //si il y a une catégorie alors..
                if ($children){
                    $category["children"] = $children;
                }

                //on assigne au tableau la liste des categories  avec leurs enfants
                $subcategories[] = $category;
            }
         }
        return $subcategories;
    }

    // effectue simplement une requete SQL et return toutes les categoriesx²
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