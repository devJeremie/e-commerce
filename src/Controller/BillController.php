<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillController extends AbstractController
{
    #[Route('editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        $pdfOptions = new Options(); //definit la nouvelle instanciation de classe Options de Dompdf
        $pdfOptions->set('defaultFont','Arial'); //Définit la font
        $domPdf = new Dompdf($pdfOptions);//On ajoute les options
        $html = $this->renderView('bill/index.html.twig', [
            'order'=>$order,
         ]); // On insere ce que l'on veut imprimer
        $domPdf->loadHtml($html); // On charge le html dans dompdf
        $domPdf->render(); //On crée le rendu
        $domPdf->stream('SneakHub-Facture-'.$order->getId().'.pdf',[//On concatene la facture avec la terminaison"pdf"
            'Attachment'=>false //ca permet de dire on va telecharger le fichier, ou l'afficher et decider de l'imprimer et telecharger
        ]); 
        
        return new Response('',200,[ //
            'Content-Type' => 'application/pdf' 
        ]);
    }
}
