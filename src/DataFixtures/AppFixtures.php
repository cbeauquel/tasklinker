<?php

namespace App\DataFixtures;
use App\Factory\EmployeeFactory;
use App\Factory\ProjectFactory;
use App\Factory\StatusFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        EmployeeFactory::createMany(6);
        ProjectFactory::createMany(5);
        StatusFactory::createMany(10);
        TaskFactory::createMany(20);
    }
}
