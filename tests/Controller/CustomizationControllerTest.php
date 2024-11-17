<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomizationControllerTest extends WebTestCase
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

    public function testCustomizeProductAsAnonymous(): void
    {
        // Get the first product
        $product = $this->entityManager
            ->getRepository(Product::class)
            ->findOneBy([]);

        $this->assertNotNull($product, 'Product must exist in test database');

        // Try to access customization page
        $this->client->request('GET', sprintf('/product/%d/customize', $product->getId()));

        // Should redirect to login
        $this->assertResponseRedirects('/login');
    }

    public function testCustomizeProductAsUser(): void
    {
        // Get test user
        $testUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneByEmail('test@example.com');

        $this->assertNotNull($testUser, 'Test user must exist in database');

        // Get test product
        $product = $this->entityManager
            ->getRepository(Product::class)
            ->findOneBy([]);

        $this->assertNotNull($product, 'Product must exist in test database');

        // Login as user
        $this->client->loginUser($testUser);

        // Access customization page
        $this->client->request('GET', sprintf('/product/%d/customize', $product->getId()));

        // Should be successful
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="customization"]');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
        $this->client = null;
    }
}