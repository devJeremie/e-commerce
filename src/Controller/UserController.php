<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' =>$userRepository->findAll(),
        ]);
    }

    #[Route('/admin/user/{id}/to/editor ', name: 'app_user_to_editor')] //remettre la wildcard {role} pour selection du role en question
    public function changeRole(EntityManagerInterface $entityManager, User $user, $role): Response
    {
        $user->setRoles(['ROLE_EDITOR', 'ROLE_USER']);
        $entityManager->flush();

        $this->addFlash('success', "Le rôle éditeur à bien été ajouté à l'utilisateur");

        return $this->redirectToRoute('app_user');
        
        // On définit une liste blanche des rôles valides pour éviter les abus
    //     $validRoles = ['ROLE_EDITOR', 'ROLE_SOUS_ADMIN', 'ROLE_USER'];

    //     if (!in_array($role, $validRoles)) {
    //         $this->addFlash('error', "Le rôle demandé n'est pas valide.");
    //         return $this->redirectToRoute('app_user');
    //     }

    //     // On remplace complètement la liste des rôles par le rôle demandé plus éventuellement ROLE_USER par défaut
    //     if ($role !== 'ROLE_USER') {
    //         $user->setRoles([$role, 'ROLE_USER']);
    //     } else {
    //         $user->setRoles([$role]);
    //     }

    //     $entityManager->flush();

    //     $this->addFlash('success', "Le rôle $role a bien été attribué à l'utilisateur.");

    //     return $this->redirectToRoute('app_user');
    }
    

    #[Route('/admin/user/{id}/remove/editor/role ', name: 'app_user_remove_editor_role')]
    public function removeRoleEditor(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles([]);
        $entityManager->flush();

        $this->addFlash('danger', "Le rôle éditeur à bien été retiré à l'utilisateur");
        
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove/', name: 'app_user_remove')]
    public function ruserRemove(EntityManagerInterface $entityManager,$id,  UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('danger', "L'utilisateur à bien été supprimé.");
        
        return $this->redirectToRoute('app_user');
    }

}
