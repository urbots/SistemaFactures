<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ElementsController extends AbstractController
{
    #[Route('/elements', name: 'app_elements')]
    public function index(): Response
    {
        return $this->render('elements/index.html.twig', [
            'controller_name' => 'ElementsController',
        ]);
    }

    public function getForm()
    {
        return $this->createFormBuilder()
            ->add('Concepte', TextType::class)
            ->add('preuUnitari', IntegerType::class)
            ->add('preuSenseImpostos', IntegerType::class)
            ->add('Impost', IntegerType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Element'])
            ->getForm();
    }
}
