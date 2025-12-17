<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testIndexPageLoadsForAuthenticatedUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/admin/category');
        $this->assertResponseIsSuccessful();
    }

    public function testAddCategoryPageLoadsForAuthenticatedUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/admin/category/new');
        $this->assertResponseIsSuccessful();
    }
}
