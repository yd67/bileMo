<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use Faker ;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PhoneFixtures extends Fixture
{
    public $brand = ['Samsung', 'Apple','Huawei','google'] ;
    private $colors = ['blanc', 'noir', 'rouge', 'bleu', 'gris', 'jaune'];
    private $storages = ['64','128','256','512'] ;

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i=0; $i < 16 ; $i++) { 
           $phone = new Phone();
            $brand = $this->brand[rand(0, count($this->brand)-1)];
            if ($brand == 'Apple'){
                $phone->setModel('Iphone' . $faker->numberBetween(6, 14));
                $phone->setBrand('Apple');
            }elseif ($brand == 'Samsung'){
                $phone->setModel($brand . 'Galaxy  S' . $faker->numberBetween(7, 22));
                $phone->setBrand('Samsung');
            }elseif($brand == 'Huawei'){
                $phone->setModel($brand . ' Y' . $faker->numberBetween(4, 9));
                $phone->setBrand('Huawei');
            }else{
                $phone->setModel($brand . ' Pixel' . $faker->numberBetween(1, 7));
                $phone->setBrand('Google');
            }

            $color = $this->colors[$faker->numberBetween(0, 5)] ;
            $storage = $this->storages[$faker->numberBetween(0,3)];
            
            $phone->setColor($color);
            $phone->setStorage($storage) ;
            $phone->setPrice($faker->randomFloat(2, 450, 999));
            $phone->setImeiCode($faker->siret());

            $description = 'smartphone de couleur  ' . $color . ' avec une capacité de stokage de ' . $storage . 'GB . Le nouveau bijou de '. $brand.'un telephone au disigne  disigne unique . et de nombreux ffonctionnalités.' ;

            $phone->setDescription($description);
            
            $manager->persist($phone);
            
        }

        $manager->flush();
    }
}
