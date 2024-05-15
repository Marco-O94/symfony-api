<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;

class PostFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $post = new Post();
        $post->setTitle('Post di prova 1');
        $post->setDescription('Contenuto di prova 1');
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setUpdatedAt(new \DateTime());

        $post2 = new Post();
        $post2->setTitle('Post di prova 2');
        $post2->setDescription('Contenuto di prova 2');
        $post2->setCreatedAt(new \DateTimeImmutable());
        $post2->setUpdatedAt(new \DateTime());


        $manager->persist($post);
        $manager->persist($post2);

        $manager->flush();
    }
}
