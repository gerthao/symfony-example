<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MicroPostController extends AbstractController
{
    public function __construct(protected readonly MicroPostRepository $microPostRepository)
    {
    }

    #[Route(['/micro-post', '/micro-post/page/{page<\d+>}'], name: 'app_micro_post_index', defaults: ['page' => 1], methods: 'GET')]
    public function index(int $page): Response
    {
        $limit     = 20;
        $pagedData = $this->microPostRepository->list($page, limit: $limit);
        $total     = $pagedData['total'];
        $data      = $pagedData['data'];

        if ($total > 0 && count($data) === 0)
            throw $this->createNotFoundException('Invalid index');

        return $this->render('micro_post/index.html.twig', [
            'page'       => $page,
            'total'      => $total,
            'hasPrev'    => $page > 1,
            'hasNext'    => ($total - ($page * $limit)) > 0,
            'microPosts' => $data
        ]);
    }

    #[Route('/micro-post/{id<\d+>}', name: 'app_micro_post_show', methods: 'GET')]
    public function show(int $id): Response
    {
        $microPost = $this->microPostRepository->findOneBy(['id' => $id]);

        if (is_null($microPost))
            throw $this->createNotFoundException('Could not find post');

        return $this->render('micro_post/show.html.twig', [
            'microPost' => $microPost
        ]);
    }

    #[Route('/micro-post/new', name: 'app_micro_post_new')]
    public function create(Request $request): Response
    {
        $microPost = new MicroPost();
        $form      = $this->createFormBuilder($microPost)
            ->setMethod('POST')
            ->add('title', TextType::class)
            ->add('text', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Post'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost = $form->getData();
            $microPost->setCreated(new DateTimeImmutable());
            $this->microPostRepository->add($microPost, flush: true);

            $this->addFlash('success', 'Post has been successfully created.');
            return $this->redirectToRoute('app_micro_post_index');
        }

        return $this->render('micro_post/new.html.twig', ['form' => $form->createView()]);
    }
}
