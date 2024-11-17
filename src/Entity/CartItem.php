<?php
namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: "items")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "cartItems")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\OneToOne(targetEntity: Customization::class, cascade: ["persist"])]
    private ?Customization $customization = null;

    #[ORM\Column]
    private ?int $quantity = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        if ($product) {
            $this->price = (string) $product->getDisplayPrice();
        }
        return $this;
    }

    public function getCustomization(): ?Customization
    {
        return $this->customization;
    }

    public function setCustomization(?Customization $customization): self
    {
        $this->customization = $customization;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
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