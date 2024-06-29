<?php

namespace App\Entity;

use App\Repository\ElementFacturaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementFacturaRepository::class)]
class ElementFactura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $preuAmbImpostos = null;

    #[ORM\Column]
    private ?float $preuSenseImpostos = null;

    #[ORM\Column]
    private ?int $Impost = null;

    #[ORM\Column]
    private ?int $Unitats = null;

    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'elementsFactura')]
    #[ORM\JoinColumn(nullable: false)]
    private Factura $factura;

    #[ORM\ManyToOne(targetEntity: Elements::class, inversedBy: 'elementsFactura')]
    #[ORM\JoinColumn(nullable: false)]
    private Elements $elements;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPreuAmbImpostos(): ?float
    {
        return $this->preuAmbImpostos;
    }

    public function setPreuAmbImpostos(float $preuAmbImpostos): static
    {
        $this->preuAmbImpostos = $preuAmbImpostos;

        return $this;
    }

    public function getPreuSenseImpostos(): ?float
    {
        return $this->preuSenseImpostos;
    }

    public function setPreuSenseImpostos(float $preuSenseImpostos): static
    {
        $this->preuSenseImpostos = $preuSenseImpostos;

        return $this;
    }

    public function getImpost(): ?int
    {
        return $this->Impost;
    }

    public function setImpost(int $Impost): static
    {
        $this->Impost = $Impost;

        return $this;
    }

    public function getUnitats(): ?int
    {
        return $this->Unitats;
    }

    public function setUnitats(int $Unitats): static
    {
        $this->Unitats = $Unitats;

        return $this;
    }

    public function getConcepte(): ?string
    {
        return $this->Concepte;
    }

    public function setConcepte(string $Concepte): static
    {
        $this->Concepte = $Concepte;

        return $this;
    }

    public function getPreuUnitari(): ?int
    {
        return $this->preuUnitari;
    }

    public function setPreuUnitari(int $preuUnitari): static
    {
        $this->preuUnitari = $preuUnitari;

        return $this;
    }
}
