<?php

namespace App\Controller;


use App\Service\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    public function __construct(private readonly ProductRepository $productRepository)
    {
        
    }

 
  #[Route("/cart", name: "app_cart", methods: ['GET'])]
    
  public function index(SessionInterface $session, Cart $cart): Response
    {
       
        $data = $cart->getCart($session);

        // Rendu de la vue pour afficher le panier
        return $this->render('cart/index.html.twig', [
            'items'=>$data['cart'], //on retourne ses deux clés afin de les récuperer dans la vue
            'total'=>$data['total']
           
        ]);
    }

    #[Route("/cart/add/{id}/", name: "app_cart_new", methods: ['GET'])]
    // Définit une route pour ajouter un produit au panier

    public function addProductToCart(int $id, SessionInterface $session): Response
    // Méthode pour ajouter un produit au panier, prend l'ID du produit et la session en paramètres

    {
        $cart = $session->get('cart', []);
        // Récupère le panier actuel de la session, ou un tableau vide si il n'existe pas
        if (!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1;
        }
        // Si le produit est déjà dans le panier, incrémente sa quantité, sinon l'ajoute avec une quantité de 1
        $session->set('cart',$cart);
        // Met à jour le panier dans la session
        return $this->redirectToRoute('app_cart');
        // Redirige vers la page du panier
    }

    #[Route("/cart/remove/{id}/", name: "app_cart_product_remove", methods: ['GET'])]
    public function removeToCart($id, SessionInterface $session): Response
    {
         // Récupération du contenu du panier en session, ou initialisation à un tableau vide si il n'existe pas
        $cart = $session->get('cart', []);
        // Vérification si le produit à supprimer existe dans le panier
        if (!empty($cart[$id])) {
            // Suppression du produit du panier
            unset($cart[$id]);
        }
        // Mise à jour du contenu du panier en session
        $session->set('cart', $cart);
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route("/cart/remove", name: "app_cart_remove", methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {
        // Mise à jour du contenu du panier en session
        $session->set('cart', []);
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }
}
