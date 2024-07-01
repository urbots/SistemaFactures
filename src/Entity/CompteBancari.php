<?php

namespace App\Entity;

use App\Repository\CompteBancariRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteBancariRepository::class)]
class CompteBancari
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $IBAN = null;

    #[ORM\Column(length: 255)]
    private ?string $SWIFT = null;

    #[ORM\Column(length: 255)]
    private ?string $Entitat = null;

    #[ORM\Column(length: 255)]
    private ?string $Referencia = null;

    #[ORM\OneToMany(targetEntity: Factura::class, mappedBy: 'compteBancari')]
    #[ORM\JoinColumn(nullable: false)]
    private $factures;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIBAN(): ?string
    {
        return $this->IBAN;
    }

    public function setIBAN(string $IBAN): static
    {
        $this->IBAN = $IBAN;

        return $this;
    }

    public function getSWIFT(): ?string
    {
        //11 characters, ponemos tantas X para que llegue a 11

        return $this->SWIFT. str_repeat('X', 11 - strlen($this->SWIFT));
    }

    public function setSWIFT(string $SWIFT): static
    {
        //11 characters, ponemos tantas X para que llegue a 11
        $this->SWIFT = $SWIFT . str_repeat('X', 11 - strlen($SWIFT));


        return $this;
    }

    public function getEntitat(): ?string
    {
        return $this->Entitat;
    }

    public function setEntitat(string $Entitat): static
    {
        $this->Entitat = $Entitat;

        return $this;
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

    public function __toString(): string
    {
        return "IBAN: " . $this->IBAN . "<br>SWIFT: " . $this->SWIFT . "<br>Entitat: " . $this->Entitat . "";
    }

    public function getXML()
    {
        return ['IBAN' => $this->getIBAN(), 'BIC' => $this->getSWIFT()];
    }
}
