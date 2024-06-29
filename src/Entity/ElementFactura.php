<?php

namespace App\Entity;

use App\Repository\ElementFacturaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementFacturaRepository::class)]
class ElementFactura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $preuAmbImpostos = null;

    #[ORM\Column]
    private ?float $preuSenseImpostos = null;

    #[ORM\ManyToOne(targetEntity: Impost::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Impost|null $Impost = null;

    #[ORM\Column]
    private ?int $Unitats = null;

    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'elementsFactura')]
    #[ORM\JoinColumn(nullable: false)]
    private Factura $factura;

    #[ORM\ManyToOne(targetEntity: Elements::class, inversedBy: 'elementsFactura')]
    #[ORM\JoinColumn(nullable: false)]
    private Elements $elements;

    public function getFactura(): Factura
    {
        return $this->factura;
    }

    public function getElements(): Elements
    {
        return $this->elements;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPreuAmbImpostos(): ?float
    {
        return $this->preuAmbImpostos;
    }

    public function setPreuAmbImpostos(float $preuAmbImpostos): static
    {
        $this->preuAmbImpostos = $preuAmbImpostos;

        return $this;
    }

    public function getPreuSenseImpostos(): ?float
    {
        return $this->preuSenseImpostos;
    }

    public function setPreuSenseImpostos(float $preuSenseImpostos): static
    {
        $this->preuSenseImpostos = $preuSenseImpostos;

        return $this;
    }

    public function getImpost(): Impost
    {
        return $this->Impost;
    }

    public function setImpost(Impost $Impost): static
    {
        $this->Impost = $Impost;

        return $this;
    }

    public function getUnitats(): ?int
    {
        return $this->Unitats;
    }

    public function setUnitats(int $Unitats): static
    {
        $this->Unitats = $Unitats;

        return $this;
    }

    public function getConcepte(): ?string
    {
        return $this->Concepte;
    }

    public function setConcepte(string $Concepte): static
    {
        $this->Concepte = $Concepte;

        return $this;
    }

    public function getPreuUnitari(): ?int
    {
        return $this->preuUnitari;
    }

    public function setPreuUnitari(int $preuUnitari): static
    {
        $this->preuUnitari = $preuUnitari;

        return $this;
    }

    public function setElements(Elements $elements): static
    {
        $this->elements = $elements;

        return $this;
    }

    public function setFactura(Factura $factura)
    {
        $this->factura = $factura;
    }



    public function getXML(){
        return [
            'ItemDescription' => $this->elements->getConcepte(),
            'Quantity' => $this->Unitats,
            'UnitPriceWithoutTax' => $this->preuSenseImpostos,
            'TotalCost' => $this->preuAmbImpostos*$this->Unitats,
            'GrossAmount' => $this->preuAmbImpostos*$this->Unitats,
            'TaxesOutputs' => [
                'Tax' => [
                    'TaxTypeCode' => $this->Impost->getType(),
                    'TaxRate' => $this->Impost->getPercentatge(),
                    'TaxableBase' => ['TotalAmount' => $this->preuAmbImpostos*$this->Unitats],
                    'TaxAmount' => ['TotalAmount' => ($this->preuAmbImpostos-$this->preuSenseImpostos)*$this->Unitats],
                ],
            ],
        ];
    }
}
