<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;//tetsnaa maa classe
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;//c'est le chef d'orcestre de symfony 

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()//tkhalik tlawej fel bd fi ooudh les requettes eql
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function orderbyEMAIL()
    {
        return $this->createQueryBuilder('a')
        ->orderBy('a.email','Asc')
        ->getQuery()
        ->getResult();
    }
    public function searchwithalph()
    {
        return $this->createQueryBuilder('a')
        ->where('a.username Like :username')
        ->setParameter('username','r%')
        ->getQuery()
        ->getResult();
    }

    public function showbyidauthor($id)
    {return $this->createQueryBuilder('a')
     ->join('a.books' , 'b')//books mtaa books.php win inver
     ->addSelect('b')
     ->where('b.author=:id')//author many to one mtaa author.php
     ->setParameter('id',$id)
     ->getQuery()
     ->getResult();
    

    }
    public function searchbyusername($username)  {

        return $this->createQueryBuilder('a')
        ->where('a.username Like :username')
        ->setParameter('username',$username)
        ->getQuery()
        ->getResult();
    }

    public function minmax($min,$max)
    {$em=$this->getEntityManager();
     return $em->createQuery('SELECT a from App\Entity\Author a where a.nb_books BETWEEN ?1 and :max')
       ->setParameters(['1'=>$min,'max'=>$max])
       ->getResult();
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
