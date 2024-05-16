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
    #[Route('', name: 'app_post', methods: ['POST'])]
    public function index(Request $request, PostRepository $repository): Response
    {
        // Get Data from POST Request data
        $data = json_decode($request->getContent(), true);
        // Filtri per la ricerca
        $criteria = [];
        if (isset($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $criteria[$key] = $value;
            }
        }
        $limit = intval($request->get('limit', 10));
        $page = intval($request->get('page', 1));
        // #####################

        // Get Posts
        $posts = $repository->getPosts($criteria, $limit, $page);
        $totalPosts = $repository->count($criteria);
        $totalPages = ceil($totalPosts / $limit);

        $results = [
            'items' => $posts,
            'page' => $page,
            'limit' => $limit,
            'totalElements' => $totalPosts,
            'totalPages' => $totalPages
        ];

        return $this->json($results, 200);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(int $id, PostRepository $repository): Response
    {
        // Get Post by ID
        $post = $repository->getPostByID($id);

        if (!$post) {
            return new Response('Post non trovato', 404);
        }
        // Return post as JSON
        return $this->json($post, 200);
    }

    #[Route('/create', name: 'app_post_create', methods: ['POST'])]
    public function create(Request $request, PostRepository $repository, EntityManagerInterface $entity, ValidatorInterface $validator): Response
    {

        return $this->json($repository->createPost($request, $entity, $validator));
    }

    #[Route('/{id}', name: 'app_post_update', methods: ['PUT'])]
    public function update(int $id, Request $request, PostRepository $repository, EntityManagerInterface $entity, ValidatorInterface $validator): Response
    {
        $post = $repository->getPostByID($id);
        if (!$post) {
            return new Response('Post non trovato', 404);
        }
        return $this->json($repository->updatePost($request, $entity, $validator, $post));
    }
}
