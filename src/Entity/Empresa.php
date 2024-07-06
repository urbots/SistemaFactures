<?php

namespace App\Entity;

use App\Entity\PersonaEmpresa;
use App\Repository\PersonaEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonaEmpresaRepository::class)]
class Empresa extends PersonaEmpresa
{
    public function getType(): string
    {
        return 'empresa';
    }

    public function getRaoSocial(): ?string
    {
        return $this->RaoSocial;
    }

    public function setRaoSocial(?string $RaoSocial): void
    {
        $this->RaoSocial = $RaoSocial;
    }

    public function getNomComercial(): ?string
    {
        return $this->NomComercial;
    }

    public function setNomComercial(?string $NomComercial): void
    {
        $this->NomComercial = $NomComercial;
    }
    #[ORM\Column(length: 255)]
    private ?string $RaoSocial = null;

    #[ORM\Column(length: 255)]
    private ?string $NomComercial = null;

    public function getXML()
    {
        return [
            'TaxIdentification' => [
                'PersonTypeCode' => 'J',
                'ResidenceTypeCode' => 'R',
                'TaxIdentificationNumber' => $this->getNIF(),
            ],
            'LegalEntity' => [
                'CorporateName' => $this->getNomComplet(),
                'AddressInSpain' => $this->getXMLAddress()
            ]
        ];
    }

    public function getNom(): ?string
    {
        return $this->RaoSocial;
    }

    public function getNomComplet(): ?string
    {
        return $this->NomComercial;
    }
}