<?php
namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Por favor, digite o nome do produto')]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Por favor, adicione uma descrição')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Por favor, digite o preço')]
    #[Assert\Positive(message: 'O preço deve ser maior que zero')]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderItem::class)]
    private Collection $orderItems;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Review::class, cascade: ['remove'])]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartItem::class)]
    private Collection $cartItems;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
        $this->isActive = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }
        return $this;
    }

    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function softDelete(): self
    {
        $this->isActive = false;
        $this->deletedAt = new \DateTime();
        return $this;
    }

    public function isDeleted(): bool
    {
        return !$this->isActive;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function getDisplayPrice(): string
    {
        return (string) $this->price;
    }
}