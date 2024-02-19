<?php

namespace App\Controller;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('hello')]
class HelloController extends AbstractController
{
    private array $messages;

    public function __construct(protected LoggerInterface $logger)
    {
        $this->messages = [
            ['message' => 'I am so smart, S-M-R-T!', 'created' => new DateTimeImmutable('2024-01-01')],
            ['message' => 'D\'oh!', 'created' => new DateTimeImmutable('2023-06-15')],
            ['message' => 'Aie, no es bueno!', 'created' => new DateTimeImmutable('2020-02-05')]
        ];
    }

    #[Route('', name: 'app_hello_index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('hello/index.html.twig', [
            'messages' => $this->messages,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_hello_show', methods: 'GET')]
    public function show(int $id): Response
    {
        return $this->render('hello/show.html.twig', [
            'message' => $this->messages[$id]
        ]);
    }
}