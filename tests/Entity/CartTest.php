<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private Cart $cart;
    private User $user;

    protected function setUp(): void
    {
        $this->cart = new Cart();
        $this->user = new User();
        $this->cart->setUser($this->user);
    }

    public function testAddItem(): void
    {
        $cartItem = new CartItem();
        $product = new Product();
        $product->setName('Test Cupcake');
        $product->setPrice('10.00');
        $cartItem->setProduct($product);
        $cartItem->setQuantity(1);

        $this->cart->addItem($cartItem);

        $this->assertCount(1, $this->cart->getItems());
        $this->assertEquals($cartItem, $this->cart->getItems()->first());
    }

    public function testRemoveItem(): void
    {
        $cartItem = new CartItem();
        $this->cart->addItem($cartItem);
        $this->cart->removeItem($cartItem);

        $this->assertCount(0, $this->cart->getItems());
    }

    public function testUpdateTotal(): void
    {
        $cartItem1 = new CartItem();
        $product1 = new Product();
        $product1->setPrice('10.00');
        $cartItem1->setProduct($product1);
        $cartItem1->setQuantity(2);
        $cartItem1->setPrice('10.00');

        $cartItem2 = new CartItem();
        $product2 = new Product();
        $product2->setPrice('15.00');
        $cartItem2->setProduct($product2);
        $cartItem2->setQuantity(1);
        $cartItem2->setPrice('15.00');

        $this->cart->addItem($cartItem1);
        $this->cart->addItem($cartItem2);
        $this->cart->updateTotal();

        $expectedTotal = '35.00'; // (10.00 * 2) + (15.00 * 1)
        $this->assertEquals($expectedTotal, $this->cart->getTotalPrice());
    }
}