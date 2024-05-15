<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post', methods: ['GET'])]
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->getPosts();
        if (!$posts) {
            return new Response('No posts found', 404);
        }
        return $this->json($posts, 200);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(int $id, PostRepository $repository): Response
    {
        // Get Post by ID
        $post = $repository->getPostByID($id);

        if (!$post) {
            return new Response('Post not found', 404);
        }
        // Return post as JSON
        return $this->json($post, 200);
    }

    #[Route('/create', name: 'app_post_create', methods: ['POST'])]
    public function create(Request $request, PostRepository $repository, EntityManagerInterface $entity, ValidatorInterface $validator): Response
    {

        return $this->json($repository->createPost($request, $entity, $validator));
    }
}
