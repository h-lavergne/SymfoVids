<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use function foo\func;

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
        if ($sort_method != "rating"){
            $dbQuery = $this->createQueryBuilder('v')
                ->andWhere("v.category IN (:val)")//cherche si v.category existe dans le tableau d'id as values
                ->leftJoin("v.comments", "c")
//                ->addSelect("c")
                ->leftJoin("v.usersThatLike", "l")
                ->leftJoin("v.usersThatDontLike", "d")
                ->addSelect("c", "l", "d")
                ->setParameter('val', $value)//bind val
                ->orderBy("v.title", $sort_method); // sql orderBy
        }
        else {
            $dbQuery = $this->createQueryBuilder('v')
                ->addSelect("COUNT(l) AS HIDDEN likes")//cherche si v.category existe dans le tableau d'id as values
                ->leftJoin("v.usersThatLike", "l")
                ->andWhere("v.category IN (:val)")
                ->setParameter("val", $value)
                ->groupBy("v")
                ->orderBy("likes", "DESC");
        }

        $dbQuery->getQuery();


        $pagination = $this->paginator->paginate($dbQuery, $page, Video::perPage);

        return $pagination;
    }

    public function findByTitle(string $query, int $page, ?string $sort_method)
    {
        //filtre si ce n'est pas par rating alors par ASC
        $queryBuilder = $this->createQueryBuilder("v");
        $searchTerms = $this->prepareQuery($query);

        //pour chaque element du tableau créés dans prepareQuery
        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere("v.title LIKE :t_" . $key)//cherche element dans title des videos
                    //assigne a "t_" -> trim(term), % -> joker = n'importe quel caractere
                ->setParameter("t_" . $key, "%" . trim($term) . '%');//trim supprime les espaces en debut et fin de chaine
        }

        if ($sort_method != "rating") {
            $dbQuery = $queryBuilder
                ->orderBy("v.title", $sort_method)
                ->leftJoin("v.comments", "c")
                ->leftJoin("v.usersThatLike", "l")
                ->leftJoin("v.usersThatDontLike", "d")
                ->addSelect("c", "l", "d")
                ->getQuery();
        }
        else {
            $dbQuery = $queryBuilder
                ->addSelect("COUNT(l) AS HIDDEN likes", "c")
                ->leftJoin("v.usersThatLike", "l")
                ->leftJoin("v.comments", "c")
                ->groupBy("v", "c")
                ->orderBy("likes", "DESC")
                ->getQuery();
        }

        return $this->paginator->paginate($dbQuery, $page, Video::perPage);

    }

    private function prepareQuery(string $query): array
    {
        //va creer un tableau avec chaque caractere delimité par un espace
        $terms = array_unique(explode(" ", $query));
        return array_filter($terms, function ($term){
            return 2 <= mb_strlen($term);
        });
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
