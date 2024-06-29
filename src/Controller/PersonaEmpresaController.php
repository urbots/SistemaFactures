<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonaEmpresaController extends AbstractController
{
    #[Route('/persona/empresa', name: 'app_persona_empresa')]
    public function index(): Response
    {
        return $this->render('persona_empresa/index.html.twig', [
            'controller_name' => 'PersonaEmpresaController',
        ]);
    }

    public function getForm()
    {
        return $this->createFormBuilder()
            ->add('NomComplet', TextType::class)
            ->add('NIF', TextType::class)
            ->add('Carrer', TextType::class)
            ->add('Ciutat', TextType::class)
            ->add('CP', IntegerType::class)
            ->add('Provincia', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Persona Empresa'])
            ->getForm();
    }
}
