<?php

namespace App\Entity;

use App\Repository\FacturaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturaRepository::class)]
class Factura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dataEmissio = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPDF = null;

    #[ORM\OneToMany(targetEntity: ElementFactura::class, mappedBy: 'factura', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $elementsFactura;

    #[ORM\ManyToOne(targetEntity: CompteBancari::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private CompteBancari $compteBancari;

    #[ORM\ManyToOne(targetEntity: PersonaEmpresa::class, inversedBy: 'facturesEmeses')]
    #[ORM\JoinColumn(nullable: false)]
    private PersonaEmpresa $emisor;

    #[ORM\ManyToOne(targetEntity: PersonaEmpresa::class, inversedBy: 'facturesRebudes')]
    #[ORM\JoinColumn(nullable: false)]
    private PersonaEmpresa $receptor;

    public function getEmisor(): PersonaEmpresa
    {
        return $this->emisor;
    }

    public function getReceptor(): PersonaEmpresa
    {
        return $this->receptor;
    }


    #[ORM\Column(nullable: false)]
    private $year;

    #[ORM\Column(nullable: false)]
    private $numFactura;

    #[ORM\Column(nullable: true)]
    private string $observacions;

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getNumFactura()
    {
        return $this->numFactura;
    }

    /**
     * @param mixed $numFactura
     */
    public function setNumFactura($numFactura): void
    {
        $this->numFactura = $numFactura;
    }

    public function __construct()
    {
        $this->elementsFactura = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDataEmissio(): ?\DateTimeInterface
    {
        return $this->dataEmissio;
    }

    public function setDataEmissio(\DateTimeInterface $dataEmissio): static
    {
        $this->dataEmissio = $dataEmissio;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getUrlPDF(): ?string
    {
        return $this->urlPDF;
    }

    public function setUrlPDF(?string $urlPDF): static
    {
        $this->urlPDF = $urlPDF;

        return $this;
    }

    public function setEmisor(PersonaEmpresa $emisor): static
    {
        $this->emisor = $emisor;

        return $this;
    }

    public function setReceptor(PersonaEmpresa $receptor): static
    {
        $this->receptor = $receptor;

        return $this;
    }

    public function setCompteBancari(CompteBancari $compteBancari): static
    {
        $this->compteBancari = $compteBancari;

        return $this;
    }

    public function addElement(ElementFactura $element)
    {
        $this->elementsFactura->add($element);
    }

    public function getNumero()
    {
        //año + numero factura con 4 ceros
        return $this->year . str_pad($this->numFactura, 4, "0", STR_PAD_LEFT);
    }

    public function getElementsFactura(): Collection
    {
        return $this->elementsFactura;
    }

    public function getCompteBancari(): CompteBancari
    {
        return $this->compteBancari;
    }

    public function getImpostos()
    {
        //retorna nom de cada impost i percentatge
        $impostos = [];
        foreach ($this->elementsFactura as $element) {
            $impost = $element->getImpost();
            if (!in_array($impost, $impostos)) {
                $impostos[] = $impost;
            }
        }
        return $impostos;
    }

    public function senseImpostos()
    {
        $total = 0;
        foreach ($this->elementsFactura as $element) {
            $total += $element->getPreuSenseImpostos() * $element->getUnitats();
        }
        return $total;
    }

    public function getObservacions(): string
    {
        if (!isset($this->observacions) || $this->observacions == null) {
            return "";
        }
        return $this->observacions;
    }

    public function setObservacions(string $observacions): void
    {
        $this->observacions = $observacions;
    }

    public function getImportSpecificImpost($impost)
    {
        $total = 0;
        foreach ($this->elementsFactura as $element) {
            if ($element->getImpost() == $impost) {
                $total += ($element->getPreuAmbImpostos() - $element->getPreuSenseImpostos()) * $element->getUnitats();
            }
        }
        return $total;
    }

    public function getDetailedImportSpecificImpost($impost): array
    {
        $base = 0;
        $result = [];
        $amount = 0;
        foreach ($this->elementsFactura as $element) {
            if ($element->getImpost() == $impost) {
                $amount += ($element->getPreuAmbImpostos() - $element->getPreuSenseImpostos()) * $element->getUnitats();
                $base += $element->getPreuSenseImpostos() * $element->getUnitats();
            }
        }
        $result['TaxableBase'] = $base;
        $result['TaxAmount'] = $amount;
        return $result;
    }

    private function getTaxableInfo()
    {
        $taxes = $this->getImpostos();
        $info = [];
        foreach ($taxes as $tax) {
            $amount = $this->getDetailedImportSpecificImpost($tax);
            $info[] = [
                'TaxTypeCode' => $tax->getType(),
                'TaxRate' => $tax->getPercentatge(),
                'TaxableBase' => ['TotalAmount' => $amount['TaxableBase']], //['TotalAmount' => '100.00000000'
                'TaxAmount' => ['TotalAmount' => $amount['TaxAmount']],
            ];
        }
        return $info;

    }

    private function getItemXML()
    {
        $arr = [];
        foreach ($this->getElementsFactura() as $elem) {
            $arr[] = $elem->getXML();
        }
        return $arr;
    }

    public function getXML()
    {
        $dueDate = clone $this->getDataEmissio();
        $dueDate->modify('+30 days');
        $sinImpuestos = $this->senseImpostos();
        $data = [
            'FileHeader' => [
                'SchemaVersion' => '3.2.2',
                'Modality' => 'I',
                'InvoiceIssuerType' => 'EM',
                'Batch' => [
                    'BatchIdentifier' => $this->getEmisor()->getNIF() . $this->getNumero(),
                    'InvoicesCount' => 1,
                    'TotalInvoicesAmount' => ['TotalAmount' => $this->getTotal()],
                    'TotalOutstandingAmount' => ['TotalAmount' => $this->getTotal()],
                    'TotalExecutableAmount' => ['TotalAmount' => $this->getTotal()],
                    'InvoiceCurrencyCode' => 'EUR',
                ],
            ],
            'Parties' => [
                'SellerParty' => [
                    $this->getEmisor()->getXML(),
                ],
                'BuyerParty' => [
                    $this->getReceptor()->getXML(),
                ],
            ],
            'Invoices' => [
                'Invoice' => [
                    'InvoiceHeader' => [
                        'InvoiceNumber' => $this->getNumero(),
                        'InvoiceSeriesCode' => 'A',
                        'InvoiceDocumentType' => 'FC',
                        'InvoiceClass' => 'OO',
                    ],
                    'InvoiceIssueData' => [
                        'IssueDate' => $this->getDataEmissio()->format('Y-m-d'),
                        'PlaceOfIssue' => [
                            'PostCode' => $this->getEmisor()->getCP(),
                            'PlaceOfIssueDescription' => $this->getEmisor()->getCiutat(),
                        ],
                        'InvoiceCurrencyCode' => 'EUR',
                        'TaxCurrencyCode' => 'EUR',
                        'LanguageName' => 'es',
                    ],
                    'TaxesOutputs' => [
                        'Tax' => $this->getTaxableInfo()
                    ],
                    'InvoiceTotals' => [
                        'TotalGrossAmount' => $sinImpuestos,
                        'TotalGeneralDiscounts' => '0.00000000',
                        'TotalGeneralSurcharges' => '0.00',
                        'TotalGrossAmountBeforeTaxes' => $sinImpuestos,
                        'TotalTaxOutputs' => ($this->getTotal() - $sinImpuestos),
                        'TotalTaxesWithheld' => '0.00000000',
                        'InvoiceTotal' => $this->getTotal(),
                        'TotalFinancialExpenses' => '0.00',
                        'TotalOutstandingAmount' => $this->getTotal(),
                        'TotalExecutableAmount' => $this->getTotal(),
                    ],
                    'Items' => [
                        'InvoiceLine' => $this->getItemXML()
                    ],
                    'PaymentDetails' => [
                        'Installment' => [
                            'InstallmentDueDate' => $dueDate->format('Y-m-d'),
                            'InstallmentAmount' =>  number_format(max($this->getTotal(), $sinImpuestos), 2, '.', ''),
                            'PaymentMeans' => '04',
                            'AccountToBeCredited' => $this->getCompteBancari()->getXML(),
                        ],
                    ],
                    'AdditionalData' => [
                        'InvoiceAdditionalInformation' => $this->getObservacions(),
                    ],
                ],
            ],
        ];
        return $data;
    }

}
