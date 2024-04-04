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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserController extends AbstractController
{
    /**
     * Retrieve users associated with the authenticated client.
     *  
     * This method retrieves a list of users associated with the authenticated client.
     * It utilizes caching to enhance performance by storing the serialized user data.
     * 
     * @param UserRepository $userRepository The repository for user entities.
     * @param ClientRepository $clientRepository The repository for client entities.
     * @param SerializerInterface $serializer The serializer used to serialize the user objects into JSON.
     * @param TagAwareCacheInterface $cache The cache service used to store and retrieve user data.
     * 
     * @return JsonResponse A JSON response containing the serialized user information.
     */
    #[Route('/api/client/users', name: 'usersByClient', methods:['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the list of users')]
    public function getUsersByClient(UserRepository $userRepository, ClientRepository $clientRepository, 
    SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        // The client is the authenticated user
        $client = $this->getUser();

        $idCache = "getUsersByClient-" . $client->getId();

        $jsonUsers = $cache->get($idCache, function (ItemInterface $item) use ($userRepository, $client, $serializer) {
            $item->tag("userCache-" . $client->getId());
            $users =  $userRepository->findBy(["client" => $client]);
            $context = SerializationContext::create()->setGroups(["getUsers"]);
            return $serializer->serialize($users, 'json', $context);
        });

        if ($jsonUsers) {
            return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Retrieve details of a user associated with the authenticated client.
     * 
     * This method retrieves the details of a specific user associated with the authenticated client.
     * 
     * @param User $user The user entity to retrieve details for.
     * @param UserRepository $userRepository The repository for user entities.
     * @param SerializerInterface $serializer The serializer used to serialize the user object into JSON.
     * 
     * @return JsonResponse A JSON response containing the serialized user information.
     */
    #[Route('/api/client/user/{id}', name: 'getUser', methods:['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the user\'s details')]
    public function getClientUser(User $user, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        // The client is the authenticated user
        $idClient = $this->getUser()->getId();

        $user = $userRepository->findBy(["id" => $user->getId(), "client" => $idClient]);
        
        if (empty($user)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }


    /**
     * Delete a user associated with the authenticated client.
     * 
     * This method deletes a specific user associated with the authenticated client.
     * 
     * @param User $user The user entity to be deleted.
     * @param UserRepository $userRepository The repository for user entities.
     * @param EntityManagerInterface $manager The entity manager used to manage entities.
     * @param TagAwareCacheInterface $cache The cache service used to invalidate cached user data.
     * 
     * @return JsonResponse A JSON response indicating the success of the deletion.
     */
    #[Route('/api/client/user/{id}', name: 'deleteUser', methods:['DELETE'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to delete a user')]
    public function deleteUser(User $user, UserRepository $userRepository, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        // The client is the authenticated user
        $idClient = $this->getUser()->getId();

        $user = $userRepository->findOneBy(["id" => $user->getId(), "client" => $idClient]);

        if (empty($user)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $cache->invalidateTags(["userCache-" . $idClient]);
        $manager->remove($user);
        $manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Add a new user associated with the authenticated client.
     * 
     * This method creates a new user associated with the authenticated client.
     * 
     * @param Request $request The HTTP request object containing user data.
     * @param EntityManagerInterface $manager The entity manager used to manage entities.
     * @param ClientRepository $clientRepository The repository for client entities.
     * @param SerializerInterface $serializer The serializer used to deserialize user data from JSON.
     * @param ValidatorInterface $validator The validator used to validate user data.
     * @param UrlGeneratorInterface $urlGenerator The URL generator used to generate resource URLs.
     * @param UserPasswordHasherInterface $passwordHasher The password hasher used to hash user passwords.
     * @param TagAwareCacheInterface $cache The cache service used to invalidate cached user data.
     * 
     * @return JsonResponse A JSON response indicating the success of the user creation along with the new user's details.
     * @throws CustomHttpException If there are validation errors, it throws a custom HTTP exception.
     */
    #[Route('/api/client/user', name: 'addUser', methods:['POST'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to create a user')]
    public function addUser(Request $request, EntityManagerInterface $manager, ClientRepository $clientRepository, 
    SerializerInterface $serializer, ValidatorInterface $validator, UrlGeneratorInterface $urlGenerator, 
    UserPasswordHasherInterface $passwordHasher, TagAwareCacheInterface $cache): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // The client is the authenticated user
        $idClient = $this->getUser()->getId();

        $user->setClient($clientRepository->find($idClient));

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            throw new CustomHttpException(400, $errors);
        }

        // Password hashing
        $hashedPassword = $passwordHasher->hashPassword(
            $user, 
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);

        $cache->invalidateTags(["userCache-" . $idClient]);
        $manager->persist($user);
        $manager->flush();

        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        $location = $urlGenerator->generate('getUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
