<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);
        $this->paginator = $paginator;
    }


    public function findByChildIds(array $value, int $page, ?string $sort_method)
    {

        //filtre si ce n'est pas par rating alors par ASC
        $sort_method = $sort_method != "rating" ? $sort_method : "ASC";

        $dbQuery = $this->createQueryBuilder('v')
            ->andWhere("v.category IN (:val)")//cherche si v.category existe dans le tableau d'id as values
            ->setParameter('val', $value)//bind val
            ->orderBy("v.title", $sort_method) // sql orderBy
            ->getQuery();

        $pagination = $this->paginator->paginate($dbQuery, $page, 5);

        return $pagination;
    }

    public function findByTitle(string $query, int $page, ?string $sort_method)
    {
        //filtre si ce n'est pas par rating alors par ASC
        $sort_method = $sort_method != "rating" ? $sort_method : "ASC";
        $queryBuilder = $this->createQueryBuilder("v");
        $searchTerms = $this->prepareQuery($query);

        //pour chaque element du tableau créés dans prepareQuery
        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere("v.title LIKE :t_" . $key)//cherche element dans title des videos
                    //assigne a "t_" -> trim(term), % -> joker = n'importe quel caractere
                ->setParameter("t_" . $key, "%" . trim($term) . '%');//trim supprime les espaces en debut et fin de chaine
            dump($term);
        }


        $dbQuery = $queryBuilder
            ->orderBy("v.title", $sort_method)
            ->getQuery();

        return $this->paginator->paginate($dbQuery, $page, 5);

    }

    private function prepareQuery(string $query): array
    {
        //va creer un tableau avec chaque caractere delimité par un espace
        return explode(' ', $query);
    }


    public function videoDetails($id)
    {
        return $this->createQueryBuilder("v")
            ->leftJoin("v.comments", "c")
            ->leftJoin("c.user", "u")
            ->addSelect("c", "u")
            ->where("v.id = :id")
            ->setParameter("id", $id)
            ->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Video[] Returns an array of Video objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
