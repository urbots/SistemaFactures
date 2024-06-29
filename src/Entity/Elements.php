<?php

namespace App\Entity;

use App\Repository\ElementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementsRepository::class)]
class Elements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Concepte = null;

    #[ORM\Column]
    private ?int $preuUnitari = null;

    #[ORM\Column]
    private ?int $preuSenseImpostos = null;

    #[ORM\Column]
    private ?float $Impost = null;

    #[ORM\OneToMany(targetEntity: ElementFactura::class, mappedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false)]
    private $elementsFactura;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPreuSenseImpostos(): ?int
    {
        return $this->preuSenseImpostos;
    }

    public function setPreuSenseImpostos(int $preuSenseImpostos): static
    {
        $this->preuSenseImpostos = $preuSenseImpostos;

        return $this;
    }

    public function getImpost(): ?float
    {
        return $this->Impost;
    }

    public function setImpost(float $Impost): static
    {
        $this->Impost = $Impost;

        return $this;
    }
}
