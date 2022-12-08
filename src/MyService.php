<?php

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class MyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $confKey
    ){}

    public function doSomething(): void
    {
        // do something
    }
}