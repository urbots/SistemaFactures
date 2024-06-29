<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Factura;


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
        return $this->render('factura/new.html.twig', [
            'controller_name' => 'FacturaController',
            'form' => $form->createView()
        ]);
    }


    #[Route('/factura/new', name: 'app_factura_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $factura = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($factura);
            $entityManager->flush();
            return $this->redirectToRoute('app_factura_show', ['id' => $factura->getId()]);
        }
        return $this->render('factura/new.html.twig', [
            'controller_name' => 'FacturaController',
            'form' => $form->createView(),
            'errors' => $form->getErrors()
        ]);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(): \Symfony\Component\Form\FormInterface
    {
        $form = $this->createFormBuilder()
            ->add('dataEmissio', DateType::class, ['label' => 'Data d\'emissió'])
            ->add('total', IntegerType::class, ['label' => 'Total'])
            ->add('urlPDF', TextType::class, ['label' => 'URL PDF'])
            ->add('elements', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => ['class' => 'App\Entity\ElementFactura'],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Elements'
            ])
            ->add('compteBancari', EntityType::class, ['label' => 'Compte bancari', 'class' => 'App\Entity\CompteBancari'])
            ->add('emisor', EntityType::class, ['label' => 'Emisor', 'class' => 'App\Entity\PersonaEmpresa'])
            ->add('receptor', EntityType::class, ['label' => 'Receptor', 'class' => 'App\Entity\PersonaEmpresa'])
            ->add('save', SubmitType::class, ['label' => 'Crear factura'])
            ->getForm();
        return $form;
    }
}
