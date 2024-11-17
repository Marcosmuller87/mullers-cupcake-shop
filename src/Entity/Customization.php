<?php
namespace App\Entity;

use App\Repository\CustomizationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomizationRepository::class)]
class Customization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $flavor = null;

    #[ORM\Column(length: 255)]
    private ?string $topping = null;

    #[ORM\Column(length: 255)]
    private ?string $decoration = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $priceIncrement = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlavor(): ?string
    {
        return $this->flavor;
    }

    public function setFlavor(string $flavor): static
    {
        $this->flavor = $flavor;
        return $this;
    }

    public function getTopping(): ?string
    {
        return $this->topping;
    }

    public function setTopping(string $topping): static
    {
        $this->topping = $topping;
        return $this;
    }

    public function getDecoration(): ?string
    {
        return $this->decoration;
    }

    public function setDecoration(string $decoration): static
    {
        $this->decoration = $decoration;
        return $this;
    }

    public function getPriceIncrement(): ?string
    {
        return $this->priceIncrement;
    }

    public function setPriceIncrement(string $priceIncrement): static
    {
        $this->priceIncrement = $priceIncrement;
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
}