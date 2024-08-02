<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/editor/city')]
class CityController extends AbstractController
{
    #[Route('/', name: 'app_city_index', methods: ['GET'])]
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    // Fonction pour créer une nouvelle ville
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // Création d'un nouvel objet City
    $city = new City();

    // Création d'un formulaire pour la ville, basé sur le type CityType
    $form = $this->createForm(CityType::class, $city);

    // Traitement de la requête pour le formulaire
    $form->handleRequest($request);

    // Si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Enregistrement de la ville dans la base de données
        $entityManager->persist($city);
        // Enregistrement des modifications dans la base de données
        $entityManager->flush();

        // Redirection vers la page d'index des villes
        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }

    // Affichage de la page de création d'une nouvelle ville
    return $this->render('city/new.html.twig', [
        // Envoi de l'objet City et du formulaire à la vue
        'city' => $city,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    // Fonction pour modifier une ville existante
public function edit(Request $request, City $city, EntityManagerInterface $entityManager): Response
{
    // Création d'un formulaire pour la ville, basé sur le type CityType
    // Le formulaire est pré-rempli avec les données de la ville existante
    $form = $this->createForm(CityType::class, $city);

    // Traitement de la requête pour le formulaire
    $form->handleRequest($request);

    // Si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Enregistrement des modifications dans la base de données
        $entityManager->flush();

        // Redirection vers la page d'index des villes
        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }

    // Affichage de la page de modification de la ville
    return $this->render('city/edit.html.twig', [
        // Envoi de l'objet City et du formulaire à la vue
        'city' => $city,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_city_delete', methods: ['POST'])]
    public function delete(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
