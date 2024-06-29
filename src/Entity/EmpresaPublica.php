<?php

namespace App\Entity;

use App\Repository\EmpresaPublicaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpresaPublicaRepository::class)]
class EmpresaPublica extends PersonaEmpresa
{
    #[ORM\OneToMany(targetEntity: Codis::class, mappedBy: 'empresaPublica')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $codis;

    public function __construct()
    {
        parent::__construct();
        $this->codis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getXML()
    {

        //all array del parent añadimos
        return [
            'TaxIdentification' => [
                'PersonTypeCode' => $this->isPersonaJuridica() ? 'J' : 'F',
                'ResidenceTypeCode' => 'R',
                'TaxIdentificationNumber' => $this->getNIF(),
            ],
            'AdministrativeCentres' => [
                'AdministrativeCentre' =>
                    $this->getCodisXML()

            ],
            'LegalEntity' => [
                'CorporateName' => $this->getNomComplet(),
                'AddressInSpain' => $this->getXMLAddress()
            ]
        ];

    }

    private function getCodisXML()
    {
        $codis = [];
        foreach ($this->codis as $codi) {
            $codis[] = $codi->getXML();
        }
        return $codis;
    }
}
