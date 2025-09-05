<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskFile;
use App\Form\TaskFileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task/{taskId}/files')]
class TaskFileController extends AbstractController
{
    #[Route('/', name: 'app_task_files', methods: ['GET', 'POST'])]
    public function index(int $taskId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($taskId);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $taskFile = new TaskFile();
        $form = $this->createForm(TaskFileType::class, $taskFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskFile->setTask($task);

            $entityManager->persist($taskFile);
            $entityManager->flush();

            $this->addFlash('success', 'File uploaded successfully!');
            return $this->redirectToRoute('app_task_files', ['taskId' => $taskId]);
        }

        return $this->render('task_file/index.html.twig', [
            'task' => $task,
            'files' => $task->getTaskFiles(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/download/{id}', name: 'app_task_file_download', methods: ['GET'])]
    public function download(TaskFile $taskFile): Response
    {
        $filePath = $this->getParameter('task_files_directory') . '/' . $taskFile->getFileName();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        return $this->file($filePath, $taskFile->getOriginalName());
    }

    #[Route('/delete/{id}', name: 'app_task_file_delete', methods: ['POST'])]
    public function delete(Request $request, TaskFile $taskFile, EntityManagerInterface $entityManager): Response
    {
        $taskId = $taskFile->getTask()->getId();

        if ($this->isCsrfTokenValid('delete' . $taskFile->getId(), $request->request->get('_token'))) {
            // Delete physical file
            $filePath = $this->getParameter('task_files_directory') . '/' . $taskFile->getFileName();
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $entityManager->remove($taskFile);
            $entityManager->flush();

            $this->addFlash('success', 'File deleted successfully!');
        }

        return $this->redirectToRoute('app_task_files', ['taskId' => $taskId]);
    }
}
