<?php

namespace App\Controller;

use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MicroPostController extends AbstractController
{
    public function __construct(protected readonly MicroPostRepository $microPostRepository)
    {
    }

    #[Route('/micro-post/page/{page<\d+>?1}', name: 'app_micro_post_index', methods: 'GET')]
    public function index(int $page): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'microPosts' => $this->microPostRepository->list($page)
        ]);
    }

    #[Route('/micro-post/{id<\d+>?1}', name: 'app_micro_post_show', methods: 'GET')]
    public function show(int $id): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'microPost' => $this->microPostRepository->findOneBy(['id' => $id])
        ]);
    }
}
