<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PersonDto;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    #[Route('/person/list', name: 'person_list', methods: ['GET'])]
    public function listPerson(): JsonResponse
    {
        $faker = Factory::create('de_DE');

        $people = [];

        for ($i = 1; $i <= 5; $i++) {
            $dto = new PersonDTO(
                $i,
                $faker->firstName(),
                $faker->lastName(),
                $faker->email()
            );
            $people[] = $dto->toArray();
        }

        return $this->json([
            'count'  => count($people),
            'people' => $people,
        ]);
    }

    #[Route('/person/inspect/{id}', name: 'person_inspect', methods: ['GET'])]
    public function inspectPerson(int $id): JsonResponse
    {
        if ($id >= 6) {
            throw $this->createNotFoundException(sprintf('Person with ID "%d" not found.', $id));
        }

        $faker = Factory::create('de_DE');

        $dto = new PersonDTO(
            $id,
            $faker->firstName(),
            $faker->lastName(),
            $faker->email()
        );

        return $this->json($dto->toArray());
    }

}
