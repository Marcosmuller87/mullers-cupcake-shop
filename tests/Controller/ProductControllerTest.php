<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testAccessProductIndexAsAnonymous(): void
    {
        // Try to access the products page
        $this->client->request('GET', '/');

        // No redirect for homepage
        $this->assertResponseIsSuccessful();
    }

    public function testAccessProductIndexAsUser(): void
    {
        // Get test user
        $testUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneByEmail('test@example.com');

        $this->assertNotNull($testUser, 'Test user must exist in database');

        // Login as user
        $this->client->loginUser($testUser);

        // Access the products page
        $this->client->request('GET', '/product');

        $this->assertResponseIsSuccessful();
    }

    public function testAccessNewProductPageAsUser(): void
    {
        // Get test user
        $testUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneByEmail('test@example.com');

        $this->assertNotNull($testUser, 'Test user must exist in database');

        // Login as user
        $this->client->loginUser($testUser);

        // Try to access the new product page
        $this->client->request('GET', '/product/new');

        // Should be denied access
        $this->assertResponseStatusCodeSame(403);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
        $this->client = null;
    }
}