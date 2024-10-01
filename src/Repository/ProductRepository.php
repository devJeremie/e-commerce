<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

//find($id, $lockMode = null, $lockVersion = null)
//findOneBy(array $criteria, array $orderBy = null)
//findAll()
//findOneBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function searchEngine(string $query){
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :query')//query est ici une variable le nom est arbitraire
            ->orWhere('p.description LIKE :query')
            ->setParameter('query','%' .$query.'%')
            ->getQuery()
            ->getResult();

    }

            //methode que lon peut créer soi meme
    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByIdUp($value): array
    //    {
    //        return $this->createQueryBuilder('p') //retourner la requete
    //            ->andWhere('p.id > :val') // ajoute des critères val = $value
    //            ->setParameter('val', $value) //on set les parametres
    //            ->orderBy('p.id', 'ASC') //on definit les criteres
    //            ->setMaxResults(10) //definit le nbr de resultat
    //            ->getQuery() //
    //            ->getResult() //
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
