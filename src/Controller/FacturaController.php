<?php

namespace App\Controller;

use App\Entity\ElementFactura;
use App\Entity\Elements;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Factura;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


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
        return $this->render('factura/index.html.twig', [
            'controller_name' => 'FacturaController',
            'factures' => $factures
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
        $pdf = new \TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola');
        $pdf->SetTitle('Factura');
        $pdf->SetSubject('Factura');
        $pdf->SetKeywords('Factura');
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('factura.pdf', 'I');
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
        $response->headers->set('Content-Disposition', 'attachment; filename="factura.xml"');

        return $response;

    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(): \Symfony\Component\Form\FormInterface
    {
        $form = $this->createFormBuilder()
            ->add('dataEmissio', DateType::class, ['label' => 'Data d\'emissió', 'widget' => 'single_text'])
            ->add('total', IntegerType::class, ['label' => 'Total'])
            ->add('compteBancari', EntityType::class, ['label' => 'Compte bancari', 'class' => 'App\Entity\CompteBancari', 'choice_label' => 'Referencia'])
            ->add('emisor', EntityType::class, ['label' => 'Emisor', 'class' => 'App\Entity\PersonaEmpresa'])
            ->add('receptor', EntityType::class, ['label' => 'Receptor', 'class' => 'App\Entity\PersonaEmpresa'])
            ->add('Observacions', TextAreaType::class, ['label' => 'Observacions'])
            ->add('save', SubmitType::class, ['label' => 'Crear factura'])
            ->getForm();
        return $form;
    }
}
