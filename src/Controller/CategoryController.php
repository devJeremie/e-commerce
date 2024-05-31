<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repo, ): Response
    {
        $categories = $repo->findAll();//permet de recuperer tout les éléments de la bdd 


        return $this->render('category/index.html.twig', [
            'categories' => $categories //contient un array avec toutes les catégories
        ]);
    }

    #[Route('/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           // dd($category);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('app_category');
        }
       
        return $this->render('category/newCategory.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/category/{id}/update ', name: 'app_category_update')]
    public function updateCategory(Category $category, EntityManagerInterface $entityManager, Request $request): Response
    {

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_category');
        }
       
        return $this->render('category/updateCategory.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/category/{id}/delete ', name: 'app_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {

       $entityManager->remove($category);
       $entityManager->flush();

       return $this->redirectToRoute('app_category');
    }
}
