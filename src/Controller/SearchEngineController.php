<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods: ['POST'])]
    public function index(Request $request): Response
    {

        if ($request->isMethod('POST')){
            $data = $request->request->all();
            $search = $data['search'];
        }

        return $this->render('search_engine/index.html.twig', [
            'controller_name' => 'SearchEngineController',
        ]);
    }
}
