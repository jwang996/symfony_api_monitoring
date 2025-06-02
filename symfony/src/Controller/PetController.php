<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PetDto;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PetController extends AbstractController
{
    #[Route('/pet/list', name: 'pet_list', methods: ['GET'])]
    public function listPet(): JsonResponse
    {
        $faker = Factory::create('de_DE');
        $pets = [];
        $speciesList = ['dog', 'cat', 'rabbit', 'hamster', 'parrot'];

        for ($i = 1; $i <= 5; $i++) {
            $dto = new PetDTO(
                $i,
                ucfirst($faker->word()),
                $speciesList[array_rand($speciesList)]
            );
            $pets[] = $dto->toArray();
        }

        return $this->json([
            'count' => count($pets),
            'pets'  => $pets,
        ]);
    }

    #[Route('/pet/inspect/{id}', name: 'pet_inspect', methods: ['GET'])]
    public function inspectPet(int $id): JsonResponse
    {
        if ($id >= 6) {
            throw $this->createNotFoundException(sprintf('Pet with ID "%d" not found.', $id));
        }

        $faker = Factory::create('de_DE');
        $speciesList = ['dog', 'cat', 'rabbit', 'hamster', 'parrot'];

        $dto = new PetDTO(
            $id,
            ucfirst($faker->word()),
            $speciesList[array_rand($speciesList)]
        );

        return $this->json($dto->toArray());
    }
}
