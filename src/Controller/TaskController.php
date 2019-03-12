<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends Controller
{
    /**
     * Index list action.
     *
     * @Route("/", name="task_index")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->manageTaskForm($request);

        return $this->renderTaskList($form);
    }

    /**
     * Trashed list action.
     *
     * @Route("/trashed", name="task_trashed")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trashedAction(Request $request)
    {
        $form = $this->manageTaskForm($request);

        return $this->renderTaskList($form, 'trashed');
    }

    /**
     * Add action.
     *
     * @Route("/add", name="task_add")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        if (null === $form = $this->manageTaskForm($request)) {
            return $this->redirectToRoute('task_index');
        }

        return $this->renderTaskList($form);
    }

    /**
     * Edit action.
     *
     * @Route("/{id}/edit", name="task_edit")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $task = $this->findTaskByRequest($request);

        $form = $this->manageTaskForm($request, $task, 'edit');

        if (null === $form) {
            return $this->redirectToRoute('task_index');
        }

        return $this->renderTaskList($form, 'edit');
    }

    /**
     * Trash action.
     *
     * @Route("/{id}/trash", name="task_trash")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trashAction($id)
    {
        $task = $this->findTaskById($id);

        $task->setTrashed(true);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('task_index');
    }

    /**
     * Restore action.
     *
     * @Route("/{id}/restore", name="task_restore")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function restoreAction($id)
    {
        $task = $this->findTaskById($id);

        $task->setTrashed(false);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('task_trashed');
    }

    /**
     * Remove action.
     *
     * @Route("/{id}/remove", name="task_remove")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($id)
    {
        $task = $this->findTaskById($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('task_trashed');
    }

    /**
     * Creates the Task form and handle the request.
     *
     * This method returns null if the task has been persisted.
     * Otherwise it returns the form view.
     *
     * @param Task $task
     * @param Request $request
     * @param string $mode
     *
     * @return null|\Symfony\Component\Form\FormInterface
     */
    private function manageTaskForm(Request $request, Task $task = null, $mode = 'add')
    {
        if (!$task) {
            $task = new Task();
        }
        if ($mode != 'add' && 0 >= $task->getId()) {
            throw new \InvalidArgumentException('Task\'s id must be defined and greater than zero.');
        }

        switch($mode) {
            case 'edit':
                $formAction = $this->generateUrl('task_edit', ['id' => $task->getId()]);
                $buttonOptions = [
                    'label' => 'Modifier',
                    'attr' => [
                        'class' => 'btn btn-warning',
                    ]
                ];
                break;

            default:
                $formAction = $this->generateUrl('task_add');
                $buttonOptions = [
                    'label' => 'Ajouter',
                    'attr' => [
                        'class' => 'btn btn-success',
                    ]
                ];
        }

        $form = $this
            ->createForm(TaskType::class, $task, [
                'action' => $formAction,
            ])
            ->add('submit', SubmitType::class, $buttonOptions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return null;
        }

        return $form;
    }

    /**
     * @param FormInterface $form
     * @param string $mode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderTaskList(FormInterface $form, $mode = 'add')
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository(Task::class)
            ->findBy(
                ['trashed' => $mode === 'trashed'],
                ['date' => 'DESC']
            );

        return $this->render('Task/index.html.twig', [
            'form'  => $form->createView(),
            'mode'  => $mode,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Finds the Task by request.
     *
     * @param Request $request
     *
     * @return Task
     */
    private function findTaskByRequest(Request $request)
    {
        return $this->findTaskById($request->attributes->get('id'));
    }

    /**
     * Finds the Task by its id.
     *
     * @param int $id
     *
     * @return Task
     */
    private function findTaskById($id)
    {
        /** @var Task $task */
        $task = $this
            ->getDoctrine()
            ->getRepository(Task::class)
            ->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        return $task;
    }
}
