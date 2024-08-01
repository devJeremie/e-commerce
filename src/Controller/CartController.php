<?php

namespace App\Controller;

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
    
  public function index(SessionInterface $session): Response
    {
        // Récupère les données du panier en session, ou un tableau vide si il n'y a rien
        $cart = $session->get('cart', []);
        // Initialisation d'un tableau pour stocker les données du panier avec les informations de produit
        $cartWithData = [];
        // Boucle sur les éléments du panier pour récupérer les informations de produit
        foreach ($cart as $id => $quantity) {
            // Récupère le produit correspondant à l'id et la quantité
            $cartWithData[] = [
                'product' => $this->productRepository->find($id), // Récupère le produit via son id
                'quantity' => $quantity // Quantité du produit dans le panier
            ];
        }

        // Calcul du total du panier
        $total = array_sum(array_map(function ($item) {
            // Pour chaque élément du panier, multiplie le prix du produit par la quantité
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        //dd($cartWithData);

        // Rendu de la vue pour afficher le panier
        return $this->render('cart/index.html.twig', [
            'items'=>$cartWithData, //on retourne ses deux variable afin de les récuperer dans la vue
            'total'=>$total
           
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
    public function removeToCart($id, SessionInterface $sessionInterface): Response
    {
         // Récupération du contenu du panier en session, ou initialisation à un tableau vide si il n'existe pas
        $cart = $sessionInterface->get('cart', []);
        // Vérification si le produit à supprimer existe dans le panier
        if (!empty($cart[$id])) {
            // Suppression du produit du panier
            unset($cart[$id]);
        }
        // Mise à jour du contenu du panier en session
        $sessionInterface->set('cart', $cart);
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route("/cart/remove", name: "app_cart_remove", methods: ['GET'])]
    public function remove(SessionInterface $sessionInterface): Response
    {
        // Mise à jour du contenu du panier en session
        $sessionInterface->set('cart', []);
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }
}
