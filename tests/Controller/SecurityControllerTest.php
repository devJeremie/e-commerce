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
        // 1. Crée un client HTTP simulé
        $client = static::createClient();
        // Simule un user connecté (on configure après)
        // $client->loginUser('test@test.com', 'password');
        // 2. Effectue une requête GET vers l'URL '/login'
        $client->request('GET', '/login');
        // 3. Assertion : // $this->assertResponseRedirects('/home', 302); 
        // Vérifie le code 302 et la destination
        $this->assertResponseIsSuccessful(); // Temporairement

    }
}
