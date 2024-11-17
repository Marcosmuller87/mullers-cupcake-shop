<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->product->getId());
        $this->assertNull($this->product->getName());
        $this->assertNull($this->product->getPrice());
        $this->assertNull($this->product->getDescription());
    }

    public function testSetAndGetName(): void
    {
        $name = "Cupcake de Chocolate";
        $this->product->setName($name);
        $this->assertEquals($name, $this->product->getName());
    }

    public function testSetAndGetPrice(): void
    {
        $price = "10.99";
        $this->product->setPrice($price);
        $this->assertEquals($price, $this->product->getPrice());
    }

    public function testSetAndGetDescription(): void
    {
        $description = "Delicioso cupcake de chocolate";
        $this->product->setDescription($description);
        $this->assertEquals($description, $this->product->getDescription());
    }
}