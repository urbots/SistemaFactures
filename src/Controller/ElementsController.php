<?php

namespace App\Controller;

use App\Entity\Elements;
use App\Entity\Impost;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ElementsController extends AbstractController
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
    #[Route('/elements', name: 'app_elements')]
    public function index(): Response
    {
        $elements = $this->getDoctrine()->getRepository(Elements::class)->findAll();
        return $this->render('elements/index.html.twig', [
            'controller_name' => 'ElementsController',
            'elements' => $elements
        ]);
    }

    #[Route('/elements/new', name: 'elements_new', methods: ['GET'])]
    public function new(): Response
    {
        $form = $this->getForm();
        return $this->render('elements/new.html.twig', [
            'controller_name' => 'ElementsController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/elements/new', name: 'elements_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $elements = $form->getData();
            $elements = new Elements();
            $elements->setConcepte($form->get('Concepte')->getData());
            $elements->setpreuUnitari($form->get('preuUnitari')->getData());
            $elements->setpreuSenseImpostos($form->get('preuSenseImpostos')->getData());
            $elements->setImpost($form->get('Impost')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($elements);
            $entityManager->flush();
            return $this->redirectToRoute('app_elements');
        }
        return $this->render('elements/new.html.twig', [
            'controller_name' => 'ElementsController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/elements/{id}', name: 'elements_show')]
    public function show($id): Response
    {
        $elements = $this->getDoctrine()->getRepository(Elements::class)->find($id);
        return $this->render('elements/show.html.twig', [
            'controller_name' => 'ElementsController',
            'element' => $elements
        ]);
    }

    #[Route('/elements/{id}/edit', name: 'elements_edit', methods: ['GET'])]
    public function edit($id): Response
    {
        $elements = $this->getDoctrine()->getRepository(Elements::class)->find($id);
        $form = $this->getForm();
        $form->get('Concepte')->setData($elements->getConcepte());
        $form->get('preuUnitari')->setData($elements->getpreuUnitari());
        $form->get('preuSenseImpostos')->setData($elements->getpreuSenseImpostos());
        $form->get('Impost')->setData($elements->getImpost());
        return $this->render('elements/edit.html.twig', [
            'controller_name' => 'ElementsController',
            'form' => $form->createView(),
            'element' => $elements
        ]);
    }

    #[Route('/elements/{id}/edit', name: 'elements_update', methods: ['POST'])]
    public function update($id, Request $request): Response
    {
        $elements = $this->getDoctrine()->getRepository(Elements::class)->find($id);
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $elements->setConcepte($form->get('Concepte')->getData());
            $elements->setpreuUnitari($form->get('preuUnitari')->getData());
            $elements->setpreuSenseImpostos($form->get('preuSenseImpostos')->getData());
            $elements->setImpost($form->get('Impost')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($elements);
            $entityManager->flush();
            return $this->redirectToRoute('app_elements');
        }
        return $this->render('elements/edit.html.twig', [
            'controller_name' => 'ElementsController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/elements/{id}/delete', name: 'elements_delete', methods: ['GET'])]
    public function delete($id): Response
    {
        $elements = $this->getDoctrine()->getRepository(Elements::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($elements);
        $entityManager->flush();
        return $this->redirectToRoute('app_elements');
    }

    public function getForm()
    {
        return $this->createFormBuilder()
            ->add('Concepte', TextareaType::class)
            ->add('preuUnitari', NumberType::class, ['scale' => 2])
            ->add('preuSenseImpostos', NumberType::class, ['scale' => 2])
            ->add('Impost', EntityType::class, [
                'class' => Impost::class,
                'choice_label' => 'nom'
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Element'])
            ->getForm();
    }
}
