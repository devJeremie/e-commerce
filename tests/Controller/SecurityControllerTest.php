<?php

namespace App\Tests\Controller;
// Importe la classe de base WebTestCase fournie par Symfony pour les tests fonctionnels
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
        // Le résultat de la requête est stocké dans un Crawler pour analyser le contenu de la page
        $crawler = $client->request('GET', '/login');

        // 3. Assertion : Vérifie que la réponse du serveur a réussi (code HTTP 200, ou 2xx)
        $this->assertResponseIsSuccessful();
        // 4. Assertion : Vérifie qu'un élément sélecteur CSS spécifique existe dans le contenu HTML de la page.
        // Ici, on cherche un formulaire dont l'attribut 'action' est "/login",
        // ce qui est typique du formulaire de connexion de Symfony.
        $this->assertSelectorExists('form[action="/login"]');
    }
}
