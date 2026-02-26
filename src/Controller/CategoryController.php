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
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategoryRepository $repo, ): Response
    {
        $categories = $repo->findAll();//permet de recuperer tout les éléments de la bdd 


        return $this->render('category/index.html.twig', [
            'categories' => $categories //contient un array avec toutes les catégories
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           // dd($category);
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', /* The phrase `'Votre catégorie à bien été créée'` is a message
            that will be displayed as a success flash message after a new
            category has been successfully created in the `addCategory`
            method of the `CategoryController` class. This message
            informs the user that the category creation process was
            successful. */
            'Votre catégorie à bien été créée');

            return $this->redirectToRoute('app_category');
        }
       
        return $this->render('category/newCategory.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/admin/category/{id}/update ', name: 'app_category_update')]
    public function updateCategory(Category $category, EntityManagerInterface $entityManager, Request $request): Response
    {

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

            $this->addFlash('success', 'Votre catégorie à bien été modifiée.');

            return $this->redirectToRoute('app_category');
        }
       
        return $this->render('category/updateCategory.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    #[Route('/admin/category/{id}/delete ', name: 'app_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {

       $entityManager->remove($category);
       $entityManager->flush();

       $this->addFlash('danger', 'Votre catégorie à bien été supprimée.');

       return $this->redirectToRoute('app_category');
    }
}
