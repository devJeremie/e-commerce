<?php 

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePayment
{
   private $redirectUrl;

   public function __construct()
   {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']); //recupère la cle secrete dans le fichier .env gràce au $_server
        Stripe::setApiVersion('2024-06-20'); //on gère la version de Stripe
    }

    public function startPayment($cart, $shippingCost, $orderId){
        //dd($cart);
       // Récupération des produits du panier
        $cartProducts = $cart['cart']; 
        // Initialisation d'un tableau vide pour stocker les produits formatés
        $products = [
            [
                'qte' => 1, 
                'price' => $shippingCost,
                'name' => "Frais de livraison"
            ]
        ];

        // Boucle pour parcourir chaque produit du panier
        foreach ($cartProducts as $value) {
            // Initialisation d'un tableau vide pour stocker les informations d'un produit
            $productItem = [];
            // Récupération du nom du produit
            $productItem['name'] = $value['product']->getName();
            // Récupération du prix du produit
            $productItem['price'] = $value['product']->getPrice();
            // Récupération de la quantité du produit
            $productItem['qte'] = $value['quantity'];
            // Ajout du produit formaté au tableau des produits
            $products[] = $productItem;
        }

        $session = Session::create([ //création de la session Stripe
            'line_items'=>[  //produits qui vont etre payer
                // Transforme chaque produit du panier en format Stripe
                array_map(fn(array $product) => [
                    // Quantité commandée pour ce produit
                    'quantity' => $product['qte'],
                    // Données de tarification créées dynamiquement (pas de Price pré-créé)
                    'price_data' => [
                        // Devise (minuscule obligatoire : eur, usd, gbp...)
                        'currency' => 'Eur',
                        // Métadonnées du produit (affichées dans Checkout)
                        'product_data' => [
                            // Nom du produit visible dans Stripe Checkout
                            'name' => $product['name']
                        ],
                        // Prix unitaire en CENTIMES (obligatoire)
                        // Ex: 29.99€ → 2999 centimes. Stripe ne gère QUE les centimes !
                        'unit_amount' => $product['price']*100, //prix donnée en centimes donc on multiplie
                    ],
                ],$products)
            ],
            'mode' => 'payment', //mode de paiement
            'cancel_url' => 'http://localhost:8000/pay/cancel', //si paiement annulé on redirige ici
            'success_url' => 'http://localhost:8000/pay/success', //si paiement réussi
            'billing_address_collection' => 'required', //si on autorise les factures
            'shipping_address_collection' => [ //pays ou on souhaite autorise le paiement
                'allowed_countries' => ['FR','EG'],
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'orderId' =>$orderId//id de la commande
                ]
            ]
            
        ]); 

        $this->redirectUrl = $session->url; //redirection vers stripe pour le paiement

    }
    public function getStripeRedirectUrl(){ //permet de recuperer l'url de l'utilisateur pour stripe
        return $this->redirectUrl;
    }
}