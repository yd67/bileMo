<?php

namespace App\DataFixtures ;

use Faker ;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $clientsName = ['Orange', 'SFR', 'Bouygues','Free'];

        for ($i=0; $i < 10 ; $i++) { 

            $client = $this->getReference('client-'.$clientsName[$faker->numberBetween(0,3)]);
            $user = new User ;
            $user->setLastName($faker->lastName());
            $user->setFistName($faker->firstName());
            $user->setEmail($faker->email());
            
            $user->setClient($client) ;

            $manager->persist($user) ;
        }

        $manager->flush() ;
    }

    public function getDependencies()
    {
        return [
            ClientFixtures::class,
        ];
    }

}