<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {

        // $p = $productRepository->findByIdUp(10);//rappel de la méthode crée
        // dd($p);
        // $search = $productRepository->searchEngine('nike'); //je met un produit qui est en table product
        // dd($search);

        $data = $productRepository->findby([],['id'=>"DESC"]);
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),//met en place la pagination
            8 //je choisi la limite de 8 articles par page
        );

        return $this->render('home/index.html.twig', [
            'products'=> $products, //maintenant ici on retourne lepaginator donc $products car il est dedans désormais
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/product/{id}/show ', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct(Product $product, ProductRepository $productRepository,CategoryRepository $categoryRepository): Response //ici on recupere directement l'entity Product,
    // le repository sert pour les derniers produits ajoutés
    {
        $lastProductsAdd = $productRepository->findBy([],['id'=>'DESC'],5);//on crée la variable a laquelle on donne le repo et lam ethode findBy, puis on donne une limit de 5 en affichage

        return $this->render('home/show.html.twig', [ //il faut bien sur créer ce fichier
            'product'=>$product,
            'products'=>$lastProductsAdd,
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/product/subcategory/{id}/filter ', name: 'app_home_product_filter', methods: ['GET'])]
    public function filter($id, SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository): Response //ici on recupere l'id et la repo des sous catégories
    
    {
        $product = $subCategoryRepository->find($id)->getProducts(); //on a crée une variable qui contient le repo des sous-catégories que l'on récup grace a leur id et on recherche les produits qui sont reliés
        $subCategory = $subCategoryRepository->find($id);
        
        return $this->render('home/filter.html.twig', [ //il faut bien sur créer ce fichier
        'products'=>$product,
        'subCategory'=>$subCategory,
        'categories'=>$categoryRepository->findAll()
        ]);
    }
}
