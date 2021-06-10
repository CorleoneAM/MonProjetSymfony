<?php

namespace App\Controller;
use App\Entity\Book;
use App\Entity\Search;
use App\Entity\Category;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class BookController extends AbstractController
{
  

    private $repo ;

    public function __construct(BookRepository $r)
    {
       $this->repo=$r; 
    }  
      /**
     * @Route("/", name="book.index")
     */
    public function index()
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @Route("/list", name="book.list")
     */
    public function list(PaginatorInterface $paginator, Request $request):Response
    {
        $book=$this->getDoctrine()->getRepository(Book::class)->findAll();
        $books=$paginator->paginate(
            $book,
            $request->query->getInt('page',1),
            8
        );
        return $this->render('book/list.html.twig', [
            'books' => $books,
            
        ]);
    }
    /**
     * @Route("/search/", name="book.search.first")
     */
    public function search(Request $request):Response
    {
        $search = new search();
        $form =$this->createForm(SearchType::class, $search);

        
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $search =$form->getData();
                return $this->redirectToRoute('book.search', ['name' 
=> $search.getName()]);
    }
    return $this->render('search/search.html.twig', [
        'form' => $form->createView(),
    ]);
    }
     /**
     * @Route("/search/{name}", name="book.search")
     */
    public function search_ok(PaginatorInterface $paginator, Request $request, $name):Response
    {
        
        
        $book=$this->getDoctrine()->getRepository(Book::class)->findbyname($name);
        $books=$paginator->paginate(
            $book,
            $request->query->getInt('page',1),
            8
        );
        return $this->render('book/list.html.twig', [
            'books' => $books,
            
        ]);
    }

    /**
     * @Route("/show/{id}", name="book.show")
     */
    public function show($id)
    {
        
        $books=$this->getDoctrine()->getRepository(Book::class)->find($id);
       // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (!$books) {
            throw $this->createNotFoundException(
                'Le livre de id :   '.$id. 'est inexistant...'
            );
        }
       
        return $this->render('book/show.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="book_delete")
     */
    public function delete($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $book=$this->getDoctrine()->getRepository(Book::class)->find($id);
      
        if (!$book) {
            throw $this->createNotFoundException(
                'Le livre de id :   '.$id. 'est inexistant...'
            );
        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();
        $this->addFlash(
            'del',
            'Product deleted successfully !'
        );

        
        return $this->redirectToRoute('book.list');
    }

     




    /**
     * @Route("/formadd", name="book_formadd")
     */
    public function formadd(Request $request)
    {
        $book=new Book();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser(); 
        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class , [
                'attr'=>[
                    'placeholder' => 'Your title..',
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('price', IntegerType::class , [
                'attr'=>[
                    'placeholder' => 'Your price..',
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('Author', TextType::class,  [
                'attr'=>[
                    'placeholder' => 'Your author..',
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,

                // uses the User.username property as the visible option string
                //'choice_label' => 'name',
                'choice_label' => 'name',
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Svp choisir un fichier de type jpeg ou png',
                    ])
                ],
            ])

            
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                ///////////////////////////////////////////////////
                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('image')->getData();
                var_dump($brochureFile);
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $originalFilename;
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
    
                    // Move the file to the directory where brochures are stored
                    try {
                        $brochureFile->move(
                            $this->getParameter('image_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $book->setImage($newFilename);
            }

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($book);
             $entityManager->flush();
             $this->addFlash(
                'succe',
                'Product added successfully !'
            );

             return $this->redirectToRoute('book.list');
         }

        return $this->render('book/formadd.html.twig', [
            'form' => $form->createView(),
            'book' => $book,'firstname'=>$user->getUsername(),
            'txtbtn'=>'Create Book'
        ]);
    }




/**
 * @Route("/update/{id}", name="book_update")
 */

    public function update(Book $book, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $form = $this->createFormBuilder($book)
            ->add('title', TextType::class , [
                'attr'=>[
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('price', IntegerType::class , [
                'attr'=>[
                    
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('Author', TextType::class ,  [
                'attr'=>[
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('category', EntityType::class, [
                
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Svp choisir un fichier de type jpeg ou png',
                    ])
                ],
            ])

            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                ///////////////////////////////////////////////////
                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('image')->getData();
                var_dump($brochureFile);
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $originalFilename;
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                        try {
                        $brochureFile->move(
                            $this->getParameter('image_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }
                    $book->setImage($newFilename);
            }

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->flush();
             $this->addFlash(
                'updt',
                'Product updated successfully !'
            );
 
             return $this->redirectToRoute('book.list');
         }

        return $this->render('book/formadd.html.twig', [
            'form' => $form->createView(),
            'txtbtn'=>'Update Book'
        ]);
    }

}
