<?php


namespace App\Service;

use App\Repository\ProductRepository;

class Cart
{

    public function __construct(private readonly ProductRepository $productRepository){

    }


    public function getCart($session):array{
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

         return [
            'cart' => $cartWithData,
            'total' => $total
         ];
 
    }

}