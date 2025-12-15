<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Classe de test pour le contrôleur de sécurité (gestion de la connexion/déconnexion).
 * Elle hérite de WebTestCase pour utiliser les outils de test fournis par Symfony.
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Teste si la page de connexion se charge correctement pour un utilisateur anonyme (non connecté).
     */
    public function testLoginPageLoadsForAnonymousUser(): void
    {
        // 1. Crée un client HTTP simulé qui agit comme un navigateur pour faire des requêtes
        $client = static::createClient();
        // 2. Effectue une requête GET vers l'URL '/login'
        // Le Crawler ($crawler) est retourné pour permettre l'inspection du contenu HTML.
        $crawler = $client->request('GET', '/login');

        // 3. Assertion : Vérifie que la réponse du serveur a réussi (code HTTP 2xx).
        // C'est une vérification générique (ex: 200, 201, 204...).
        $this->assertResponseIsSuccessful(); // Juste vérifier que /login répond 200
        // // 4. Assertion : Vérifie spécifiquement que le code de statut HTTP est exactement 200 (OK).
        // // Note: La ligne ci-dessus (assertResponseIsSuccessful) et celle-ci sont souvent redondantes 
        // // si l'on s'attend strictement à 200, mais elles sont toutes deux valides.
        // $this->assertResponseStatusCodeSame(200);

        // 4. Vérifie l'existence du formulaire principal de connexion 
        //en utilisant son sélecteur CSS (ici, la classe 'loginForm')
        $this->assertSelectorExists('form.loginForm');
        // 5. Vérifie l'existence du champ de saisie pour l'email 
        //(recherche un input avec l'attribut name="email")
        $this->assertSelectorExists('input[name="email"]');
        // 6. Vérifie l'existence du champ de saisie pour le mot de passe 
        //(recherche un input avec l'attribut name="password")
        $this->assertSelectorExists('input[name="password"]');
        // 7. Vérifie qu'il y a un élément h1 (titre principal) 
        //sur la page qui contient le texte "Connexion"
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginRedirectsIfAlreadyAuthenticated(): void
    {
        // 1. Crée un client HTTP simulé pour interagir avec l'application.
        $client = static::createClient();
        // 2. Accède au conteneur de services du kernel de test.
        $container = static::getContainer();
        // 3. Récupère le service qui gère la récupération
        // des utilisateurs (User Provider).
        // L'alias 'app_user_provider_test' est spécifique à l'environnement de test et doit être configuré
        // pour récupérer les utilisateurs nécessaires au test 
        //(souvent à partir d'une fixture ou d'un service mocké).
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        // 4. Charge l'objet utilisateur réel (l'entité User) en utilisant un identifiant
        //(ici, l'email 'test@test.com').
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        // 5. Simule la connexion de cet objet utilisateur au client HTTP.
        // À partir de ce point, le client est considéré comme authentifié par Symfony.
        $client->loginUser($user);
        // --- Exécution de la Requête ---
        // 6. Effectue une requête GET vers l'URL '/login' alors que le client est connecté.
        $client->request('GET', '/login');
        // --- Assertion ---
        // 7. Assertion : Vérifie que la réponse du serveur est une redirection (code HTTP 3xx).
        // C'est le comportement attendu : un utilisateur déjà connecté ne doit pas voir la page de connexion,
        // mais doit être renvoyé vers une autre route.
        $this->assertResponseRedirects();
    }
    public function testLogoutWorks(): void
    {
        // 1. Crée un client HTTP simulé.
        $client = static::createClient();
        // --- Étape d'Authentification (Nécessaire pour tester la déconnexion) ---
        // 2. Accède au conteneur de services pour récupérer l'utilisateur.
        $container = static::getContainer();
        // 3. Récupère le service qui fournit les utilisateurs de test.
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        // 4. Charge l'objet utilisateur 'test@test.com'.
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        // 5. Simule la connexion de cet utilisateur au client.
        // Le client est maintenant dans l'état 'connecté'.
        $client->loginUser($user);
        $client->request('GET', '/logout');
        // --- Assertion ---
        // 7. Assertion : Vérifie que la réponse du serveur est une redirection (code HTTP 3xx).
        // Une déconnexion réussie invalide la session et redirige l'utilisateur
        // vers une destination configurée (souvent la page d'accueil ou la page de connexion).
        $this->assertResponseRedirects();
    }
}
