<?php

namespace App\DataFixtures;
use App\Entity\Books;
use App\Entity\Cinema;
use App\Entity\Games;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    private $slug;

    public function __construct(SlugifyInterface $slugify)
    {
        $this->faker = Factory::create();
        $this->slug = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCinema($manager);
        $this->loadBooks($manager);
        $this->loadGames($manager);
    }

    public function loadCinema(ObjectManager $manager)
    {
        for ($i = 1; $i < 20; $i++) {
            $cinema = new Cinema();
            $cinema ->setTitle($this->faker->text(100));
            $cinema ->setSlug($this->slug->slugify($cinema->getTitle()));
            $cinema ->setBody($this->faker->text(1000));
            $cinema ->setCreatedAt($this->faker->dateTime);
            $cinema ->setImg('no_image.png');

            $manager->persist($cinema);
        }
        $manager->flush();
    }
    public function loadGames(ObjectManager $manager)
    {
        for ($i = 1; $i < 20; $i++) {
            $games = new Games();
            $games ->setTitle($this->faker->text(100));
            $games ->setSlug($this->slug->slugify($games->getTitle()));
            $games ->setBody($this->faker->text(1000));
            $games ->setCreatedAt($this->faker->dateTime);
            $games ->setImg('no_image.png');

            $manager->persist($games);
        }
        $manager->flush();
    }
    public function loadBooks(ObjectManager $manager)
    {
        for ($i = 1; $i < 20; $i++) {
            $books = new Books();
            $books ->setTitle($this->faker->text(100));
            $books ->setSlug($this->slug->slugify($books->getTitle()));
            $books->setBody($this->faker->text(1000));
            $books ->setCreatedAt($this->faker->dateTime);
            $books->setImg('no_image.png');

            $manager->persist($books);
        }
        $manager->flush();
    }
}
