<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();
        /* @var User[] $users */
        $users = [];
        /* @var MicroPost[] $microPosts */
        $microPosts = [];

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setPassword($this->userPasswordHasher->hashPassword($user, $faker->password()));

            $users[] = $user;
        }

        for ($i = 0; $i < 100; $i++) {
            $dateTime = DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade());

            $microPost = (new MicroPost())
                ->setTitle($faker->text(64))
                ->setText($faker->text(128))
                ->setCreated($dateTime)
                ->setLastModified($dateTime)
                ->setAuthor($users[rand(0, sizeof($users) - 1)]);

            $microPosts[] = $microPost;
        }

        foreach ($users as $user) $manager->persist($user);
        foreach ($microPosts as $microPost) $manager->persist($microPost);

        $manager->flush();
    }
}
