<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods: ['POST'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {

         // Vérifie si la requête est de type POST
         if ($request->isMethod('POST')){
            // Récupère les données de la requête
            $data = $request->request->all();
            // Récupère le mot-clé de recherche
            $word = $data['word'];
            
            // Appelle la méthode searchEngine du repository pour récupérer les résultats de recherche
            $results = $productRepository->searchEngine($word);
        }

        // Rendu de la vue search_engine/index.html.twig avec les résultats de recherche
        return $this->render('search_engine/index.html.twig', [
            'products' => $results,
        ]);
    }
}