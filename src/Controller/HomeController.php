<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {

        return $this->render('home/index.html.twig', [
            'products'=>$productRepository->findAll(),
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/product/{id}/show ', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct(Product $product, ProductRepository $productRepository): Response //ici on recupere directement l'entity Product,
    // le repository sert pour les derniers produits ajoutés
    {
        $lastProductsAdd = $productRepository->findBy([],['id'=>'DESC'],5);//on crée la variable a laquelle on donne le repo et lam ethode findBy, puis on donne une limit de 5 en affichage

        return $this->render('home/show.html.twig', [ //il faut bien sur créer ce fichier
            'product'=>$product,
            'products'=>$lastProductsAdd
        ]);
    }

    #[Route('/product/subcategory/{id}/filter ', name: 'app_home_product_filter', methods: ['GET'])]
    public function filter($id, SubCategoryRepository $subCategoryRepository): Response //ici on recupere 
    
    {
      
        return $this->render('home/filter.html.twig', [ //il faut bien sur créer ce fichier
           
        ]);
    }
}
