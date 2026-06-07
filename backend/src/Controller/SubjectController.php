<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/subjects')]
final class SubjectController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository): JsonResponse
    {
        $subjects = $subjectRepository->findAll();

        return $this->json($subjects);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, SubjectRepository $subjectRepository): JsonResponse
    {
        $subject = $subjectRepository->find($id);
        
        if (!$subject) {
            return $this->json(
                ['message' => 'Subject not found'],
                404
            );
        }

        return $this->json($subject);
    }
}
