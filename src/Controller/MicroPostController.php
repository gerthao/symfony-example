<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostFormType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Security\Voter\MicroPostVoter;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    public function __construct(
        protected readonly MicroPostRepository $microPostRepository,
        protected readonly CommentRepository   $commentRepository,
        protected readonly LoggerInterface     $logger,
    )
    {
    }

    #[Route([
        '/micro-post',
        '/micro-post/page/{page<\d+>}'
    ], name: 'app_micro_post_index', defaults: ['page' => 1], methods: 'GET')]
    #[IsGranted(MicroPostVoter::VIEW, 'post')]
    public function index(int $page): Response
    {
        $limit     = 20;
        $pagedData = $this->microPostRepository->list($page, limit: $limit);
        $total     = $pagedData['total'];
        $data      = $pagedData['data'];

        if ($total > 0 && count($data) === 0) {
            throw $this->createNotFoundException('Invalid index');
        }

        return $this->render('micro_post/index.html.twig', [
            'page'       => $page,
            'total'      => $total,
            'hasPrev'    => $page > 1,
            'hasNext'    => ($total - ($page * $limit)) > 0,
            'microPosts' => $data
        ]);
    }

    #[Route('/micro-post/{id<\d+>}', name: 'app_micro_post_show', methods: 'GET')]
    #[IsGranted(MicroPostVoter::VIEW, 'post')]
    public function show(int $id): Response
    {
        $microPost = $this->microPostRepository->findWithComments($id);

        if (is_null($microPost)) {
            throw $this->createNotFoundException('Oops, that post could not be found.');
        }

        return $this->render('micro_post/show.html.twig', [
            'microPost' => $microPost
        ]);
    }

    #[Route('/micro-post/new', name: 'app_micro_post_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request): Response
    {
        $microPost = new MicroPost();
        $form      = $this->createForm(MicroPostFormType::class, $microPost)
            ->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var MicroPost $microPost */
            $microPost       = $form->getData();
            $currentDateTime = new DateTimeImmutable();

            $this->microPostRepository->add(
                $microPost
                    ->setCreated($currentDateTime)
                    ->setLastModified($currentDateTime)
                    ->setAuthor($this->getUser()),
                flush: true
            );

            $this->addFlash('success', 'Post has been successfully created.');
            return $this->redirectToRoute('app_micro_post_index');
        }

        return $this->render('micro_post/new.html.twig', ['form' => $form]);
    }

    #[Route('/micro-post/{id<\d+>}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPostVoter::EDIT, 'post')]
    public function edit(int $id, Request $request): Response
    {
        $microPost = $this->microPostRepository->find($id);

        if (is_null($microPost)) throw $this->createNotFoundException('Oops, that post could not be found.');

        $form = $this->createForm(MicroPostFormType::class, $microPost)
            ->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var MicroPost $microPost */
            $microPost       = $form->getData();
            $currentDateTime = new DateTimeImmutable();

            $this->microPostRepository->add(
                $microPost
                    ->setLastModified(new DateTimeImmutable())
                    ->setLastModified($currentDateTime),
                flush: true
            );

            $this->addFlash('success', 'Post has been successfully updated.');
            return $this->redirectToRoute('app_micro_post_show', ['id' => $id]);
        }

        return $this->render('micro_post/edit.html.twig', ['microPost' => $microPost, 'form' => $form]);
    }

    #[Route('/micro-post/{id<\d+>}/comment/new', name: 'app_micro_post_comment_new')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(int $id, Request $request): Response
    {
        $microPost = $this->microPostRepository->find($id);
        if (is_null($microPost)) throw $this->createNotFoundException('Oops, that post could not be found.');

        $comment = new Comment();
        $form    = $this->createForm(CommentType::class, $comment)
            ->add('save', SubmitType::class, ['label' => 'Save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var Comment $comment */
            $comment         = $form->getData();
            $currentDateTime = new DateTimeImmutable();

            $this->commentRepository->add(
                $comment
                    ->setPost($microPost)
                    ->setCreated($currentDateTime)
                    ->setLastModified($currentDateTime)
                    ->setAuthor($this->getUser()),
                flush: true
            );

            $this->addFlash('success', 'A comment has been successfully added.');
            return $this->redirectToRoute('app_micro_post_show', ['id' => $id]);
        }

        return $this->render('micro_post/comment/new.html.twig', ['microPost' => $microPost, 'form' => $form]);
    }
}
