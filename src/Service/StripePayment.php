<?php 

namespace App\ServiceService;

use Stripe\Stripe;

class StripePayment
{
   public $redirectUrl;

   public function __construct()
   {
        Stripe::setApiKey($_SERVER('STRIPE_SECRET_KEY'));
   }
}