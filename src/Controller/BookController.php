<?php

namespace App\Controller;



use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Form\Search2Type;
use App\Form\MaxminNumberType;




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
        $t=$bookRepository->sumCategory();

       $form=$this->createForm(Search2Type::class);
     
       $form->handleRequest($req);
       if($form->isSubmitted()){
        $ref=$form->get('ref')->getData();
        $book= $bookRepository->search($ref);
       

       }
         if ($book === null) {
            throw $this->createNotFoundException('Aucun Livre.');
        }

        $book = $bookRepository->trie();
        
        
        
        $publishedCount = $bookRepository->count(['published' => true]);
        $unpublishedCount = $bookRepository->count(['published' => false]);

        return $this->renderForm('book/showdbbook.html.twig', [
            'book' => $book,
            'f'=>$form,
            't'=>$t,

            
            
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
        return $this->render('book/showdbbook.html.twig', [
            'book' => $book,
            't'=>$t,
            
            
            
            
            
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);

        

        
       
    }

    #[Route('/list', name: 'list')]
    public function list(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->livrepub();

        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/ediit', name: 'ediit')]
    public function ediit(BookRepository $bookRepository, ManagerRegistry $managerRegistry)
    {
        $entityManager =$managerRegistry->getManager();

        $x = $bookRepository->editw();

        foreach (  $x  as $book) {
            $book->setCategory('Romance');
            $entityManager->persist($book);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_showdbbook'); // Redirect to the list of books or another appropriate route
    }

    #[Route('/afficherliste', name: 'afficherliste')]
    public function afficherliste(BookRepository $bookRepository)
    {
        $books = $bookRepository->afficherliste();

        return $this->render('book/afficherliste.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/minmax', name: 'minmax')]
    #[Route('/minmaxNumber', name: 'minmaxNumber')]
    public function minmax(Request $request, BookRepository $bookRepository): Response
    {
        $form = $this->createForm(MaxminNumberType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $minNumber = $data['minNumber'];
            $maxNumber = $data['maxNumber'];

            $books = $bookRepository->minmax($minNumber, $maxNumber);
            return $this->render('book/minmax.html.twig', [
                'book' => $books,
            ]);
        }

        return $this->renderForm('book/minmaxNumber.html.twig', [
            'minmaxNumber' => $form,
        ]);
    }
    


   
    
}
