<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function search($ref)
    {
        return $this->createQueryBuilder('ra')
        ->where('ra.ref=:ref')
        ->setParameter('ref',$ref)
        ->getQuery()
        ->getResult();
    }
    public function trie()
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->orderBy('a.username', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function livrepub()

    {return $this->createQueryBuilder('book')
    ->join('book.author', 'author')
    ->Where('author.nb_books > 35')
    ->andWhere('book.publicationDate < :date')
    ->groupBy('author.nb_books')
    ->setParameter('date', new \DateTime('2023-01-01'))
    ->getQuery()
    ->getResult();
    }
public function editw()
   { return $this->createQueryBuilder('book')
            ->innerJoin('book.author', 'author')
            ->where('author.username = :authorName')
            ->setParameter('authorName', 'wiliam Shakespear')
            ->getQuery()
            ->getResult();
   }
   public function sumCategory()
   {
       $entityManager = $this->getEntityManager();
       $query=$entityManager->createQuery(

         "SELECT COUNT(b.ref) as total
               FROM  App\Entity\Book b
               WHERE b.category = :category");

    
       $query->setParameter('category', 'Science-Fiction');

       return $query->getSingleScalarResult();
   }
   public function afficherliste ()

  { $a = $this->getEntityManager();
        $query = $a->createQuery(
            'SELECT book
             FROM App\Entity\Book book
             WHERE book.publicationDate between :startDate and :endDate'
        );
        $query->setParameter('startDate', '2014-01-01');
        $query->setParameter('endDate', '2018-12-31');
        return $query->getResult();
    }

    public function minmax($minNumber,$maxNumber)
    {
        {
            $A = $this->getEntityManager();
            $query = $A->createQuery(
                'SELECT book
                FROM App\Entity\Book book
                JOIN book.author author
                where author.nb_books between :minNumber  and :maxNumber
                GROUP BY author.nb_books
            ');
    
    $query->setParameter('minNumber', $minNumber);
    $query ->setParameter('maxNumber', $maxNumber);
              return  $query->getResult();
        }
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}