<?php

namespace App\Entity;

use App\Repository\ImpostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImpostRepository::class)]
class Impost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPercentatge(): ?int
    {
        return $this->percentatge;
    }

    public function setPercentatge(?int $percentatge): void
    {
        $this->percentatge = $percentatge;
    }

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?int $percentatge = null;

    public function getType(): ?string
    {
        //string con 2 números, 01, 02, 03, 04, 05
        return str_pad($this->type, 2, '0', STR_PAD_LEFT);
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    #[ORM\Column]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->nom . ' (' . $this->percentatge . '%)';
    }
}
