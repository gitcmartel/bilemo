<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\CustomHttpException;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/api/users/{clientId}', name: 'users_by_client', methods:['GET'])]
    public function getUsersByClient($clientId, UserRepository $userRepository, ClientRepository $clientRepository, 
    SerializerInterface $serializer): JsonResponse
    {
        $client = $clientRepository->find($clientId);
        $users = $userRepository->findBy(["client" => $client]);

        if ($users) {
            $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'getUsers']);
            return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

    }

    #[Route('/api/user/{id}', name: 'user', methods:['GET'])]
    public function getClientUser(User $user, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/user/{id}', name: 'delete_user', methods:['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $manager): JsonResponse
    {
        $manager->remove($user);
        $manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/user', name: 'add_user', methods:['POST'])]
    public function addUser(Request $request, EntityManagerInterface $manager, ClientRepository $clientRepository, 
    SerializerInterface $serializer, ValidatorInterface $validator, UrlGeneratorInterface $urlGenerator, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $content = $request->toArray();
        $idClient = $content['idClient'] ?? -1;

        $user->setClient($clientRepository->find($idClient));

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            //return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
            throw new CustomHttpException(400, $errors);
        }

        // Password hashing
        $hashedPassword = $passwordHasher->hashPassword(
            $user, 
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);

        $location = $urlGenerator->generate('user', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
