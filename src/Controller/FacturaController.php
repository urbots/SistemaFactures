<?php

namespace App\Controller;

use App\Entity\CompteBancari;
use App\Entity\ElementFactura;
use App\Entity\Elements;
use App\Entity\Persona;
use DateTime;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Factura;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use TCPDF;


class FacturaController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function getDoctrine()
    {
        return $this->doctrine;
    }
    #[Route('/factura', name: 'app_factura')]
    public function index(): Response
    {
        $factures = $this->getDoctrine()->getRepository(Factura::class)->findAll();
        $formCSV = $this->getFormFromCSV();
        return $this->render('factura/index.html.twig', [
            'controller_name' => 'FacturaController',
            'factures' => $factures,
            'formCSV' => $formCSV->createView()
        ]);
    }

    #[Route('/factura/new', name: 'app_factura_new', methods: ['GET'])]
    public function new(): Response
    {
        $form = $this->getForm();
        $productes = $this->getDoctrine()->getRepository(Elements::class)->findAll();
        $imposts = $this->getDoctrine()->getRepository('App\Entity\Impost')->findAll();
        return $this->render('factura/new.html.twig', [
            'controller_name' => 'FacturaController',
            'form' => $form->createView(),
            'productes' => $productes,
            'imposts' => $imposts
        ]);
    }


    #[Route('/factura/new', name: 'app_factura_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $req = $_POST;
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $factura = new Factura();
            $data = $form->getData();
            $factura->setDataEmissio($data['dataEmissio']);
            $factura->setYear($data['dataEmissio']->format('Y'));
            //buscamos el numero de factura mas alto de ese año
            $factures = $this->getDoctrine()->getRepository(Factura::class)->findBy(['year' => $factura->getYear()]);
            $max = 0;
            foreach ($factures as $facture) {
                $num = intval($facture->getNumFactura());
                if ($num > $max) {
                    $max = $num;
                }
            }
            $factura->setNumFactura($max + 1);
            $factura->setTotal($data['total']);
            $emisor = $this->getDoctrine()->getRepository('App\Entity\PersonaEmpresa')->find($data['emisor']);
            $factura->setEmisor($emisor);
            $receptor = $this->getDoctrine()->getRepository('App\Entity\PersonaEmpresa')->find($data['receptor']);
            $factura->setReceptor($receptor);
            $compteBancari = $this->getDoctrine()->getRepository('App\Entity\CompteBancari')->find($data['compteBancari']);
            $factura->setCompteBancari($compteBancari);
            foreach ($req['productes'] as $idx => $producte) {
                $prod = $this->getDoctrine()->getRepository('App\Entity\Elements')->find($producte);
                $element = new ElementFactura();
                $element->setUnitats($req['quantitats'][$idx]);
                $element->setPreuSenseImpostos($req['preusSenseImpostos'][$idx]);
                $impost = $this->getDoctrine()->getRepository('App\Entity\Impost')->find($req['impostos'][$idx]);
                $element->setImpost($impost);
                $element->setPreuAmbImpostos($req['preusAmbImpostos'][$idx]);
                $element->setElements($prod);
                $element->setFactura($factura);
                $factura->addElement($element);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($factura);
            $entityManager->flush();
            return $this->redirectToRoute('app_factura_show', ['id' => $factura->getId()]);
        }
        $this->addFlash('error', 'Error al crear la factura'.$form->getErrors(true));
        return $this->redirectToRoute('app_factura_new');
    }

    #[Route('/factura/{id}', name: 'app_factura_show', methods: ['GET'])]
    public function show($id): Response
    {
        $factura = $this->getDoctrine()->getRepository(Factura::class)->find($id);
        return $this->render('factura/show.html.twig', [
            'controller_name' => 'FacturaController',
            'factura' => $factura
        ]);
    }

    #[Route('/factura/{id}/edit', name: 'app_factura_edit', methods: ['GET'])]
    public function edit($id): Response
    {
        $factura = $this->getDoctrine()->getRepository(Factura::class)->find($id);
        $form = $this->getForm();
        return $this->render('factura/edit.html.twig', [
            'controller_name' => 'FacturaController',
            'factura' => $factura,
            'form' => $form->createView()
        ]);
    }

    #[Route('/factura/{id}/pdf', name: 'app_factura_pdf', methods: ['GET'])]
    public function pdf($id): Response
    {
        $factura = $this->getDoctrine()->getRepository(Factura::class)->find($id);
        $html = $this->renderView('factura/pdf.html.twig', [
            'factura' => $factura
        ]);
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($factura->getEmisor()->getNomComplet());
        $pdf->SetTitle('Factura');
        $pdf->SetSubject('Factura');
        $pdf->SetKeywords('Factura');
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('FAC-'.urlencode($factura->getEmisor()->getNomComplet()).'-'.$factura->getNumFactura().'.pdf', 'D');
        return new Response();
    }

    #[Route('/factura/{id}/xml', name: 'app_factura_xml', methods: ['GET'])]
    public function xml($id): Response
    {
        $factura = $this->getDoctrine()->getRepository(Factura::class)->find($id);
        $data = $factura->getXML();
        $encoders = [new XmlEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $xmlContent = $serializer->serialize($data, 'xml', [
            XmlEncoder::ROOT_NODE_NAME => 'Facturae',
            XmlEncoder::ENCODING => 'UTF-8',
            XmlEncoder::STANDALONE => true,
            'xml_format_output' => true,
        ]);

        // Añadir los atributos manualmente al nodo raíz
        $xmlContent = str_replace('<Facturae>', '<fe:Facturae xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:fe="http://www.facturae.gob.es/formato/Versiones/Facturaev3_2_2.xml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">', $xmlContent);
        $xmlContent = str_replace('</Facturae>', '</fe:Facturae>', $xmlContent);

        $response = new Response($xmlContent);
        $response->headers->set('Content-Type', 'application/xml');
        $response->headers->set('Content-Disposition', 'attachment; filename="FAC-'.urlencode($factura->getEmisor()->getNomComplet()).'-'.$factura->getNumFactura().'.xml"');

        return $response;

    }

    // Crear factura a partir de un csv

    public function getFormFromCSV(): \Symfony\Component\Form\FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_factura_csv_create'))
            ->add('csv', FileType::class, ['label' => 'Fitxer CSV','attr'=>[ 'class' => 'form-control']])
            ->add('posarMateixNumero', CheckboxType::class, ['label' => 'Posar el mateix número de factura', 'required' => false,'attr'=>[ 'class' => 'form-check-input', 'onchange' => 'showNumFactura()']])
            ->add('numFactura', IntegerType::class, ['label' => 'Número de factura', 'required' => false,'attr'=>[ 'class' => 'form-control']])
            ->add('dataEmissio', DateType::class, ['label' => 'Data d\'emissió', 'widget' => 'single_text','attr'=>[ 'class' => 'form-control']])
            ->add('Comentari', TextareaType::class, ['label' => 'Comentari', 'required' => false,'attr'=>[ 'class' => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Crear factura','attr'=>[ 'class' => 'btn btn-primary']])
            ->getForm();
    }

    // Crear factura a partir de un csv
    #[Route('/factura/csv', name: 'app_factura_csv_create', methods: ['POST'])]
    public function generateFromCSV(Request $request): Response
    {
        $form = $this->getFormFromCSV();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //llegim el fitxer del formulari
            $file = $form->getData()['csv'];
            $data = file_get_contents($file);
            $lines = explode("\n", $data);
            $i = 0;
            //start transaction
            $this->getDoctrine()->getConnection()->beginTransaction();
            foreach ($lines as $line){
                //ignorem la primera linia
                if ($i == 0) {
                    $i++;
                    continue;
                }
                //primeres columnes Nom	Cognom 1	Cognom 2	Adreça	Codi Postal	Ciutat/Poble	Provincia	NIF (DNI / NIE )	Correu electrònic (NO URV)	Teléfon
                //creem una persona
                $persona = new Persona();
                $cols = explode(';', $line);
                if(count($cols) < 16){
                    error_log('Linea '.$i.' no te suficients columnes');
                    continue;
                }
                $persona->setNom(mb_convert_encoding($cols[0], 'UTF-8', 'auto'));
                $persona->setCognom1(mb_convert_encoding($cols[1], 'UTF-8', 'auto'));
                $persona->setCognom2(mb_convert_encoding($cols[2], 'UTF-8', 'auto'));
                $persona->setCarrer(mb_convert_encoding($cols[3], 'UTF-8', 'auto'));
                $persona->setCP($cols[4]);
                $persona->setCiutat($cols[5]);
                $persona->setProvincia($cols[6]);
                $persona->setNIF($cols[7]);
                //de moment no posem correu ni telefon
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($persona);
                $entityManager->flush();

                //IBAN	SWIFT	Entitat, creem un compte bancari amb referencia COMPTE_NOM_COGNOM1_COGNOM2
                $compte = new CompteBancari();
                $compte->setIBAN($cols[10]);
                $compte->setSWIFT($cols[11]);
                $compte->setEntitat($cols[12]);
                $compte->setReferencia('COMPTE_'.$persona->getNom().'_'.$persona->getCognom1().'_'.$persona->getCognom2());
                $entityManager->persist($compte);
                $entityManager->flush();

                //Id_element	Quantitat
                $producte = $this->getDoctrine()->getRepository(Elements::class)->findOneBy(['id' => $cols[13]]);
                if($producte == null){
                    error_log('Producte amb id '.$cols[11].' no trobat');
                }
                $element = new ElementFactura();
                $quantitat = intval($cols[14]);
                $element->setUnitats($quantitat);
                $element->setPreuSenseImpostos($producte->getPreuSenseImpostos() * $quantitat);
                $element->setImpost($producte->getImpost());
                $element->setPreuAmbImpostos($producte->getPreuUnitari() * $quantitat);
                $element->setElements($producte);

                //Id_Receptor
                $receptor = $this->getDoctrine()->getRepository('App\Entity\PersonaEmpresa')->findOneBy(['id' => $cols[15]]);
                if($receptor == null){
                    error_log('Receptor amb id '.$cols[13].' no trobat');
                }

                //Creem la factura
                $factura = new Factura();
                $factura->setDataEmissio($form->getData()['dataEmissio']);
                $factura->setYear($form->getData()['dataEmissio']->format('Y'));
                //si está marcat posarMateixNumero treiem el número de la factura del formulari
                if ($form->getData()['posarMateixNumero']) {
                    $factura->setNumFactura($form->getData()['numFactura']);
                } else {
                    //buscamos el numero de factura mas alto de ese año
                    $factures = $this->getDoctrine()->getRepository(Factura::class)->findBy(['year' => $factura->getYear()]);
                    $max = 0;
                    foreach ($factures as $facture) {
                        $num = intval($facture->getNumFactura());
                        if ($num > $max) {
                            $max = $num;
                        }
                    }
                    $factura->setNumFactura($max + 1);
                }
                $factura->setTotal($element->getPreuAmbImpostos());
                $factura->setEmisor($persona);
                $factura->setReceptor($receptor);
                $factura->setCompteBancari($compte);
                $factura->addElement($element);
                $factura->setObservacions($form->getData()['Comentari']);
                $element->setFactura($factura);
                $entityManager->persist($factura);
                $entityManager->flush();
            }
            $entityManager->commit();
        }
        return $this->redirectToRoute('app_factura');
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(): \Symfony\Component\Form\FormInterface
    {
        $form = $this->createFormBuilder()
            ->add('dataEmissio', DateType::class, ['label' => 'Data d\'emissió', 'widget' => 'single_text'])
            ->add('total', NumberType::class, ['label' => 'Total'])
            ->add('compteBancari', EntityType::class, ['label' => 'Compte bancari', 'class' => 'App\Entity\CompteBancari', 'choice_label' => 'Referencia'])
            ->add('emisor', EntityType::class, ['label' => 'Emisor', 'class' => 'App\Entity\PersonaEmpresa'])
            ->add('receptor', EntityType::class, ['label' => 'Receptor', 'class' => 'App\Entity\PersonaEmpresa'])
            ->add('Observacions', TextAreaType::class, ['label' => 'Observacions', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'Crear factura'])
            ->getForm();
        return $form;
    }
}
