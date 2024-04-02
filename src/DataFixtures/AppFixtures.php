<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // create 20 products
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('product '.$i);
            $product->setConstructor('constructor '.$i);
            $product->setDimensions('Height : 146.7 mm - Width : 71.5 mm - Depth : 7.65 mm');
            $product->setWeight(strVal(mt_rand(100, 300)) . 'Gr');
            $type = (mt_rand(0, 1));
            $product->setOs($type === 0 ? 'IOS': "Androïd");
            $product->setConnectivity("4G, 5G, Wifi, Bluetooth");
            $product->setCamera("Digital zoom up to 5x, Night mode, Smart HDR 4");
            $product->setApp($type === 0 ? "Siri, Notes, Maps, Apple Store, Face ID":"Play Store, YouTube, Gmail");
            $product->setStorage('256Go');
            $product->setRam('6Go');
            $product->setBattery('Lithium-ion Up to 19 hours. Fast-charge capable');
            $product->setGps('yes');
            $product->setAccessories('USB-C Charge Cable');
            $product->setProcessor($type === 0 ? 'A15 Bionic chip' : 'Snapdragon 8 Gen2');
            $product->setDesign('Glass back and aluminum');
            $product->setDescription(
                $type === 0 ?
                'Description Iphone'
                :'Description Androïd Phone'
            );
            $product->setPrice(mt_rand(200, 2000));
            
            $manager->persist($product);
        }

        $manager->flush();

        //Create 20 Clients
        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setName('ClientName'.$i);
            $client->setAddress('Client address'.$i);
            $client->setAddressComplement('Client address complement' .$i);
            $client->setPostalCode('73 200');
            $client->setCity('Mercury');
            $client->setCreationDate(new \DateTime);
            $client->setPassword($this->passwordHasher->hashPassword($client, 'passwordtest!'.$i));
            $client->setEmail('client'.$i.'@test.fr');
            $client->setRoles(["ROLE_USER"]);
            $this->addReference('client'.$i, $client);

            $manager->persist($client);
        }

        $manager->flush();

        //Create 40 Users (2 per client)
        for ($i = 0; $i < 20; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $user = new User();
                $user->setUsername('Client'.$i.'User'.$j);
                $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordtest!'.$i));
                $user->setName('Client'.$i.'Name'.$j);
                $user->setSurname(('Client'.$i.'Surname'.$j));
                $user->setEmail('client'.$i.'user'.$j.'@test.fr');
                $user->setCreationDate(new \DateTime);
                $client = $this->getReference('client'.$i);
                $user->setClient($client);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
