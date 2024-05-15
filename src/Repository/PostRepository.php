<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function getPosts(): array
    {
        return $this->findAll();
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

    public function formatErrors($errors): string
    {
        $errorsString = (string) $errors;
        return $errorsString;
    }
}
