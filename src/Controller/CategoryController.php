<?php

namespace App\Controller;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Book;
/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

       /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category, PaginatorInterface $paginator, Request $request): Response
    {
        $book=$this->getDoctrine()->getRepository(Book::class)->getPaginatedBooksByCategory($category);
        $ok_books=$paginator->paginate(
            $book,
            $request->query->getInt('page',1),
            8
        );
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'ok_books' => $ok_books,
        ]);
    }
}
