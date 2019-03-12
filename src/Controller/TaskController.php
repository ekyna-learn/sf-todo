<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends Controller
{
    /**
     * @Route("/", name="task_index")
     */
    public function indexAction()
    {
        $form = $this
            ->createFormBuilder()
            ->add('content', TextareaType::class, [
                'label' => false,
            ])
            ->getForm();

        return $this->render('Task/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
