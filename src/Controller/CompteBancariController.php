<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompteBancariController extends AbstractController
{
    #[Route('/compte/bancari', name: 'app_compte_bancari')]
    public function index(): Response
    {
        return $this->render('compte_bancari/index.html.twig', [
            'controller_name' => 'CompteBancariController',
        ]);
    }

    public function getForm()
    {
        return $this->createFormBuilder()
            ->add('Entitat', TextType::class)
            ->add('Referencia', TextType::class)
            ->add('SWIFT', TextType::class)
            ->add('IBAN', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Compte Bancari'])
            ->getForm();
    }
}
