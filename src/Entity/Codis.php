<?php

namespace App\Entity;

use App\Repository\CodisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\ManyToOne(targetEntity: TipusCodi::class, inversedBy: 'codis')]
    #[ORM\JoinColumn(nullable: false)]
    private TipusCodi $tipusCodi;

    #[ORM\ManyToOne(targetEntity: EmpresaPublica::class, inversedBy: 'codis')]
    #[ORM\JoinColumn(nullable: false)]
    private EmpresaPublica $empresaPublica;




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

    public function getXML(){
       return  [
            'CentreCode' => $this->getCodi(),
            'RoleTypeCode' => $this->getTipusCodi()->getReferencia(),
            'Name' => $this->Name,
            'AddressInSpain' => $this->empresaPublica->getXMLAddress(),
        ];
    }

    /**
     * @return mixed
     */
    public function getTipusCodi()
    {
        return $this->tipusCodi;
    }

    /**
     * @param mixed $tipusCodi
     */
    public function setTipusCodi($tipusCodi): void
    {
        $this->tipusCodi = $tipusCodi;
    }
}
