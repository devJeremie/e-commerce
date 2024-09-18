<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Mime\Email;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailer){
    }

    
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, 
                          ProductRepository $productRepository, 
                          EntityManagerInterface $entityManager,
                          Cart $cart
    ): Response
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

        // Récupère les données du panier à partir de la session using le service Cart
        $data = $cart->getCart($session);
        // Crée un nouvel objet Order
        $order = new Order();
        // Crée un formulaire pour gérer la création de la commande using le type de formulaire OrderType
        $form = $this->createForm(OrderType::class, $order);
        // Gère la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si la commande est une commande à payer à la livraison
            if($order->isPayOnDelivery()) {
                // Vérifie si le total du panier n'est pas vide
                if(!empty($data['total'])) {
                    // Définit le prix total de la commande
                    $order->setTotalPrice($data['total']);
                    // Définit la date de création de la commande
                    $order->setCreatedAt(new \DateTimeImmutable());
                    //dd($order);
                    $entityManager->persist($order);
                    $entityManager->flush();
                    // Boucle sur chaque élément du panier
                    foreach($data['cart'] as $value) {
                        // Crée un nouvel objet OrderProducts
                        $orderProduct = new OrderProducts();
                        // Définit la commande pour le produit de la commande
                        $orderProduct->setOrder($order);
                        // Définit le produit pour le produit de la commande
                        $orderProduct->setProduct($value['product']);
                        // Définit la quantité pour le produit de la commande
                        $orderProduct->setQuantity($value['quantity']);
                        // Enregistre le produit de la commande dans la base de données
                        $entityManager->persist($orderProduct);
                        $entityManager->flush();
                    }
                }
               
                // Mise à jour du contenu du panier en session
                $session->set('cart', []);

                $html = $this->renderView('mail/orderConfirm.html.twig',[ //crée une vue mail
                    'order'=>$order //on recupere le $order apres le flush donc on a toutes les infos
                ]);
                $email = (new Email()) //On importe la classe depuis Symfony\Component\Mime\Email;
                ->from('sneakhub@gmailcom') //Adresse de l'expéditeur donc notre boutique ou vous mêmes
                //->to('to@gmailcom') //Adresse du receveur
                ->to($order->getEmail())
                ->subject('Confirmation de réception de commande') //Intitulé du mail
                ->html($html);
                $this->mailer->send($email);

                // Redirection vers la page du panier
                return $this->redirectToRoute('app_order_message');
            }
            // $order->setTotalPrice($data['total']);
            // $order->setCreatedAt(new \DateTimeImmutable());
            //     //dd($order);
            // $entityManager->persist($order);
            // $entityManager->flush();

        }

        return $this->render('order/index.html.twig', [
            'form'=>$form->createView(),
            'total'=>$data['total'],
        ]);
    }

    #[Route('/editor/order', name: 'app_orders_show')]
    public function getAllOrder(OrderRepository $orderRepository, Request $request, PaginatorInterface $paginator):Response
    {
        $data = $orderRepository->findBy([],['id'=>'DESC']);
        //dd($orders);

        $orders = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),//met en place la pagination
            6 //je choisi la limite de 6 commandes par page
            //2
        );

        return $this->render('order/order.html.twig', [
            "orders"=>$orders
        ]);
    }

    #[Route('/editor/order/{id}/is-completed/update', name: 'app_orders_is-completed-update')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $entityManager):Response
    {
        $order = $orderRepository->find($id);
        $order->setIsCompleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Modification effectuée');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/editor/order/{id}/remove', name: 'app_orders_remove')]
    public function removeOrder(Order $order, EntityManagerInterface $entityManager):Response 
    {
        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('danger', 'Commande supprimée');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/order_message', name: 'app_order_message')]
    public function orderMessage():Response
    {
        return $this->render('order/order_message.html.twig');
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status'=>200, "message"=>'on', 'content'=> $cityShippingPrice]));
    }
}
