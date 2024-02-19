<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        for ($i = 0; $i < 100; $i++) {
            $microPost = (new MicroPost())
                ->setTitle($faker->text(30))
                ->setText($faker->text(200))
                ->setCreated(DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade()));

            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
