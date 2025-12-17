<?php

namespace App\Tests\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{

    public function testRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category');
        $this->assertResponseRedirects('/login');
    }

    public function testAuthenticatedAdminAccess(): void
    {
        $client = static::createClient();

        // MOCK CategoryRepository
        $categoryRepoMock = $this->createMock(CategoryRepository::class);
        $categoryRepoMock->method('findAll')->willReturn([]);
        static::getContainer()->set(CategoryRepository::class, $categoryRepoMock);

        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/admin/category');
        $this->assertResponseIsSuccessful();
    }

    public function testAddFormRequiresAdmin(): void
    {
        $client = static::createClient();

        // MOCK CategoryRepository
        $categoryRepoMock = $this->createMock(CategoryRepository::class);
        $categoryRepoMock->method('findAll')->willReturn([]);
        static::getContainer()->set(CategoryRepository::class, $categoryRepoMock);

        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/admin/category/new');
        $this->assertResponseIsSuccessful();
    }
}
