<?php

namespace App\Controller;

use App\Entity\AddProductHistory;
use App\Entity\Product;
use App\Form\AddProductHistoryType;
use App\Form\ProductType;
use App\Form\ProductUpdateType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/editor/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
#region Add
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();/* on recup l'image et son contenu*/
   
            if ($image) {/*si l'image existe*/
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalName);/* permet de recup des image avec espace dans le nom et l'enlever*/
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();/*cree un id unique a toute les images meme si elles ont un nom similaire*/

                try {
                    $image->move
                        ($this->getParameter('image_directory'),
                        $newFileImageName);/* on recup l'image et on la renomme et on la stocke dans le repertoire */
                }catch (FileException $exception) {}/*en cas d'erreur*/
                    $product->setImage($newFileImageName);
                
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new AddProductHistory();/*nouvelle instanciation de la classe*/
            $stockHistory->setQuantity($product->getStock());/*on recup l'id du produit et on ajoute au stock*/
            $stockHistory->setProduct($product);/*on insere le produit*/
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();/*effectue la mise a jour en bdd*/
            
            $this->addFlash('success','Votre produit a été ajouté');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
#endregion 
#region Show
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();/* on recup l'image et son contenu*/
   
            if ($image) {/*si l'image existe*/
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalName);/* permet de recup des image avec espace dans le nom et l'enlever*/
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();/*cree un id unique a toute les images meme si elles ont un nom similaire*/

                try {
                    $image->move
                        ($this->getParameter('image_directory'),
                        $newFileImageName);/* on recup l'image et on la renomme et on la stocke dans le repoertoire */
                }catch (FileException $exception) {}/*en cas d'erreur*/
                    $product->setImage($newFileImageName);
                
            }


            $entityManager->flush();

            $this->addFlash('success','Votre produit a été modifié');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    #endregion
    #region Delete
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($product);

            $this->addFlash('danger', 'Votre produit a été supprimé.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #endregion
    #region Stock
    #[Route('/add/product/{id}/stock', name: 'app_product_stock_add', methods: ['POST','GET'])]
    public function stockAdd($id, EntityManagerInterface $entityManager, Request $request, ProductRepository $productRepository): Response
    {
        // Création d'une nouvelle entité AddProductHistory (historique des ajouts de stock)
        $stockAdd = new AddProductHistory();
        // Génération du formulaire à partir du type AddProductHistoryType
        $form =$this->createForm(AddProductHistoryType::class, $stockAdd);
        $form->handleRequest($request);
        // Récupération du produit à modifier via son identifiant
        $product = $productRepository->find($id);
        // Vérification du formulaire après soumission
        if ($form->isSubmitted() && $form->isValid()) {
            // Si la quantité saisie est positive
            if($stockAdd->getQuantity()>0){
                // Calcul de la nouvelle quantité du stock
                $newQuantity = $product->getStock() + $stockAdd->getQuantity();
                $product->setStock($newQuantity);
                // Définition de la date d'ajout et liaison avec le produit concerné
                $stockAdd->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $stockAdd->setProduct($product);
                // Enregistrement dans la base de données
                $entityManager->persist($stockAdd);
                $entityManager->flush();
                // Message de confirmation et redirection vers la liste des produits
                $this->addFlash('success', "Le stock du produit à été modifié");
                return $this->redirectToRoute('app_product_index');
            }else {
                // Si la quantité est négative ou nulle, on empêche la mise à jour
                $this->addFlash('danger', "Le stock du produit ne doit pas être inférieur à zéro");
                return $this->redirectToRoute('app_product_stock_add', ['id'=>$product->getId()]);
            }
  
        }
        // Affichage du formulaire et des informations sur le produit
        return $this->render('product/addStock.html.twig',
            ['form'=> $form->createView(),
             'product' => $product,
            ]
        );
    }

    #[Route('/add/product/{id}/stock/history', name: 'app_product_stock_add_history', methods: ['GET'])]
    public function showHistoryProductStock($id, ProductRepository $productRepository, AddProductHistoryRepository $addProductHistoryRepository):Response
    {

        $product = $productRepository->find($id);/*on recupere le produit passé en paramètre*/
        $productAddHistory = $addProductHistoryRepository->findBy(['product'=>$product],['id'=>'DESC']);
        
        return $this->render('product/addedHistoryStockShow.html.twig',[
            "productsAdded"=>$productAddHistory
        ]);
    }
}
#endregion