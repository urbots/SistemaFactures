<?php

namespace App\Entity;

use App\Entity\PersonaEmpresa;
use App\Repository\PersonaEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonaEmpresaRepository::class)]
class Persona extends PersonaEmpresa
{
    public function getType(): string
    {
        return 'persona';
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }
    #[ORM\Column(length: 255)]
    private ?string $nom = null;
    #[ORM\Column(length: 255)]
    private ?string $cognom1 = null;
    #[ORM\Column(length: 255)]
    private ?string $cognom2 = null;
    public function getXML()
    {
        return [
            'TaxIdentification' => [
                'PersonTypeCode' => 'F',
                'ResidenceTypeCode' => 'R',
                'TaxIdentificationNumber' => $this->getNIF(),
            ],
            'Individual' => [
                'Name' => $this->nom,
                'FirstSurname' => $this->cognom1,
                'SecondSurname' => $this->cognom2,
                'AddressInSpain' => $this->getXMLAddress()
            ]
        ];
    }

    public function getCognom1(): ?string
    {
        return $this->cognom1;
    }

    public function setCognom1(?string $cognom1): void
    {
        $this->cognom1 = $cognom1;
    }

    public function getCognom2(): ?string
    {
        return $this->cognom2;
    }

    public function setCognom2(?string $cognom2): void
    {
        $this->cognom2 = $cognom2;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getNomComplet(): ?string
    {
        return $this->nom . ' ' . $this->cognom1 . ' ' . $this->cognom2;
    }
}