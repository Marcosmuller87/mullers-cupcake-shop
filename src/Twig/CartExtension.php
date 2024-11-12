<?php

namespace App\Twig;

use App\Repository\CartRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartExtension extends AbstractExtension
{
    public function __construct(private CartRepository $cartRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_repository', [$this, 'getCartRepository']),
        ];
    }

    public function getCartRepository(): CartRepository
    {
        return $this->cartRepository;
    }
}