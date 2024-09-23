<?php

namespace App\Controller;

use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * Route pour la page de succès du paiement
     * 
     */
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function success(): Response
    {
        // Rendre la vue "index.html.twig" avec le nom du contrôleur
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    /**
     * Route pour la page d'annulation du paiement
     * 
     */
    
    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        // Rendre la vue "index.html.twig" avec le nom du contrôleur
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    /**
     * Route pour la notification de Stripe (webhook)
     * 
     */
    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request): Response
    {
        // Définir la clé secrète de Stripe à partir de la variable d'environnement
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        
        // Définir la clé de webhook de Stripe
        $endpoint_secret = 'whsec_762838b6a470d9e24ef2fd08b52ec325225147e39308429459f540637fc2205b';
        // Récupérer le contenu de la requête
        $payload = $request->getContent();
        // Récupérer l'en-tête de signature de la requête
        $sigHeader = $request->headers->get('Stripe-Signature');
        // Initialiser l'événement à null
        $event = null;

        try {
            // Construire l'événement à partir de la requête et de la signature
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Retourner une erreur 400 si le payload est invalide
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Retourner une erreur 400 si la signature est invalide
            return new Response('Invalid signature', 400);
        }
        
        // Gérer les différents types d'événements
        switch ($event->type) {
            case 'payment_intent.succeeded':  // Événement de paiement réussi
                // Récupérer l'objet payment_intent
                $paymentIntent = $event->data->object;
                
                // Enregistrer les détails du paiement dans un fichier
                $fileName = 'stripe-detail-'.uniqid().'txt';
                $orderId = $paymentIntent->metadata->orderId;
                file_put_contents($fileName, $orderId);
                break;
            case 'payment_method.attached':   // Événement de méthode de paiement attachée
                // Récupérer l'objet payment_method
                $paymentMethod = $event->data->object; 
                break;
            default :
                // Ne rien faire pour les autres types d'événements
                break;
        }

        // Retourner une réponse 200 pour indiquer que l'événement a été reçu avec succès
        return new Response('Événement reçu avec succès', 200);
    }
}