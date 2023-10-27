<?php

namespace App\Controller;



use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Form\Search2Type;




use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

   



    #[Route('/addformbook', name: 'addformbook')]
    public function addformbook( ManagerRegistry $managerRegistry,Request $req): Response
    { 
        $x=$managerRegistry->getManager();//appelle manager registry ili yaamel delete wala mise a jour
        $book=new Book();//instance 
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($req);//envoi d'une requete
        
        if($form->isSubmitted() and $form->isValid() ){
            $author = $book->getAuthor();//donner privé pour nbbook
            if ($book->isPublished()) { 
                $author->setNbBooks($author->getNbBooks() + 1);
            } 
         $x->persist($author);
        //$x = $this->getDoctrine()->getManager();
        $x->persist($book);
        $x->persist($author);

        $x->flush();
        return $this->redirectToRoute('app_showdbbook');
        }
        return $this->renderForm('book/addformbook.html.twig', [
            
            'f'=>$form
        ]);
    }

    #[Route('/editBook /{ref}', name: 'editBook')]
    public function editbook( $ref,ManagerRegistry $managerRegistry,Request $req, BookRepository $BookRepository): Response   
     {{$x= $managerRegistry->getManager();
        $detaid=$BookRepository->find($ref);
        //var_dump($detail).die
        $form = $this->createForm(BookType::class,$detaid);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid() ){
            $author = $detaid->getAuthor();
            if (!$detaid->isPublished()) { 
                $author->setNbBooks($author->getNbBooks() - 1);
            }
            $x->persist($detaid);
            $x->flush();
            return $this->redirectToRoute('app_showdbbook');
        }
        return $this->renderForm('book/editBook.html.twig', [
            'form'=>$form        ]);
    }
    }

    #[Route('/deletbook/{ref}', name: 'deletbook')]
    public function deletbook( $ref,ManagerRegistry $managerRegistry,Request $req, BookRepository $BookRepository): Response   
     {{$x= $managerRegistry->getManager();
        $detaid=$BookRepository->find($ref);
        $author = $detaid->getAuthor();
              
                $author->setNbBooks($author->getNbBooks() - 1);
            
        $x->remove($detaid);
        //var_dump($detail).die
        
            $x->flush();
            return $this->redirectToRoute('app_showdbbook');
        }
    
    }


    
    #[Route('/showbyidBook/{ref}', name: 'showbyidBook')]
    public function showidbyauthor($ref,BookRepository $BookRepository , ManagerRegistry $managerRegistry): Response
    {
        $em=$managerRegistry->getManager();
        $book = $BookRepository->find($ref);
        $em->persist($book);
        $em->flush();
        return $this->render('book/showbyidBook.html.twig', [
            'book' => $book,
        ]);
    }
    #[Route('/showdbbook', name: 'app_showdbbook')]
    public function publishedBooks(BookRepository $bookRepository,Request $req): Response
    {
       
        // Récupérez la liste des livres publiés
        $book = $bookRepository->findBy(['published' => true]);

       $form=$this->createForm(Search2Type::class);
       $form->handleRequest($req);
       if($form->isSubmitted()){
        $ref=$form->get('ref')->getData();
        $book= $bookRepository->search($ref);
        $books= $bookRepository->trie($username = null);

       }
         if ($book === null) {
            throw $this->createNotFoundException('Aucun Livre.');
        }
        
        $publishedCount = $bookRepository->count(['published' => true]);
        $unpublishedCount = $bookRepository->count(['published' => false]);

        return $this->renderForm('book/showdbbook.html.twig', [
            'book' => $book,
            'f'=>$form,
            
            
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
        return $this->render('book/showdbbook.html.twig', [
            'books' => $books,
            
            
            
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
    }

    #[Route('/deleteZeroBooks', name: 'deleteZeroBooks')]
    public function deleteZeroBooks(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $authorRepository = $entityManager->getRepository(Author::class);
        $bookRepository = $entityManager->getRepository(Book::class);

        // Récupérer la liste des auteurs avec nb_books égal à zéro
        $authorsToDelete = $authorRepository->findBy(['nb_books' => 0]);

        foreach ($authorsToDelete as $author) {
            // Retrieve the associated books
            $books = $bookRepository->findBy(['author' => $author]);

            foreach ($books as $book) {
                $entityManager->remove($book);
            }

            $entityManager->remove($author);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_showdbbook');
    }
}
