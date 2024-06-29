<?php

namespace App\Entity;

use App\Repository\PersonaEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PersonaEmpresaRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['personaEmpresa' => 'PersonaEmpresa', 'empresaPublica' => 'EmpresaPublica'])]
class PersonaEmpresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomComplet = null;
    #[ORM\Column(length: 255)]
    private ?string $NIF = null;

    #[ORM\Column(length: 255)]
    private ?string $Carrer = null;

    #[ORM\Column(length: 255)]
    private ?string $Ciutat = null;

    #[ORM\Column(length: 255)]
    private ?string $CP = null;

    #[ORM\Column(length: 255)]
    private ?string $Provincia = null;

    #[ORM\OneToMany(targetEntity: Factura::class, mappedBy: 'emisor')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $facturesEmeses;

    #[ORM\OneToMany(targetEntity: Factura::class, mappedBy: 'receptor')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $facturesRebudes;

    public function __construct()
    {
        $this->facturesEmeses = new ArrayCollection();
        $this->facturesRebudes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNIF(): ?string
    {
        return $this->NIF;
    }

    public function setNIF(string $NIF): static
    {
        $this->NIF = $NIF;

        return $this;
    }

    public function getCarrer(): ?string
    {
        return $this->Carrer;
    }

    public function setCarrer(string $Carrer): static
    {
        $this->Carrer = $Carrer;

        return $this;
    }

    public function getCiutat(): ?string
    {
        return $this->Ciutat;
    }

    public function setCiutat(string $Ciutat): static
    {
        $this->Ciutat = $Ciutat;

        return $this;
    }

    public function getCP(): ?string
    {
        return $this->CP;
    }

    public function setCP(string $CP): static
    {
        $this->CP = $CP;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->Provincia;
    }

    public function setProvincia(string $Provincia): static
    {
        $this->Provincia = $Provincia;

        return $this;
    }

    public function setNomComplet(mixed $getData)
    {
        $this->NomComplet = $getData;
    }

    public function getNomComplet()
    {
        return $this->NomComplet;
    }

    public function __toString()
    {
        return $this->NomComplet;
    }
}
