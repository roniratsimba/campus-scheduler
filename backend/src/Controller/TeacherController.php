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

        return $this->json(array_map(
            fn(Teacher $teacher) => [
                'id' => $teacher->getId(),
                'firstName' => $teacher->getFirstName(),
                'lastName' => $teacher->getLastName(),
                'email' => $teacher->getEmail(),
                'active' => $teacher->isActive(),
            ],
            $teachers
        ));
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
            'active' => $teacher->isActive(),
        ]);
    }
}