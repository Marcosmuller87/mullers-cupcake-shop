<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create test user
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setAddress('123 Test Street');  // Adding the required address field
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'test123');
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setName('Admin User');
        $admin->setAddress('456 Admin Avenue');  // Adding the required address field
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        // Create test products
        $product = new Product();
        $product->setName('Test Cupcake');
        $product->setDescription('A delicious test cupcake');
        $product->setPrice('10.00');
        $manager->persist($product);

        $product2 = new Product();
        $product2->setName('Another Cupcake');
        $product2->setDescription('Another yummy test cupcake');
        $product2->setPrice('12.00');
        $manager->persist($product2);

        $manager->flush();
    }
}