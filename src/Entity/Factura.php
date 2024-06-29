<?php

namespace App\Entity;

use App\Repository\FacturaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturaRepository::class)]
class Factura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dataEmissio = null;

    #[ORM\Column]
    private ?int $total = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPDF = null;

    #[ORM\OneToMany(targetEntity: ElementFactura::class, mappedBy: 'factura')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $elementsFactura;

    #[ORM\ManyToOne(targetEntity: CompteBancari::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private CompteBancari $compteBancari;

    #[ORM\ManyToOne(targetEntity: PersonaEmpresa::class, inversedBy: 'facturesEmeses')]
    #[ORM\JoinColumn(nullable: false)]
    private PersonaEmpresa $emisor;

    #[ORM\ManyToOne(targetEntity: PersonaEmpresa::class, inversedBy: 'facturesRebudes')]
    #[ORM\JoinColumn(nullable: false)]
    private PersonaEmpresa $receptor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDataEmissio(): ?\DateTimeInterface
    {
        return $this->dataEmissio;
    }

    public function setDataEmissio(\DateTimeInterface $dataEmissio): static
    {
        $this->dataEmissio = $dataEmissio;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getUrlPDF(): ?string
    {
        return $this->urlPDF;
    }

    public function setUrlPDF(?string $urlPDF): static
    {
        $this->urlPDF = $urlPDF;

        return $this;
    }
}
