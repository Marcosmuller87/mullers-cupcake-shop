<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderReference = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    private ?Customization $customization = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderReference(): ?Order
    {
        return $this->orderReference;
    }

    public function setOrderReference(?Order $orderReference): static
    {
        $this->orderReference = $orderReference;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getCustomization(): ?Customization
    {
        return $this->customization;
    }

    public function setCustomization(?Customization $customization): static
    {
        $this->customization = $customization;
        return $this;
    }

    public function getSubtotal(): string
    {
        $subtotal = bcmul($this->price, (string) $this->quantity, 2);
        
        if ($this->customization) {
            $customizationTotal = bcmul($this->customization->getPriceIncrement(), (string) $this->quantity, 2);
            $subtotal = bcadd($subtotal, $customizationTotal, 2);
        }
        
        return $subtotal;
    }
}