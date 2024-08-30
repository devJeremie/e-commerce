<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, 
                          ProductRepository $productRepository, 
                          EntityManagerInterface $entityManager,
                          Cart $cart): Response
    {

        //  // Récupère les données du panier en session, ou un tableau vide si il n'y a rien
        //  $cart = $session->get('cart', []);
        //  // Initialisation d'un tableau pour stocker les données du panier avec les informations de produit
        //  $cartWithData = [];
        //  // Boucle sur les éléments du panier pour récupérer les informations de produit
        //  foreach ($cart as $id => $quantity) {
        //      // Récupère le produit correspondant à l'id et la quantité
        //      $cartWithData[] = [
        //          'product' => $productRepository->find($id), // Récupère le produit via son id
        //          'quantity' => $quantity // Quantité du produit dans le panier
        //      ];
        //  }
 
        //  // Calcul du total du panier
        //  $total = array_sum(array_map(function ($item) {
        //      // Pour chaque élément du panier, multiplie le prix du produit par la quantité
        //      return $item['product']->getPrice() * $item['quantity'];
        //  }, $cartWithData));

        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($order->isPayOnDelivery()) {
                //dd($order);
                $order->setTotalPrice($data['total']);
                $order->setCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($order);
                $entityManager->flush();
            }
        }

        return $this->render('order/index.html.twig', [
            'form'=>$form->createView(),
            'total'=>$data['total'],
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status'=>200, "message"=>'on', 'content'=> $cityShippingPrice]));
    }
}
