<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function getPostByID(int $id): Post | null
    {
        return $this->find($id);
    }

    public function getPosts($criteria, $limit, $page): array
    {
        $query = $this->createQueryBuilder('p');
        if (isset($criteria) && !empty($criteria)) {
            foreach ($criteria as $key => $value) {
                $query->andWhere("p.$key = :$key")
                    ->setParameter($key, $value);
            }
        }
        $query->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);
        return $query->getQuery()->getResult();
    }

    public function createPost(Request $request, EntityManagerInterface $entity, ValidatorInterface $validator): Response
    {
        $post = new Post();
        $requestData = json_decode($request->getContent(), true);
        $post->setTitle($requestData['title']);
        $post->setDescription($requestData['description']);
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setUpdatedAt(new \DateTime());

        $errors = $validator->validate($post);


        if (count($errors) > 0) {
            $errorsString = $this->formatErrors($errors);
            dd($errors);
            return new Response($errorsString, 400);
        } else {
            $entity->persist($post);
            $entity->flush();
            return new Response('Post creato con successo', 201);
        }
    }

    public function updatePost(Request $request, EntityManagerInterface $entity, ValidatorInterface $validator, Post $post): Response
    {
        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData)) {
            return new Response('Dati non validi', 400);
        }
        $post->setTitle($requestData['title']);
        $post->setDescription($requestData['description']);
        $post->setUpdatedAt(new \DateTime());

        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            $errorsString = $this->formatErrors($errors);
            return new Response($errorsString, 400);
        } else {
            $entity->flush();
            return new Response('Post aggiornato con successo', 200);
        }
    }

    public function formatErrors($errors): string
    {
        $errorsString = (string) $errors;
        return $errorsString;
    }
}
