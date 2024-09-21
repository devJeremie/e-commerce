<?php

namespace App\Controller;

use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function success(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request): Response
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']); //recupère la cle secrete dans le fichier .env gràce au $_server
        
        $endpoint_secret= 'whsec_762838b6a470d9e24ef2fd08b52ec325225147e39308429459f540637fc2205b'; //cle stripe
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $event = null;

        try{
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpoint_secret
            );
        }catch(\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        }catch(\Stripe\Exception\SignatureVerificationException $e){
            return new Response('Invalid signature', 400);
        }
        switch($event->type){
            case 'payment_intent.succeeded':  // contient l'objet payment_intent
                $paymentIntent = $event->data->object;
                $fileName = 'stripe-detail-'.uniqid().'txt';
                file_put_contents($fileName, $paymentIntent);
                break;
            case 'payment_method.attached':   // contient l'objet payment_method
                $paymentMethod = $event->data->object; 
                break;
            default :
                break;
        }

        return new Response('Evènement recu avec succés', 200);
    }
}
