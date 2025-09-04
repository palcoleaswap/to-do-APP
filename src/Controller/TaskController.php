<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Tag;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar tags del campo de texto
            $tagNames = $form->get('tagNames')->getData();
            
            if ($tagNames) {
                $this->processTags($tagNames, $task, $entityManager);
            }

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        // Pre-rellenar el campo tagNames con los tags existentes
        $existingTagNames = [];
        foreach ($task->getTags() as $tag) {
            $existingTagNames[] = $tag->getName();
        }
        
        $form = $this->createForm(TaskType::class, $task);
        
        // Pre-rellenar el campo tagNames si hay tags existentes
        if (!empty($existingTagNames)) {
            $form->get('tagNames')->setData(implode(', ', $existingTagNames));
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Limpiar tags existentes
            $task->getTags()->clear();
            
            // Procesar nuevos tags del campo de texto
            $tagNames = $form->get('tagNames')->getData();
            
            if ($tagNames) {
                $this->processTags($tagNames, $task, $entityManager);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Método helper para procesar tags desde el campo de texto
     */
    private function processTags(string $tagNames, Task $task, EntityManagerInterface $entityManager): void
    {
        $tagNamesArray = array_map('trim', explode(',', $tagNames));
        
        foreach ($tagNamesArray as $tagName) {
            if (!empty($tagName)) {
                // Buscar si el tag ya existe
                $tag = $entityManager->getRepository(Tag::class)
                    ->findOneBy(['name' => $tagName]);
                
                // Si no existe, crearlo
                if (!$tag) {
                    $tag = new Tag();
                    $tag->setName($tagName);
                    $entityManager->persist($tag);
                }
                
                // Añadir el tag a la tarea (solo si no está ya añadido)
                if (!$task->getTags()->contains($tag)) {
                    $task->addTag($tag);
                }
            }
        }
    }
}