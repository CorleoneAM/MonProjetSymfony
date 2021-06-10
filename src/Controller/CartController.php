<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Cart\CartService;
class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(SessionInterface $session, BookRepository $bookRepository)
    {
       $panier = $session->get('panier', []);
       $panierWithData= [];
       foreach($panier as $id =>$quantity)
       {
           $panierWithData[]=[
               'book'=> $bookRepository->find($id),
               'quantity' =>$quantity
           ];
       }
       $total=0;
       foreach($panierWithData as $item){
           $totalItem =$item['book']->getPrice() * $item['quantity'];
            $total +=$totalItem;
        }
        return $this->render('cart/index.html.twig', [
           'items' => $panierWithData,
           'total' => $total
        ]);
    }




     /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id  , CartService $cartservice){
        $cartservice->add($id);

        return $this->redirectToRoute("cart_index");
    }


     /**
     * @Route("panier/remove/{id}" , name="cart_remove")
     */
    public function remove($id ,  CartService $cartservice)
    {
        $cartservice->remove($id);

        return $this->redirectToRoute("cart_index");
    }
}
