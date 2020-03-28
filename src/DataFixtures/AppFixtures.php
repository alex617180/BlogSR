<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Post;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $faker;

    private $slug;

    public function __construct(SluggerInterface $slugger)
    {
        $this->faker = Factory::create();
        $this->slug = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        $this->loadPosts($manager);
    }
    public function loadPosts(ObjectManager $manager)
    {
        for ($i = 1; $i < 20; $i++) {
            $post = new Post();
            $post->setTitle($this->faker->text(100));
            $post->setSlug($this->slug->slug($post->getTitle())->lower());
            $post->setBody($this->faker->text(1000));
            $post->setCreatedAt($this->faker->dateTime);

            $manager->persist($post);
        }
        $manager->flush();
    }
}
