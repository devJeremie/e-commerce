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
        $domPdf = new Dompdf($pdfOptions);
        

        return $this->render('bill/index.html.twig', [
           'order'=>$order,
        ]);
    }
}
