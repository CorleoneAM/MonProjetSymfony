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

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @Route("/api/book")
 */
class ApiBookController extends AbstractController
{
  

    private $repo ;

    public function __construct(BookRepository $r)
    {
       $this->repo=$r; 
    }  

    /**
     * @Route("/", name="api_book_index", format="json", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        $serializer = new Serializer ([new ObjectNormalizer()]);
        foreach ($bookRepository->findAll() as $book) {
            $books[] = $serializer->normalize($book, null, [AbstractNormalizer::ATTRIBUTES => ['title','Author', 'price', 'category' => ['name'], 'image']]); // au lieu de le faire manuelle $users[]= ['name'=> $user->gatName(),...] if faut convertir arraylist(resultat find all) to array et object user to array cepourcela on utilise serialization
        }
        
         return new JsonResponse($books);
     }

      /**
     * @Route("/new", name="api_book_new", methods={"POST"})
     */
    public function new(Request $request) : Response
{
    
    $book=new Book();
    $options = array('csrf_protection' => false);
    $form = $this->createFormBuilder($book,$options)
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
        ->add('image',  TextType::class, [
            'attr'=>[
                'placeholder' => 'Your author..',
                'style' =>'width: 100%; padding: 12px 20px;
                margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                 border-radius: 4px; box-sizing: border-box;'
            ]
        ])

        ->getForm();

        $form->handleRequest($request);
        $form->submit($request->request->all());
        $serializer = new Serializer([new ObjectNormalizer()]);

        if ($form->isValid()) {

         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->persist($book);
         $entityManager->flush();
         
         $book_json = $serializer->normalize($book, null, [AbstractNormalizer::ATTRIBUTES => ['title','Author', 'price', 'category' => ['name'], 'image']]);
         
         return new JsonResponse($book_json);
     }

     return new JsonResponse($serializer->normalize($form->getErrors()));
    }

 }
   

