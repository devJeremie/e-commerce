<?php

namespace App\Tests\Controller;

use App\Controller\UserController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

// Utilisation de KernelTestCase pour des tests d'intégration plus légers
class UserControllerTest extends KernelTestCase
{
    private $entityManager;
    private $userRepository;
    private $userController;

    protected function setUp(): void
    {
        // Démarre le kernel Symfony pour avoir accès aux services
        self::bootKernel();

        // Récupère le conteneur de services
        $container = static::getContainer();

        // Utilise le conteneur pour obtenir les services réels au lieu de mocks
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->userRepository = $container->get(UserRepository::class);

        // Crée une instance réelle du contrôleur avec les services
        $this->userController = new UserController($this->entityManager, $this->userRepository);
    }

    public function testIndex(): void
    {
        // Appelle la méthode index du contrôleur
        $response = $this->userController->index($this->userRepository);
        
        // Vérifie que la réponse est une instance de Response
        $this->assertInstanceOf(Response::class, $response);
        // Vérifie que le code de statut est 200 (OK)
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        // Ici, vous pourriez ajouter des assertions pour vérifier le contenu de la réponse
    }

    public function testChangeRole(): void
    {
        // Crée un nouvel utilisateur pour le test
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        // Appelle la méthode changeRole du contrôleur
        $response = $this->userController->changeRole($this->entityManager, $user);

        // Vérifie que la réponse est une redirection
        $this->assertInstanceOf(RedirectResponse::class, $response);
        // Vérifie que le rôle a été correctement ajouté
        $this->assertEquals(['ROLE_EDITOR', 'ROLE_USER'], $user->getRoles());
        // Vérifie que le code de statut est 302 (Found/Redirection)
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRemoveRoleEditor(): void
    {
        // Crée un utilisateur avec le rôle éditeur
        $user = new User();
        $user->setRoles(['ROLE_EDITOR', 'ROLE_USER']);

        // Appelle la méthode removeRoleeditor du contrôleur
        $response = $this->userController->removeRoleeditor($this->entityManager, $user);

        // Vérifie que la réponse est une redirection
        $this->assertInstanceOf(RedirectResponse::class, $response);
        // Vérifie que le rôle ROLE_EDITOR a été retiré
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        // Vérifie que le code de statut est 302 (Found/Redirection)
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRuserRemove(): void
    {
        // Crée et persiste un nouvel utilisateur dans la base de données de test
        $user = new User();
        $user->setEmail('test@example.com');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        // Appelle la méthode ruserRemove du contrôleur
        $response = $this->userController->ruserRemove($userId);

        // Vérifie que la réponse est une redirection
        $this->assertInstanceOf(RedirectResponse::class, $response);
        // Vérifie que le code de statut est 302 (Found/Redirection)
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());

        // Vérifie que l'utilisateur a été effectivement supprimé de la base de données
        $deletedUser = $this->userRepository->find($userId);
        $this->assertNull($deletedUser);
    }

    protected function tearDown(): void
    {
        // Appelle la méthode tearDown() de la classe parente
        // Cela assure que toute la logique de nettoyage standard est exécutée
        parent::tearDown();

        // Désactive temporairement les vérifications de clés étrangères
        // Cela permet de tronquer les tables sans se soucier des contraintes de clés étrangères
        $this->entityManager->getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS=0');

        // Vide complètement la table 'user'
        // TRUNCATE est plus rapide que DELETE et réinitialise les auto-incréments
        $this->entityManager->getConnection()->executeStatement('TRUNCATE TABLE user');

        // Réactive les vérifications de clés étrangères
        // Important pour maintenir l'intégrité de la base de données pour les tests suivants
        $this->entityManager->getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS=1');

        // Efface tous les objets gérés par l'EntityManager
        // Cela évite que des entités en mémoire ne soient réutilisées dans les tests suivants
        $this->entityManager->clear();
    }
}
