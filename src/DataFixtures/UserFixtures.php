<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Symfony\Component\Debug\Tests\testHeader;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$name, $last_name, $email, $password, $api_key, $roles])
        {
            $user = new User();
            $user->setName($name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);

            $manager->persist($user);
        }


        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ["Hugo", "Doe", "hugo@gmail.com", "password", "hjd8dehdh", ["ROLE_ADMIN"]],
            ["Clement", "Doe", "clement@gmail.com", "password", null, ["ROLE_USER"]],
            ["Leo", "Doe", "leo@gmail.com", "password", null, ["ROLE_USER"]],
        ];
    }
}
