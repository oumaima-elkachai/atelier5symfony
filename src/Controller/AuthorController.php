<?php

namespace App\Controller;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinmaxType;
use App\Form\SearchType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{

    public $authors = array(
        array('id' => 1, 'picture' => '/images/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
        array('id' => 2, 'picture' => '/images/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
        array('id' => 3, 'picture' => '/images/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showauthor/{name}', name: 'app_showauthor')]
    public function showauthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'name'=>$name
        ]);
    }
    #[Route('/showtableauthor', name: 'showtableauthor')]
    public function showtableauthor(): Response
   {
        return $this->render('author/showtableauthors.html.twig', [
            'authors' => $this->authors//apelle du tableau
        ]);
    }

    #[Route('/showbyidauthors/{id}', name: 'showbyidauthors')]
    public function showbyidauthors($id): Response
    {
        //var_dump($id).die();
        $author = null;
        foreach ($this->authors as $authorD) {
            if($authorD['id']==$id) {

        
            $author=$authorD;
        }
         }
         //var_dump($author).die();
        return $this->render('author/showbyidauthors.html.twig', [
        'author' => $author
         
            
           
        ]);

    }
    #[Route('/showdb', name: 'app_showdb')]
    public function showdbauthor(AuthorRepository $authorRepository,Request $req): Response
    { //$author=$authorRepository->findall();
         $author=$authorRepository->orderbyEMAIL();
             //$author=$authorRepository->searchwithalph();
       //$form=$this->createForm(SearchType::class);
       /****************************** */
        //$form=$this->createForm(MinmaxType::class);
               /****************************** */


       //$form->handleRequest($req);
       //if($form->isSubmitted()){
               /****************************** */

        //$datainput=$form->get('username')->getData();
        //var_dump($datainput) .die();
               /****************************** */
              // $min=$form->get('min')->getData();
               //$max=$form->get('max')->getData();
               /****************************** */


        //$authorsnew=$authorRepository->searchbyusername($datainput);
                       /****************************** */
        //$authorsnew = $authorRepository->minmax($min,$max);
        //return $this->renderForm('author/showdb.html.twig', [
            //'author'=>$authorsnew,
           // 'f'=>$form
            //'minmax'=>$form

            
       // ]);

      // }

        return $this->render('author/showdb.html.twig', [
            'author'=>$author,
            //'f'=>$form

            
        ]);
    }

    #[Route('/DeleteDQL', name:'DD')]
    function DeleteDQL(AuthorRepository $repo){
        $repo->DeleteAuthor();
        return $this->redirectToRoute('app_showdb');
    }

    #[Route('/showidauthor/{id}', name: 'showidauthor')]
    public function showidauthor($id,AuthorRepository $authorRepository): Response
    {$author=$authorRepository->showbyidauthor($id);
        return $this->render('author/showidauthor.html.twig', [
            'authors' => $author
        ]);
    }

    #[Route('/addauthor', name: 'addauthor')]
    public function addauthor(ManagerRegistry $managerRegistry): Response
    { $x=$managerRegistry->getManager();
        $author=new Author();
        $author->setUsername("3a54new");
        $author->setEmail("3a54new@esprti.tn");
        $x->persist($author);
        $x->flush();
        return new Response("great add");
        
    }
    #[Route('/addformauthor', name: 'addformauthor')]
    public function addformauthor( ManagerRegistry $managerRegistry,Request $req): Response
    { 
        $x=$managerRegistry->getManager();//appelle manager registry ili yaamel delete wala mise a jour
        $author=new Author();//instance 
        $form = $this->createForm(AuthorType::class,$author);
        $form->handleRequest($req);
        
        if($form->isSubmitted() and $form->isValid() ){
        $x->persist($author);
        $x->flush();
        return $this->redirectToRoute('app_showdb');
        }
        return $this->renderForm('author/addformauthor.html.twig', [
            
            'f'=>$form
        ]);
    }

    #[Route('/editauthor /{id}', name: 'editauthor')]
    public function editauthor( $id,ManagerRegistry $managerRegistry,Request $req, AuthorRepository $authorRepository): Response   
     {{$x= $managerRegistry->getManager();
        $detaid=$authorRepository->find($id);
        //var_dump($detail).die
        $form = $this->createForm(AuthorType::class,$detaid);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid() ){
            $x->persist($detaid);
            $x->flush();
            return $this->redirectToRoute('app_showdb');
        }
        return $this->renderForm('author/editauthor.html.twig', [
            'form'=>$form        ]);
    }
    }

    #[Route('/deletauthor/{id}', name: 'deletauthor')]
    public function deletauthor( $id,ManagerRegistry $managerRegistry,Request $req, AuthorRepository $authorRepository): Response   
     {{$x= $managerRegistry->getManager();
        $detaid=$authorRepository->find($id);
        $x->remove($detaid);
        //var_dump($detail).die
        
            $x->flush();
            return $this->redirectToRoute('app_showdb');
        }
    
    }

    
    }
    
  
   

