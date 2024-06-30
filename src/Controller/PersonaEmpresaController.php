<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Entity\Persona;
use App\Entity\PersonaEmpresa;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Choice;

class PersonaEmpresaController extends AbstractController
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
    #[Route('/persona/empresa', name: 'app_persona_empresa')]
    public function index(): Response
    {
        $personesEmpresa = $this->getDoctrine()->getRepository(PersonaEmpresa::class)->findAll();
        return $this->render('persona_empresa/index.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
            'personesEmpresa' => $personesEmpresa
        ]);
    }

    #[Route('/persona/empresa/new', name: 'persona_empresa_new', methods: ['GET'])]
    public function new(): Response
    {
        $form = $this->getForm();
        return $this->render('persona_empresa/new.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/persona/empresa/new', name: 'persona_empresa_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $personaEmpresa = $form->getData();
            if ($form->get('type')->getData() == 'Persona') {
                $personaEmpresa = new Persona();
                $personaEmpresa->setNom($form->get('nom')->getData());
                $personaEmpresa->setCognom1($form->get('cognom1')->getData());
                $personaEmpresa->setCognom2($form->get('cognom2')->getData());
            } else {
                $personaEmpresa = new Empresa();
                $personaEmpresa->setRaoSocial($form->get('RaoSocial')->getData());
                $personaEmpresa->setNomComercial($form->get('NomComercial')->getData());
            }
            $personaEmpresa->setNIF($form->get('NIF')->getData());
            $personaEmpresa->setCarrer($form->get('Carrer')->getData());
            $personaEmpresa->setCiutat($form->get('Ciutat')->getData());
            $personaEmpresa->setCP($form->get('CP')->getData());
            $personaEmpresa->setProvincia($form->get('Provincia')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personaEmpresa);
            $entityManager->flush();
            return $this->redirectToRoute('app_persona_empresa');
        }
        return $this->render('persona_empresa/new.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/persona/empresa/{id}', name: 'persona_empresa_show')]
    public function show($id): Response
    {
        $personaEmpresa = $this->getDoctrine()->getRepository(PersonaEmpresa::class)->find($id);
        return $this->render('persona_empresa/show.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
            'personaEmpresa' => $personaEmpresa
        ]);
    }

    #[Route('/persona/empresa/{id}/edit', name: 'persona_empresa_edit', methods: ['GET'])]
    public function edit($id): Response
    {
        $personaEmpresa = $this->getDoctrine()->getRepository(PersonaEmpresa::class)->find($id);
        $form = $this->getForm(true);
        if ($personaEmpresa instanceof Persona) {
            $form->get('type')->setData('Persona');
            $form->get('nom')->setData($personaEmpresa->getNom());
            $form->get('cognom1')->setData($personaEmpresa->getCognom1());
            $form->get('cognom2')->setData($personaEmpresa->getCognom2());
        } else {
            $form->get('type')->setData('Empresa');
            $form->get('RaoSocial')->setData($personaEmpresa->getRaoSocial());
            $form->get('NomComercial')->setData($personaEmpresa->getNomComercial());
        }
        $form->get('NIF')->setData($personaEmpresa->getNIF());
        $form->get('Carrer')->setData($personaEmpresa->getCarrer());
        $form->get('Ciutat')->setData($personaEmpresa->getCiutat());
        $form->get('CP')->setData($personaEmpresa->getCP());
        $form->get('Provincia')->setData($personaEmpresa->getProvincia());
        return $this->render('persona_empresa/edit.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
            'form' => $form->createView(),
            'personaEmpresa' => $personaEmpresa
        ]);
    }

    #[Route('/persona/empresa/{id}/edit', name: 'persona_empresa_update', methods: ['POST'])]
    public function update($id, Request $request): Response
    {
        $personaEmpresa = $this->getDoctrine()->getRepository(PersonaEmpresa::class)->find($id);
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($personaEmpresa instanceof Persona) {
                $personaEmpresa->setNom($form->get('nom')->getData());
                $personaEmpresa->setCognom1($form->get('cognom1')->getData());
                $personaEmpresa->setCognom2($form->get('cognom2')->getData());
            } else {
                $personaEmpresa->setRaoSocial($form->get('RaoSocial')->getData());
                $personaEmpresa->setNomComercial($form->get('NomComercial')->getData());
            }
            $personaEmpresa->setNIF($form->get('NIF')->getData());
            $personaEmpresa->setCarrer($form->get('Carrer')->getData());
            $personaEmpresa->setCiutat($form->get('Ciutat')->getData());
            $personaEmpresa->setCP($form->get('CP')->getData());
            $personaEmpresa->setProvincia($form->get('Provincia')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personaEmpresa);
            $entityManager->flush();
            return $this->redirectToRoute('app_persona_empresa');
        }
        return $this->render('persona_empresa/edit.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/persona/empresa/{id}/delete', name: 'persona_empresa_delete', methods: ['GET'])]
    public function delete($id): Response
    {
        $personaEmpresa = $this->getDoctrine()->getRepository(PersonaEmpresa::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($personaEmpresa);
        $entityManager->flush();
        return $this->redirectToRoute('app_persona_empresa');
    }


    public function getForm($update = false)
    {
        $form = $this->createFormBuilder()
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Selecciona un tipus' => '',
                    'Persona' => 'Persona',
                    'Empresa' => 'Empresa',
                    'Empresa Publica' => 'Empresa Publica'
                ],
                'label' => 'Tipus',
                'constraints' => [
                    new Choice(['choices' => ['Persona', 'Empresa', 'Empresa Publica']])
                ]
            ])
            ->add('nom', TextType::class, ['label' => 'Nom','required' => false])
            ->add('cognom1', TextType::class, ['label' => 'Cognoms','required' => false])
            ->add('cognom2', TextType::class, ['label' => 'Cognoms','required' => false])
            ->add('RaoSocial', TextType::class, ['label' => 'Rao Social','required' => false])
            ->add('NomComercial', TextType::class, ['label' => 'Nom Comercial','required' => false])
            ->add('NIF', TextType::class, ['label' => 'NIF'])
            ->add('Carrer', TextType::class, ['label' => 'Carrer'])
            ->add('Ciutat', TextType::class, ['label' => 'Ciutat'])
            ->add('CP', IntegerType::class, ['label' => 'CP'])
            ->add('Provincia', TextType::class, ['label' => 'Provincia']);
        if ($update) {
            $form->add('save', SubmitType::class, ['label' => 'Actualitzar']);
        } else {
            $form->add('save', SubmitType::class, ['label' => 'Guardar']);
        }
        return $form->getForm();

    }
}
