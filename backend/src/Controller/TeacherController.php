<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/teachers')]
final class TeacherController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(TeacherRepository $teacherRepository): JsonResponse
    {
        $teachers = $teacherRepository->findAll();

        return $this->json($teachers);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TeacherRepository $teacherRepository): JsonResponse
    {
        $teacher = $teacherRepository->find($id);

        if (!$teacher) {
            return $this->json(
                ['message' => 'Teacher not found'],
                404
            );
        }

        return $this->json([
            'id' => $teacher->getId(),
            'firstName' => $teacher->getFirstName(),
            'lastName' => $teacher->getLastName(),
            'email' => $teacher->getEmail(),
            'isActive' => $teacher->isActive(),
        ]);
    }
    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $teacher = new Teacher();

        $teacher->setFirstName($data['firstName']);
        $teacher->setLastName($data['lastName'] ?? null);
        $teacher->setEmail($data['email']);
        $teacher->setIsActive($data['isActive'] ?? true);

        $entityManager->persist($teacher);
        $entityManager->flush();

        return $this->json([
            'id' => $teacher->getId(),
            'message' => 'Teacher created'
        ], 201);
    }
}