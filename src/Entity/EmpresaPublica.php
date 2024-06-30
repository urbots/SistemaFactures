<?php

namespace App\Entity;

use App\Repository\EmpresaPublicaRepository;
use App\Repository\PersonaEmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonaEmpresaRepository::class)]
class EmpresaPublica extends Empresa
{
    public function getType(): string
    {
        return 'empresa_publica';
    }
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

        //all array del parent añadimos AdministrativeCentres en el medio de TaxIdentification y LegalEntity
        $data = parent::getXML();
        //primero hay TaxIdentification y luego LegalEntity, hay que añadir AdministrativeCentres entre ellos
        $data['AdministrativeCentres'] = [
            'AdministrativeCentre' =>
                $this->getCodisXML()

        ];
        //reordenamos el array para que quede TaxIdentification, AdministrativeCentres y LegalEntity
        $data = array_merge(array_slice($data, 0, 1), array_slice($data, 2, 1), array_slice($data, 1, 1));
        return $data;


//        return [
//            'TaxIdentification' => [
//                'PersonTypeCode' => $this->isPersonaJuridica() ? 'J' : 'F',
//                'ResidenceTypeCode' => 'R',
//                'TaxIdentificationNumber' => $this->getNIF(),
//            ],
//            'AdministrativeCentres' => [
//                'AdministrativeCentre' =>
//                    $this->getCodisXML()
//
//            ],
//            'LegalEntity' => [
//                'CorporateName' => $this->getNomComplet(),
//                'AddressInSpain' => $this->getXMLAddress()
//            ]
//        ];

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
