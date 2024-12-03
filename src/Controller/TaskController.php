<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Status;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route("", methods: ["GET"], name: "list")]
    public function listTasks(Request $request, TaskRepository $repository): JsonResponse
    {
        $page = (int) $request->query->get("page", 1);
        $status = $request->query->get('status', null);
        $search = $request->query->get('search', null);

        $paginator = $repository->findPaginatedAndSortedTasks($page, $status, $search);

        $tasks = [];
        foreach ($paginator as $task) {
            $tasks[] = [
                "id" => $task->getId(),
                "title" => $task->getTitle(),
                "description" => $task->getDescription(),
                "status" => $task->getStatus(),
                "created_at" => $task->getCreatedAt(),
                "updated_at" => $task->getUpdatedAt(),
            ];
        }

        return new JsonResponse([
            "tasks" => $tasks,
            "page" => $page,
            "total" => count($paginator)
        ]);
    }

    
    #[Route("/tasks", methods: ["POST"])] 
    public function createTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title'] ?? null);
        $task->setDescription($data['description'] ?? null);

        $task->setStatus(Status::from($data['status'] ?? Status::TODO));
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTime());

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Task created successfully', 'id' => $task->getId()], 201);
    }

    #[Route("/tasks/{id}", methods: ["PUT"])]
    public function updateTask(int $id, Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $task->setTitle($data['title'] ?? $task->getTitle());
        $task->setDescription($data['description'] ?? $task->getDescription());
        $task->setStatus(Status::from($data['status'] ?? Status::TODO));
        $task->setUpdatedAt(new \DateTime());

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Task updated successfully']);
    }

    
    #[Route("/tasks/{id}", methods: ["DELETE"])]
    public function deleteTask(int $id, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Task deleted successfully'], 204);
    }
}
