<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture
{
    private $passwordHasher ;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->passwordHasher = $userPasswordHasher ;
    }

    public function load(ObjectManager $manager): void
    {
        $clientsName = ['Orange', 'SFR', 'Bouygues','Free'];

        foreach ($clientsName as $c) {
           $client = new Client ;

           $email = $c.'@'.$c.'.fr';

           $client->setName($c)
           ->setEmail($email)
           ->setPassword(
            $this->passwordHasher->hashPassword(
                    $client,
                    'test98'
                )
            )
            ->setRoles(['ROLE_CLIENT']);
            ;
            $manager->persist($client);

            $this->addReference('client-'.$c, $client);
        }
       
        $manager->flush();
    }
}