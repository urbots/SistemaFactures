<?php

namespace App\Entity;

use App\Repository\CodisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodisRepository::class)]
class Codis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Codi = null;

    #[ORM\Column(length: 255)]
    private ?string $Unitat = null;

    #[ORM\ManyToOne(targetEntity: TipusCodi::class, inversedBy: 'codis')]
    #[ORM\JoinColumn(nullable: false)]
    private $tipusCodi;

    #[ORM\ManyToOne(targetEntity: EmpresaPublica::class, inversedBy: 'codis')]
    #[ORM\JoinColumn(nullable: false)]
    private $empresaPublica;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodi(): ?string
    {
        return $this->Codi;
    }

    public function setCodi(string $Codi): static
    {
        $this->Codi = $Codi;

        return $this;
    }

    public function getUnitat(): ?string
    {
        return $this->Unitat;
    }

    public function setUnitat(string $Unitat): static
    {
        $this->Unitat = $Unitat;

        return $this;
    }
}
