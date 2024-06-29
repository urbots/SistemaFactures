<?php

namespace App\Entity;

use App\Repository\TipusCodiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipusCodiRepository::class)]
class TipusCodi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Referencia = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\OneToMany(targetEntity: Codis::class, mappedBy: 'tipusCodi')]
    #[ORM\JoinColumn(nullable: false)]
    private $codis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferencia(): ?string
    {
        return $this->Referencia;
    }

    public function setReferencia(string $Referencia): static
    {
        $this->Referencia = $Referencia;

        return $this;
    }
}
