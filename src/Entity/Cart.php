<?php
namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: "cart", cascade: ["persist", "remove"])]
    private Collection $items;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = '0.00';

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCart($this);
            $this->updateTotal();
        }
        return $this;
    }

    public function removeItem(CartItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
            $this->updateTotal();
        }
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function updateTotal(): void
    {
        $total = '0.00';
        foreach ($this->items as $item) {
            $total = bcadd($total, $item->getSubtotal(), 2);
        }
        $this->totalPrice = $total;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function convertToOrder(): Order
    {
        $order = new Order();
        $order->setUser($this->user);
        $order->setTotalPrice($this->totalPrice);
        $order->setStatus('pending');
        
        foreach ($this->items as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getPrice());
            
            if ($cartItem->getCustomization()) {
                $orderItem->setCustomization($cartItem->getCustomization());
            }
            
            $orderItem->setOrderReference($order);
            $order->addOrderItem($orderItem);
        }

        return $order;
    }
}