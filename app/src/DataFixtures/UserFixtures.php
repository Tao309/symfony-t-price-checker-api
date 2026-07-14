<?php

namespace App\DataFixtures;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire('%env(TOKEN_ADMIN)%')] private readonly string $adminToken,
        #[Autowire('%env(TOKEN_USER_1)%')] private readonly string $userTokenOne,
        #[Autowire('%env(TOKEN_USER_2)%')] private readonly string $userTokenTwo,
        #[Autowire('%env(TOKEN_USER_3)%')] private readonly string $userTokenThree,
        #[Autowire('%env(NAME_USER_1)%')] private readonly string $userNameOne,
        #[Autowire('%env(NAME_USER_2)%')] private readonly string $userNameTwo,
        #[Autowire('%env(NAME_USER_3)%')] private readonly string $userNameThree,
        #[Autowire('%env(ID_ADMIN)%')] private readonly string $userIdAdmin,
        #[Autowire('%env(ID_USER_1)%')] private readonly string $userIdOne,
        #[Autowire('%env(ID_USER_2)%')] private readonly string $userIdTwo,
        #[Autowire('%env(ID_USER_3)%')] private readonly string $userIdThree,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = null;

        if ($this->adminToken) {
            $user = new User();
            $user->setId($this->userIdAdmin);
            $user->setUsername('admin');
            $user->setEmail('admin@localhost.ru');
            $user->setPassword($this->passwordHasher->hashPassword($user, $this->adminToken));
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);

            $token = new AccessToken();
            $token->token = $this->adminToken;
            $token->userIdentifier = 'admin';
            $token->expiresAt = \DateTimeImmutable::createFromFormat('Y-m-d', '2050-01-01');
            $manager->persist($token);
        }

        if ($this->userTokenOne && $this->userNameOne) {
            $user = new User();
            $user->setId($this->userIdOne);
            $user->setUsername($this->userNameOne);
            $user->setEmail($this->userNameOne.'@localhost.ru');
            $user->setPassword($this->passwordHasher->hashPassword($user, $this->userTokenOne));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);

            $token = new AccessToken();
            $token->token = $this->userTokenOne;
            $token->userIdentifier = $this->userNameOne;
            $token->expiresAt = \DateTimeImmutable::createFromFormat('Y-m-d', '2050-01-01');
            $manager->persist($token);
        }

        if ($this->userTokenTwo && $this->userNameTwo) {
            $user = new User();
            $user->setId($this->userIdTwo);
            $user->setUsername($this->userNameTwo);
            $user->setEmail($this->userNameTwo.'@localhost.ru');
            $user->setPassword($this->passwordHasher->hashPassword($user, $this->userTokenTwo));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);

            $token = new AccessToken();
            $token->token = $this->userTokenTwo;
            $token->userIdentifier = $this->userNameTwo;
            $token->expiresAt = \DateTimeImmutable::createFromFormat('Y-m-d', '2050-01-01');
            $manager->persist($token);
        }

        if ($this->userTokenThree && $this->userNameThree) {
            $user = new User();
            $user->setId($this->userIdThree);
            $user->setUsername($this->userNameThree);
            $user->setEmail($this->userNameThree.'@localhost.ru');
            $user->setPassword($this->passwordHasher->hashPassword($user, $this->userTokenThree));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);

            $token = new AccessToken();
            $token->token = $this->userTokenThree;
            $token->userIdentifier = $this->userNameThree;
            $token->expiresAt = \DateTimeImmutable::createFromFormat('Y-m-d', '2050-01-01');
            $manager->persist($token);
        }

        if (!$user) {
            return;
        }

        $metadata = $this->em->getClassMetaData(User::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        $manager->flush();
    }
}
